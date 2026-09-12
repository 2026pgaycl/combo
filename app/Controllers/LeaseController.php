<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csv;
use App\Core\Database;
use App\Core\Request;
use App\Core\Upload;
use App\Models\Document;
use App\Models\Lease;
use App\Models\LeaseDocument;
use App\Models\NotificationLog;
use App\Models\Tenant;
use App\Models\Unit;

class LeaseController extends Controller
{
    private const DOC_TYPES = ['contract', 'addendum', 'kyc', 'insurance'];

    private const DOC_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

    private const DOC_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    public function index(): void
    {
        $this->view('leases.index', [
            'leases' => Lease::withDetails(),
        ]);
    }

    public function create(): void
    {
        $this->view('leases.create', [
            'vacantUnits' => Unit::vacantUnits(),
            'tenants' => Tenant::all('company_name'),
        ]);
    }

    public function export(): void
    {
        Csv::export(
            'leases.csv',
            ['id', 'unit_id', 'tenant_id', 'start_date', 'end_date', 'monthly_rent', 'deposit', 'escalation_clause', 'status'],
            Lease::all('id DESC')
        );
    }

    /**
     * Bulk-creates leases from a CSV in the same shape export() produces.
     * unit_id/tenant_id must reference existing rows; a row importing as
     * status=active marks that unit occupied, matching store() above. No
     * notification is queued for imported leases (bulk/historical loads,
     * not a live event).
     */
    public function import(): void
    {
        $this->verifyCsrf();

        $imported = 0;
        $skipped = 0;

        foreach (Csv::parseUpload(Request::file('csv')) as $row) {
            $unitId = (int) ($row['unit_id'] ?? 0);
            $tenantId = (int) ($row['tenant_id'] ?? 0);
            if ($unitId === 0 || $tenantId === 0 || !Unit::find($unitId) || !Tenant::find($tenantId)) {
                $skipped++;
                continue;
            }

            $status = $row['status'] ?: 'pending';

            Lease::create([
                'unit_id' => $unitId,
                'tenant_id' => $tenantId,
                'start_date' => $row['start_date'] ?: null,
                'end_date' => $row['end_date'] ?: null,
                'monthly_rent' => (float) ($row['monthly_rent'] ?? 0),
                'deposit' => (float) ($row['deposit'] ?? 0),
                'escalation_clause' => $row['escalation_clause'] ?: null,
                'status' => $status,
            ]);

            if ($status === 'active') {
                Unit::update($unitId, ['status' => 'occupied']);
            }

            $imported++;
        }

        $this->flash('success', "Imported {$imported} lease(s)." . ($skipped ? " Skipped {$skipped} row(s) with an unknown unit_id or tenant_id." : ''));
        $this->redirect('/leases');
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $unitId = (int) Request::input('unit_id');
        $tenantId = (int) Request::input('tenant_id');
        $startDate = Request::input('start_date');
        $endDate = Request::input('end_date');
        $monthlyRent = (float) Request::input('monthly_rent', 0);

        Database::beginTransaction();
        try {
            $leaseId = Lease::create([
                'unit_id' => $unitId,
                'tenant_id' => $tenantId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'monthly_rent' => $monthlyRent,
                'deposit' => (float) Request::input('deposit', 0),
                'escalation_clause' => Request::input('escalation_clause') ?: null,
                'status' => 'active',
            ]);

            Unit::update($unitId, ['status' => 'occupied']);

            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            error_log('[Combo] Lease creation failed: ' . $e->getMessage());
            $this->flash('error', 'Could not create the lease. Please try again.');
            $this->redirect('/leases/create');
        }

        $tenant = Tenant::find($tenantId);
        $unit = Unit::findWithDetails($unitId);
        if ($tenant && $unit) {
            NotificationLog::queue(
                null,
                'email',
                "Lease confirmed — {$unit['unit_number']}",
                sprintf(
                    "Dear %s,\n\nYour lease for %s — %s is now active (%s to %s) at RM %s/month.\n\nRecipient: %s <%s>",
                    $tenant['contact_name'],
                    $unit['building_name'],
                    $unit['unit_number'],
                    $startDate,
                    $endDate,
                    number_format($monthlyRent, 2),
                    $tenant['company_name'],
                    $tenant['contact_email'] ?? 'no email on file'
                )
            );
        }

        $this->flash('success', 'Lease created and unit marked occupied.');
        $this->redirect("/leases/{$leaseId}");
    }

    public function show(string $id): void
    {
        $lease = Lease::findWithDetails((int) $id);
        if (!$lease) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }
        $this->view('leases.show', [
            'lease' => $lease,
            'documents' => Document::forRelated('lease', (int) $id),
            'leaseDocuments' => LeaseDocument::forLease((int) $id),
            'docTypes' => self::DOC_TYPES,
        ]);
    }

    public function storeDocument(string $id): void
    {
        $this->verifyCsrf();

        $docType = (string) Request::input('doc_type', 'contract');
        if (!in_array($docType, self::DOC_TYPES, true)) {
            $docType = 'contract';
        }

        $filePath = Upload::store(
            Request::file('document'),
            'lease_documents',
            self::DOC_EXTENSIONS,
            self::DOC_MIME_TYPES,
            $error
        );

        if ($filePath === null) {
            $this->flash('error', $error);
            $this->redirect("/leases/{$id}");
        }

        LeaseDocument::create([
            'lease_id' => (int) $id,
            'doc_type' => $docType,
            'file_path' => $filePath,
            'expiry_date' => Request::input('expiry_date') ?: null,
        ]);

        $this->flash('success', 'Document added.');
        $this->redirect("/leases/{$id}");
    }

    public function destroyDocument(string $docId): void
    {
        $this->verifyCsrf();

        $document = LeaseDocument::find((int) $docId);
        if (!$document) {
            $this->redirect('/leases');
        }

        Upload::delete($document['file_path']);
        LeaseDocument::delete((int) $docId);

        $this->flash('success', 'Document deleted.');
        $this->redirect("/leases/{$document['lease_id']}");
    }

    /** Terminates a lease early and frees up the unit. */
    public function terminate(string $id): void
    {
        $this->verifyCsrf();

        $lease = Lease::find((int) $id);
        if ($lease) {
            Database::beginTransaction();
            try {
                Lease::update((int) $id, ['status' => 'terminated']);
                Unit::update((int) $lease['unit_id'], ['status' => 'vacant']);
                Database::commit();
                $this->flash('success', 'Lease terminated and unit marked vacant.');

                $tenant = Tenant::find((int) $lease['tenant_id']);
                $unit = Unit::findWithDetails((int) $lease['unit_id']);
                if ($tenant && $unit) {
                    NotificationLog::queue(
                        null,
                        'email',
                        "Lease terminated — {$unit['unit_number']}",
                        sprintf(
                            "Dear %s,\n\nYour lease for %s — %s has been terminated effective today.\n\nRecipient: %s <%s>",
                            $tenant['contact_name'],
                            $unit['building_name'],
                            $unit['unit_number'],
                            $tenant['company_name'],
                            $tenant['contact_email'] ?? 'no email on file'
                        )
                    );
                }
            } catch (\Throwable $e) {
                Database::rollBack();
                error_log('[Combo] Lease termination failed: ' . $e->getMessage());
                $this->flash('error', 'Could not terminate the lease.');
            }
        }

        $this->redirect('/leases');
    }
}
