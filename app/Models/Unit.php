<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Unit extends Model
{
    protected static string $table = 'units';

    /** All units with their floor + building name, and current tenant if occupied. */
    public static function withDetails(?int $buildingId = null): array
    {
        $sql = 'SELECT u.*, f.name AS floor_name, b.id AS building_id, b.name AS building_name,
                       t.company_name AS tenant_name
                FROM units u
                JOIN floors f ON f.id = u.floor_id
                JOIN buildings b ON b.id = f.building_id
                LEFT JOIN leases l ON l.unit_id = u.id AND l.status = "active"
                LEFT JOIN tenants t ON t.id = l.tenant_id';
        $params = [];
        if ($buildingId) {
            $sql .= ' WHERE b.id = ?';
            $params[] = $buildingId;
        }
        $sql .= ' ORDER BY b.name, f.level_order, u.unit_number';
        return Database::select($sql, $params);
    }

    public static function findWithDetails(int $id): ?array
    {
        return Database::selectOne(
            'SELECT u.*, f.name AS floor_name, f.building_id, b.name AS building_name
             FROM units u
             JOIN floors f ON f.id = u.floor_id
             JOIN buildings b ON b.id = f.building_id
             WHERE u.id = ?',
            [$id]
        );
    }

    public static function vacantUnits(): array
    {
        return Database::select(
            'SELECT u.*, f.name AS floor_name, b.name AS building_name
             FROM units u
             JOIN floors f ON f.id = u.floor_id
             JOIN buildings b ON b.id = f.building_id
             WHERE u.status = "vacant"
             ORDER BY b.name, u.unit_number'
        );
    }
}
