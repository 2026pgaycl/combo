<?php

namespace App\Core;

abstract class Model
{
    protected static string $table = '';

    public static function find(int $id): ?array
    {
        return Database::selectOne('SELECT * FROM ' . static::$table . ' WHERE id = ?', [$id]);
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        return Database::select('SELECT * FROM ' . static::$table . ' ORDER BY ' . $orderBy);
    }

    public static function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $sql = 'INSERT INTO ' . static::$table . ' (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')';
        return Database::insertGetId($sql, array_values($data));
    }

    public static function update(int $id, array $data): int
    {
        $assignments = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $sql = 'UPDATE ' . static::$table . " SET {$assignments} WHERE id = ?";
        return Database::execute($sql, [...array_values($data), $id]);
    }

    public static function delete(int $id): int
    {
        return Database::execute('DELETE FROM ' . static::$table . ' WHERE id = ?', [$id]);
    }
}
