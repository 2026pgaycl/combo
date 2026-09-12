<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Upload;
use App\Models\MaintenanceTicket;
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

        $ticketId = MaintenanceTicket::create([
            'unit_id' => $unitId,
            'tenant_id' => $lease['tenant_id'] ?? null,
            'category' => Request::input('category', 'general'),
            'description' => $description,
            'photo_path' => $photoPath,
            'status' => 'open',
        ]);

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

        $assignedTo = Request::input('assigned_to');
        $cost = Request::input('cost');

        $data = [
            'status' => Request::input('status', 'open'),
            'assigned_to' => ($assignedTo !== '' && $assignedTo !== null) ? (int) $assignedTo : null,
            'cost' => ($cost !== '' && $cost !== null) ? (float) $cost : null,
        ];

        $photoFile = Request::file('photo');
        if ($photoFile && $photoFile['error'] !== UPLOAD_ERR_NO_FILE) {
            $photoPath = Upload::store($photoFile, 'maintenance', self::PHOTO_EXTENSIONS, self::PHOTO_MIME_TYPES, $error);
            if ($photoPath === null) {
                $this->flash('error', $error);
                $this->redirect("/maintenance/{$id}");
            }

            $existing = MaintenanceTicket::find((int) $id);
            if (!empty($existing['photo_path'])) {
                Upload::delete($existing['photo_path']);
            }

            $data['photo_path'] = $photoPath;
        }

        MaintenanceTicket::update((int) $id, $data);

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
