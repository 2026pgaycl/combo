<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Upload;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Lease;
use App\Models\LeaseDocument;
use App\Models\MaintenanceTicket;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\TicketUpdate;
use App\Models\User;

/**
 * Self-service area for the "tenant" role — everything here is scoped to the
 * logged-in tenant's own records (via *_forTenant model methods) so one
 * tenant can never view or act on another's data, even by guessing an id.
 */
class PortalController extends Controller
{
    private const PHOTO_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    private const PHOTO_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public function index(): void
    {
        $user = Auth::user();
        $tenantId = (int) $user['tenant_id'];

        $invoices = Invoice::forTenant($tenantId);
        $tickets = MaintenanceTicket::forTenant($tenantId);

        $this->view('portal.index', [
            'tenantName' => $user['name'],
            'leases' => Lease::forTenant($tenantId),
            'openInvoices' => array_values(array_filter($invoices, fn($i) => $i['status'] !== 'paid')),
            'openTickets' => array_values(array_filter($tickets, fn($t) => !in_array($t['status'], ['resolved', 'closed'], true))),
        ]);
    }

    public function invoices(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];

        $this->view('portal.invoices', [
            'invoices' => Invoice::forTenant($tenantId),
        ]);
    }

    public function showInvoice(string $id): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];
        $invoice = Invoice::findForTenant((int) $id, $tenantId);
        if (!$invoice) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $this->view('portal.invoice_show', [
            'invoice' => $invoice,
            'items' => InvoiceItem::forInvoice((int) $id),
            'payments' => Payment::forInvoice((int) $id),
            'amountPaid' => Payment::totalPaid((int) $id),
        ]);
    }

    public function maintenance(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];

        $this->view('portal.maintenance', [
            'tickets' => MaintenanceTicket::forTenant($tenantId),
        ]);
    }

    public function createMaintenance(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];

        $this->view('portal.maintenance_create', [
            'units' => Lease::activeUnitsForTenant($tenantId),
        ]);
    }

    public function storeMaintenance(): void
    {
        $this->verifyCsrf();

        $tenantId = (int) Auth::user()['tenant_id'];
        $unitId = (int) Request::input('unit_id');
        $description = trim((string) Request::input('description', ''));
        $allowedUnitIds = array_column(Lease::activeUnitsForTenant($tenantId), 'id');

        if ($unitId === 0 || $description === '' || !in_array($unitId, $allowedUnitIds, true)) {
            $this->flash('error', 'Select one of your units and describe the issue.');
            $this->redirect('/portal/maintenance/create');
        }

        $photoPath = null;
        $photoFile = Request::file('photo');
        if ($photoFile && $photoFile['error'] !== UPLOAD_ERR_NO_FILE) {
            $photoPath = Upload::store($photoFile, 'maintenance', self::PHOTO_EXTENSIONS, self::PHOTO_MIME_TYPES, $error);
            if ($photoPath === null) {
                $this->flash('error', $error);
                $this->redirect('/portal/maintenance/create');
            }
        }

        $ticketId = MaintenanceTicket::create([
            'unit_id' => $unitId,
            'tenant_id' => $tenantId,
            'category' => Request::input('category', 'general'),
            'description' => $description,
            'photo_path' => $photoPath,
            'status' => 'open',
        ]);

        $this->flash('success', 'Ticket reported.');
        $this->redirect("/portal/maintenance/{$ticketId}");
    }

    public function showMaintenance(string $id): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];
        $ticket = MaintenanceTicket::findForTenant((int) $id, $tenantId);
        if (!$ticket) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $this->view('portal.maintenance_show', [
            'ticket' => $ticket,
            'updates' => TicketUpdate::forTicket((int) $id),
        ]);
    }

    public function storeMaintenanceNote(string $id): void
    {
        $this->verifyCsrf();

        $tenantId = (int) Auth::user()['tenant_id'];
        if (!MaintenanceTicket::findForTenant((int) $id, $tenantId)) {
            $this->redirect('/portal/maintenance');
        }

        $note = trim((string) Request::input('note', ''));
        if ($note !== '') {
            TicketUpdate::create([
                'ticket_id' => (int) $id,
                'user_id' => Auth::id(),
                'note' => $note,
            ]);
        }

        $this->redirect("/portal/maintenance/{$id}");
    }

    /** Tenant-level documents plus every lease document / generic document on each of the tenant's leases. */
    public function documents(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];
        $leases = Lease::forTenant($tenantId);

        $leaseSections = [];
        foreach ($leases as $lease) {
            $docs = [];
            foreach (LeaseDocument::forLease((int) $lease['id']) as $d) {
                $docs[] = [
                    'label' => $d['doc_type'] === 'kyc' ? 'KYC' : ucfirst($d['doc_type']),
                    'file_path' => $d['file_path'],
                    'expiry_date' => $d['expiry_date'],
                    'created_at' => $d['uploaded_at'],
                ];
            }
            foreach (Document::forRelated('lease', (int) $lease['id']) as $d) {
                $docs[] = [
                    'label' => $d['title'],
                    'file_path' => $d['file_path'],
                    'expiry_date' => $d['expiry_date'],
                    'created_at' => $d['created_at'],
                ];
            }
            $leaseSections[] = [
                'label' => $lease['building_name'] . ' — ' . $lease['unit_number'],
                'documents' => $docs,
            ];
        }

        $this->view('portal.documents', [
            'tenantDocuments' => Document::forRelated('tenant', $tenantId),
            'leaseSections' => $leaseSections,
        ]);
    }

    public function profile(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];

        $this->view('portal.profile', [
            'tenant' => Tenant::find($tenantId),
        ]);
    }

    public function updateProfile(): void
    {
        $this->verifyCsrf();

        $tenantId = (int) Auth::user()['tenant_id'];
        $contactName = trim((string) Request::input('contact_name', ''));

        Tenant::update($tenantId, [
            'contact_name' => $contactName,
            'contact_phone' => Request::input('contact_phone') ?: null,
            'billing_address' => Request::input('billing_address') ?: null,
        ]);

        // Keep the login's display name in sync with the tenant's contact name.
        $userId = Auth::id();
        if ($userId) {
            User::update($userId, ['name' => $contactName]);
            $_SESSION['user']['name'] = $contactName;
        }

        $this->flash('success', 'Profile updated.');
        $this->redirect('/portal/profile');
    }
}
