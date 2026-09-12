<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class TicketUpdate extends Model
{
    protected static string $table = 'ticket_updates';

    public static function forTicket(int $ticketId): array
    {
        return Database::select(
            'SELECT tu.*, u.name AS user_name
             FROM ticket_updates tu
             JOIN users u ON u.id = tu.user_id
             WHERE tu.ticket_id = ?
             ORDER BY tu.created_at DESC',
            [$ticketId]
        );
    }
}
