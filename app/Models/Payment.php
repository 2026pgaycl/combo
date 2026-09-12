<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Payment extends Model
{
    protected static string $table = 'payments';

    public static function forInvoice(int $invoiceId): array
    {
        return Database::select(
            'SELECT * FROM payments WHERE invoice_id = ? ORDER BY paid_at DESC',
            [$invoiceId]
        );
    }

    public static function totalPaid(int $invoiceId): float
    {
        $row = Database::selectOne(
            'SELECT COALESCE(SUM(amount), 0) AS total FROM payments WHERE invoice_id = ?',
            [$invoiceId]
        );
        return (float) ($row['total'] ?? 0);
    }
}
