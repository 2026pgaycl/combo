<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csv;
use App\Core\Request;
use App\Models\NotificationLog;
use App\Models\User;

class NotificationController extends Controller
{
    public function index(): void
    {
        $status = Request::input('status');
        $notifications = NotificationLog::withDetails();
        if ($status) {
            $notifications = array_values(array_filter($notifications, fn($n) => $n['status'] === $status));
        }

        $this->view('notifications.index', [
            'notifications' => $notifications,
            'selectedStatus' => $status,
        ]);
    }

    public function create(): void
    {
        $this->view('notifications.create', [
            'users' => User::all('name'),
        ]);
    }

    public function export(): void
    {
        Csv::export(
            'notifications.csv',
            ['id', 'user_id', 'channel', 'subject', 'body', 'status', 'sent_at', 'created_at'],
            NotificationLog::all('id DESC')
        );
    }

    /** Bulk-queues notifications from a CSV in the same shape export() produces, all as status=queued. */
    public function import(): void
    {
        $this->verifyCsrf();

        $imported = 0;
        $skipped = 0;

        foreach (Csv::parseUpload(Request::file('csv')) as $row) {
            $subject = trim((string) ($row['subject'] ?? ''));
            if ($subject === '') {
                $skipped++;
                continue;
            }

            $userId = (int) ($row['user_id'] ?? 0);
            $channel = in_array($row['channel'] ?? '', ['email', 'sms', 'push'], true) ? $row['channel'] : 'email';

            NotificationLog::queue($userId > 0 ? $userId : null, $channel, $subject, $row['body'] ?? '');
            $imported++;
        }

        $this->flash('success', "Imported {$imported} notification(s)." . ($skipped ? " Skipped {$skipped} row(s) missing a subject." : ''));
        $this->redirect('/notifications');
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $userId = Request::input('user_id');

        NotificationLog::create([
            'user_id' => ($userId !== '' && $userId !== null) ? (int) $userId : null,
            'channel' => Request::input('channel', 'email'),
            'subject' => Request::input('subject') ?: null,
            'body' => Request::input('body') ?: null,
            'status' => 'queued',
        ]);

        $this->flash('success', 'Notification queued.');
        $this->redirect('/notifications');
    }

    public function show(string $id): void
    {
        $notification = NotificationLog::findWithDetails((int) $id);
        if (!$notification) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $this->view('notifications.show', ['notification' => $notification]);
    }

    /** No email/SMS/push transport is wired up — this just records the delivery outcome you observed elsewhere. */
    public function updateStatus(string $id): void
    {
        $this->verifyCsrf();

        $status = Request::input('status', 'queued');
        $data = ['status' => $status];
        if ($status === 'sent') {
            $data['sent_at'] = date('Y-m-d H:i:s');
        }

        NotificationLog::update((int) $id, $data);

        $this->flash('success', 'Notification updated.');
        $this->redirect("/notifications/{$id}");
    }
}
