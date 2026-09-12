<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Invoice extends Model
{
    protected static string $table = 'invoices';

    /** Invoices with tenant/unit context and amount paid so far. */
    public static function withDetails(): array
    {
        return Database::select(
            'SELECT i.*, t.company_name, u.unit_number, b.name AS building_name,
                    COALESCE(SUM(p.amount), 0) AS amount_paid
             FROM invoices i
             JOIN leases l ON l.id = i.lease_id
             JOIN tenants t ON t.id = l.tenant_id
             JOIN units u ON u.id = l.unit_id
             JOIN floors f ON f.id = u.floor_id
             JOIN buildings b ON b.id = f.building_id
             LEFT JOIN payments p ON p.invoice_id = i.id
             GROUP BY i.id
             ORDER BY i.due_date DESC'
        );
    }

    public static function findWithDetails(int $id): ?array
    {
        return Database::selectOne(
            'SELECT i.*, t.company_name, t.contact_name, t.contact_email, u.unit_number, b.name AS building_name
             FROM invoices i
             JOIN leases l ON l.id = i.lease_id
             JOIN tenants t ON t.id = l.tenant_id
             JOIN units u ON u.id = l.unit_id
             JOIN floors f ON f.id = u.floor_id
             JOIN buildings b ON b.id = f.building_id
             WHERE i.id = ?',
            [$id]
        );
    }
}
