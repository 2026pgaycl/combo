<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class UnitPhoto extends Model
{
    protected static string $table = 'unit_photos';

    public static function forUnit(int $unitId): array
    {
        return Database::select(
            'SELECT * FROM unit_photos WHERE unit_id = ? ORDER BY created_at DESC',
            [$unitId]
        );
    }
}
