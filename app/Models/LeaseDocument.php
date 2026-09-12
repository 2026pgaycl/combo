<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class LeaseDocument extends Model
{
    protected static string $table = 'lease_documents';

    public static function forLease(int $leaseId): array
    {
        return Database::select(
            'SELECT * FROM lease_documents WHERE lease_id = ? ORDER BY uploaded_at DESC',
            [$leaseId]
        );
    }
}
