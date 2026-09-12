<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Floor extends Model
{
    protected static string $table = 'floors';

    public static function forBuilding(int $buildingId): array
    {
        return Database::select(
            'SELECT * FROM floors WHERE building_id = ? ORDER BY level_order, name',
            [$buildingId]
        );
    }
}
