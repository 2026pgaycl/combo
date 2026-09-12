<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csv;
use App\Core\Database;
use App\Core\Request;
use App\Core\Upload;
use App\Models\MaintenanceTicket;
use App\Models\NotificationLog;
use App\Models\Tenant;
use App\Models\TicketUpdate;
use App\Models\Unit;
use App\Models\User;

class MaintenanceController extends Controller
{
    private const PHOTO_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    private const PHOTO_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public function index(): void
    {
        $status = Request::input('status');
        $tickets = MaintenanceTicket::withDetails();
        if ($status) {
            $tickets = array_values(array_filter($tickets, fn($t) => $t['status'] === $status));
        }

        $this->view('maintenance.index', [
            'tickets' => $tickets,
            'selectedStatus' => $status,
        ]);
    }

    public function create(): void
    {
        $unitId = Request::input('unit_id');

        $this->view('maintenance.create', [
            'units' => Unit::withDetails(),
            'selectedUnitId' => $unitId ? (int) $unitId : null,
        ]);
    }

    public function export(): void
    {
        Csv::export(
            'maintenance_tickets.csv',
            ['id', 'unit_id', 'tenant_id', 'category', 'description', 'status', 'assigned_to', 'cost'],
            MaintenanceTicket::all('id DESC')
        );
    }

    /**
     * Bulk-creates tickets from a CSV in the same shape export() produces.
     * unit_id must reference an existing unit; tenant_id/assigned_to are
     * optional. No confirmation/assignment notification is queued, since
     * imports are bulk/historical data, not a live event.
     */
    public function import(): void
    {
        $this->verifyCsrf();

        $imported = 0;
        $skipped = 0;

        foreach (Csv::parseUpload(Request::file('csv')) as $row) {
            $unitId = (int) ($row['unit_id'] ?? 0);
            $description = trim((string) ($row['description'] ?? ''));
            if ($unitId === 0 || $description === '' || !Unit::find($unitId)) {
                $skipped++;
                continue;
            }

            $tenantId = (int) ($row['tenant_id'] ?? 0);
            $assignedTo = (int) ($row['assigned_to'] ?? 0);
            $cost = $row['cost'] ?? '';

            MaintenanceTicket::create([
                'unit_id' => $unitId,
                'tenant_id' => $tenantId > 0 ? $tenantId : null,
                'category' => $row['category'] ?: 'general',
                'description' => $description,
                'status' => $row['status'] ?: 'open',
                'assigned_to' => $assignedTo > 0 ? $assignedTo : null,
                'cost' => $cost !== '' ? (float) $cost : null,
            ]);
            $imported++;
        }

        $this->flash('success', "Imported {$imported} ticket(s)." . ($skipped ? " Skipped {$skipped} row(s) with a missing description or unknown unit_id." : ''));
        $this->redirect('/maintenance');
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $unitId = (int) Request::input('unit_id');
        $description = trim((string) Request::input('description', ''));

        if ($unitId === 0 || $description === '') {
            $this->flash('error', 'Select a unit and describe the issue.');
            $this->redirect('/maintenance/create');
        }

        $photoPath = null;
        $photoFile = Request::file('photo');
        if ($photoFile && $photoFile['error'] !== UPLOAD_ERR_NO_FILE) {
            $photoPath = Upload::store($photoFile, 'maintenance', self::PHOTO_EXTENSIONS, self::PHOTO_MIME_TYPES, $error);
            if ($photoPath === null) {
                $this->flash('error', $error);
                $this->redirect('/maintenance/create');
            }
        }

        $lease = Database::selectOne(
            'SELECT tenant_id FROM leases WHERE unit_id = ? AND status = "active"',
            [$unitId]
        );

        $category = Request::input('category', 'general');

        $ticketId = MaintenanceTicket::create([
            'unit_id' => $unitId,
            'tenant_id' => $lease['tenant_id'] ?? null,
            'category' => $category,
            'description' => $description,
            'photo_path' => $photoPath,
            'status' => 'open',
        ]);

        if (!empty($lease['tenant_id'])) {
            $tenant = Tenant::find((int) $lease['tenant_id']);
            if ($tenant) {
                NotificationLog::queue(
                    null,
                    'email',
                    'We received your maintenance request',
                    sprintf(
                        "Dear %s,\n\nWe've logged your %s request: \"%s\". We'll follow up soon.\n\nRecipient: %s <%s>",
                        $tenant['contact_name'],
                        $category,
                        $description,
                        $tenant['company_name'],
                        $tenant['contact_email'] ?? 'no email on file'
                    )
                );
            }
        }

        $this->flash('success', 'Ticket reported.');
        $this->redirect("/maintenance/{$ticketId}");
    }

