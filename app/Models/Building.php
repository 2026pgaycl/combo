<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Building extends Model
{
    protected static string $table = 'buildings';

    /** Buildings with a computed unit count and occupancy rate. */
    public static function withStats(): array
    {
        return Database::select(
            'SELECT b.*,
                    COUNT(u.id) AS unit_count,
                    SUM(CASE WHEN u.status = "occupied" THEN 1 ELSE 0 END) AS occupied_count
             FROM buildings b
             LEFT JOIN floors f ON f.building_id = b.id
             LEFT JOIN units u ON u.floor_id = f.id
             GROUP BY b.id
             ORDER BY b.name'
        );
    }
}
