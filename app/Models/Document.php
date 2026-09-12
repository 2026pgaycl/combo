<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Document extends Model
{
    protected static string $table = 'documents';

    public static function forRelated(string $type, int $id): array
    {
        return Database::select(
            'SELECT * FROM documents WHERE related_type = ? AND related_id = ? ORDER BY created_at DESC',
            [$type, $id]
        );
    }

    /** All documents with a human-readable label for whatever they're attached to. */
    public static function withLabels(): array
    {
        return Database::select(
            "SELECT d.*,
                    CASE d.related_type
                        WHEN 'building' THEN (SELECT name FROM buildings WHERE id = d.related_id)
                        WHEN 'tenant' THEN (SELECT company_name FROM tenants WHERE id = d.related_id)
                        WHEN 'unit' THEN (
                            SELECT CONCAT(b.name, ' — ', u.unit_number)
                            FROM units u
                            JOIN floors f ON f.id = u.floor_id
                            JOIN buildings b ON b.id = f.building_id
                            WHERE u.id = d.related_id
                        )
                        WHEN 'lease' THEN (
                            SELECT CONCAT('Lease #', l.id, ' — ', t.company_name)
                            FROM leases l
                            JOIN tenants t ON t.id = l.tenant_id
                            WHERE l.id = d.related_id
                        )
                    END AS related_label
             FROM documents d
             ORDER BY d.created_at DESC"
        );
    }
}
