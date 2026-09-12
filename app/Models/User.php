<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    /** Staff who can be assigned maintenance tickets. */
    public static function maintenanceStaff(): array
    {
        return Database::select(
            "SELECT u.* FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE r.slug IN ('maintenance_staff', 'property_manager', 'super_admin')
             ORDER BY u.name"
        );
    }

    /** The portal login for a tenant, if one has been created. */
    public static function findByTenant(int $tenantId): ?array
    {
        return Database::selectOne('SELECT * FROM users WHERE tenant_id = ?', [$tenantId]);
    }
}
