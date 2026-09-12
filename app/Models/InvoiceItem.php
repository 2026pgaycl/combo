<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class InvoiceItem extends Model
{
    protected static string $table = 'invoice_items';

    public static function forInvoice(int $invoiceId): array
    {
        return Database::select(
            'SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id',
            [$invoiceId]
        );
    }
}
