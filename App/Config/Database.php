<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=db_peminjamanproperti;charset=utf8mb4',
                    'root',
                    '',
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                http_response_code(500);
                exit('Koneksi database gagal.');
            }
        }
        return self::$pdo;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public static function exec(string $sql, array $params = []): bool
    {
        return self::getConnection()->prepare($sql)->execute($params);
    }

    public static function generateId(string $table, string $column, string $prefix): string
    {
        $row  = self::fetchOne("SELECT MAX($column) AS max_id FROM $table WHERE $column LIKE :p", [':p' => $prefix . '%']);
        $next = ($row && !empty($row['max_id'])) ? ((int)substr((string)$row['max_id'], strlen($prefix)) + 1) : 1;
        return $prefix . str_pad((string)$next, 8 - strlen($prefix), '0', STR_PAD_LEFT);
    }
}
