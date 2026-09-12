<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class NotificationLog extends Model
{
    protected static string $table = 'notifications_log';

    public static function withDetails(): array
    {
        return Database::select(
            'SELECT n.*, u.name AS user_name
             FROM notifications_log n
             LEFT JOIN users u ON u.id = n.user_id
             ORDER BY n.created_at DESC'
        );
    }

    public static function findWithDetails(int $id): ?array
    {
        return Database::selectOne(
            'SELECT n.*, u.name AS user_name
             FROM notifications_log n
             LEFT JOIN users u ON u.id = n.user_id
             WHERE n.id = ?',
            [$id]
        );
    }

    /**
     * Queues a notification for an app event. $userId is a users.id (e.g. staff
     * assigned to a ticket) — tenants have no user account in this schema, so
     * tenant-facing notices pass null and identify the recipient in the body.
     */
    public static function queue(?int $userId, string $channel, string $subject, string $body): void
    {
        self::create([
            'user_id' => $userId,
            'channel' => $channel,
            'subject' => $subject,
            'body' => $body,
            'status' => 'queued',
        ]);
    }
}
