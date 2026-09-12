<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Upload;
use App\Models\Document;
use App\Models\Lease;
use App\Models\LeaseDocument;
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

    public function store(): void
    {
        $this->verifyCsrf();

        $unitId = (int) Request::input('unit_id');
        $tenantId = (int) Request::input('tenant_id');

        Database::beginTransaction();
        try {
            $leaseId = Lease::create([
                'unit_id' => $unitId,
                'tenant_id' => $tenantId,
                'start_date' => Request::input('start_date'),
                'end_date' => Request::input('end_date'),
                'monthly_rent' => (float) Request::input('monthly_rent', 0),
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
            } catch (\Throwable $e) {
                Database::rollBack();
                error_log('[Combo] Lease termination failed: ' . $e->getMessage());
                $this->flash('error', 'Could not terminate the lease.');
            }
        }

        $this->redirect('/leases');
    }
}
