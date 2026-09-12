<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Lease extends Model
{
    protected static string $table = 'leases';

    public static function withDetails(): array
    {
        return Database::select(
            'SELECT l.*, t.company_name, u.unit_number, b.name AS building_name,
                    DATEDIFF(l.end_date, CURDATE()) AS days_to_expiry
             FROM leases l
             JOIN tenants t ON t.id = l.tenant_id
             JOIN units u ON u.id = l.unit_id
             JOIN floors f ON f.id = u.floor_id
             JOIN buildings b ON b.id = f.building_id
             ORDER BY l.end_date'
        );
    }

    public static function findWithDetails(int $id): ?array
    {
        return Database::selectOne(
            'SELECT l.*, t.company_name, t.contact_name, t.contact_email,
                    u.unit_number, b.name AS building_name
             FROM leases l
             JOIN tenants t ON t.id = l.tenant_id
             JOIN units u ON u.id = l.unit_id
             JOIN floors f ON f.id = u.floor_id
             JOIN buildings b ON b.id = f.building_id
             WHERE l.id = ?',
            [$id]
        );
    }

    /** Leases expiring within N days — used for the 90/60/30-day renewal alerts. */
    public static function expiringWithin(int $days): array
    {
        return Database::select(
            'SELECT l.*, t.company_name, t.contact_email, u.unit_number, b.name AS building_name
             FROM leases l
             JOIN tenants t ON t.id = l.tenant_id
             JOIN units u ON u.id = l.unit_id
             JOIN floors f ON f.id = u.floor_id
             JOIN buildings b ON b.id = f.building_id
             WHERE l.status = "active"
               AND l.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             ORDER BY l.end_date',
            [$days]
        );
    }
}
