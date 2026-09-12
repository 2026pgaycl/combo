<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class MaintenanceTicket extends Model
{
    protected static string $table = 'maintenance_tickets';

    public static function withDetails(): array
    {
        return Database::select(
            'SELECT mt.*, u.unit_number, b.name AS building_name,
                    t.company_name AS tenant_name, usr.name AS assignee_name
             FROM maintenance_tickets mt
             JOIN units u ON u.id = mt.unit_id
             JOIN floors f ON f.id = u.floor_id
             JOIN buildings b ON b.id = f.building_id
             LEFT JOIN tenants t ON t.id = mt.tenant_id
             LEFT JOIN users usr ON usr.id = mt.assigned_to
             ORDER BY FIELD(mt.status, "open", "in_progress", "resolved", "closed"), mt.created_at DESC'
        );
    }

    public static function findWithDetails(int $id): ?array
    {
        return Database::selectOne(
            'SELECT mt.*, u.unit_number, b.name AS building_name,
                    t.company_name AS tenant_name, usr.name AS assignee_name
             FROM maintenance_tickets mt
             JOIN units u ON u.id = mt.unit_id
             JOIN floors f ON f.id = u.floor_id
             JOIN buildings b ON b.id = f.building_id
             LEFT JOIN tenants t ON t.id = mt.tenant_id
             LEFT JOIN users usr ON usr.id = mt.assigned_to
             WHERE mt.id = ?',
            [$id]
        );
    }

    /** A tenant's own tickets, for the tenant portal. */
    public static function forTenant(int $tenantId): array
    {
        return Database::select(
            'SELECT mt.*, u.unit_number, b.name AS building_name, usr.name AS assignee_name
             FROM maintenance_tickets mt
             JOIN units u ON u.id = mt.unit_id
             JOIN floors f ON f.id = u.floor_id
             JOIN buildings b ON b.id = f.building_id
             LEFT JOIN users usr ON usr.id = mt.assigned_to
             WHERE mt.tenant_id = ?
             ORDER BY FIELD(mt.status, "open", "in_progress", "resolved", "closed"), mt.created_at DESC',
            [$tenantId]
        );
    }

    /** Scoped lookup so a tenant can only ever open their own ticket by id. */
    public static function findForTenant(int $id, int $tenantId): ?array
    {
        return Database::selectOne(
            'SELECT mt.*, u.unit_number, b.name AS building_name, usr.name AS assignee_name
             FROM maintenance_tickets mt
             JOIN units u ON u.id = mt.unit_id
             JOIN floors f ON f.id = u.floor_id
             JOIN buildings b ON b.id = f.building_id
             LEFT JOIN users usr ON usr.id = mt.assigned_to
             WHERE mt.id = ? AND mt.tenant_id = ?',
            [$id, $tenantId]
        );
    }
}
