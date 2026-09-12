<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    protected static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $host = Env::get('DB_HOST', 'localhost');
            $name = Env::get('DB_NAME');
            $charset = Env::get('DB_CHARSET', 'utf8mb4');
            $user = Env::get('DB_USER');
            $pass = Env::get('DB_PASS');

            $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

            try {
                self::$connection = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                // Never leak DB credentials or raw PDO errors to the browser.
                error_log('[Combo] DB connection failed: ' . $e->getMessage());
                http_response_code(500);
                die('Service temporarily unavailable. Please try again shortly.');
            }
        }

        return self::$connection;
    }

    /** Run a SELECT and return all matching rows. */
    public static function select(string $sql, array $params = []): array
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Run a SELECT and return the first matching row, or null. */
    public static function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Run an INSERT/UPDATE/DELETE. Returns affected row count. */
    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** Run an INSERT and return the new row's id. */
    public static function insertGetId(string $sql, array $params = []): int
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return (int) self::connection()->lastInsertId();
    }

    public static function beginTransaction(): void
    {
        self::connection()->beginTransaction();
    }

    public static function commit(): void
    {
        self::connection()->commit();
    }

    public static function rollBack(): void
    {
        self::connection()->rollBack();
    }
}
