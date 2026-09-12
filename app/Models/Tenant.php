<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Tenant extends Model
{
    protected static string $table = 'tenants';

    /** Tenants with their currently active lease info, if any. */
    public static function withActiveLease(): array
    {
        return Database::select(
            'SELECT t.*, l.id AS lease_id, l.end_date, u.unit_number, b.name AS building_name
             FROM tenants t
             LEFT JOIN leases l ON l.tenant_id = t.id AND l.status = "active"
             LEFT JOIN units u ON u.id = l.unit_id
             LEFT JOIN floors f ON f.id = u.floor_id
             LEFT JOIN buildings b ON b.id = f.building_id
             ORDER BY t.company_name'
        );
    }
}