    public function show(string $id): void
    {
        $ticket = MaintenanceTicket::findWithDetails((int) $id);
        if (!$ticket) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $this->view('maintenance.show', [
            'ticket' => $ticket,
            'updates' => TicketUpdate::forTicket((int) $id),
            'staff' => User::maintenanceStaff(),
        ]);
    }

    public function updateStatus(string $id): void
    {
        $this->verifyCsrf();

        $existing = MaintenanceTicket::find((int) $id);

        $assignedTo = Request::input('assigned_to');
        $cost = Request::input('cost');
        $newStatus = Request::input('status', 'open');
        $newAssignedTo = ($assignedTo !== '' && $assignedTo !== null) ? (int) $assignedTo : null;

        $data = [
            'status' => $newStatus,
            'assigned_to' => $newAssignedTo,
            'cost' => ($cost !== '' && $cost !== null) ? (float) $cost : null,
        ];

        $photoFile = Request::file('photo');
        if ($photoFile && $photoFile['error'] !== UPLOAD_ERR_NO_FILE) {
            $photoPath = Upload::store($photoFile, 'maintenance', self::PHOTO_EXTENSIONS, self::PHOTO_MIME_TYPES, $error);
            if ($photoPath === null) {
                $this->flash('error', $error);
                $this->redirect("/maintenance/{$id}");
            }

            if (!empty($existing['photo_path'])) {
                Upload::delete($existing['photo_path']);
            }

            $data['photo_path'] = $photoPath;
        }

        MaintenanceTicket::update((int) $id, $data);

        if ($newAssignedTo !== null && $newAssignedTo !== (int) ($existing['assigned_to'] ?? 0)) {
            NotificationLog::queue(
                $newAssignedTo,
                'email',
                "Maintenance ticket #{$id} assigned to you",
                "You have been assigned maintenance ticket #{$id}."
            );
        }

        if ($newStatus !== $existing['status'] && in_array($newStatus, ['resolved', 'closed'], true) && !empty($existing['tenant_id'])) {
            $tenant = Tenant::find((int) $existing['tenant_id']);
            if ($tenant) {
                NotificationLog::queue(
                    null,
                    'email',
                    "Your maintenance ticket #{$id} is {$newStatus}",
                    sprintf(
                        "Dear %s,\n\nYour maintenance ticket #%s has been marked %s.\n\nRecipient: %s <%s>",
                        $tenant['contact_name'],
                        $id,
                        $newStatus,
                        $tenant['company_name'],
                        $tenant['contact_email'] ?? 'no email on file'
                    )
                );
            }
        }

        $this->flash('success', 'Ticket updated.');
        $this->redirect("/maintenance/{$id}");
    }

    public function storeNote(string $id): void
    {
        $this->verifyCsrf();

        $note = trim((string) Request::input('note', ''));
        if ($note === '') {
            $this->redirect("/maintenance/{$id}");
        }

        TicketUpdate::create([
            'ticket_id' => (int) $id,
            'user_id' => Auth::id(),
            'note' => $note,
        ]);

        $this->redirect("/maintenance/{$id}");
    }
}
