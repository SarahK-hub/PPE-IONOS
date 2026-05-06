<?php
namespace Config;

use PDO;

final class Database {
    private static ?PDO $pdo = null;

    public static function get(): PDO {
        if (self::$pdo) return self::$pdo;

        // Lecture du fichier .env (obligatoire pour IONOS)
        $envFile = __DIR__ . '/../../.env';
        $env = is_file($envFile)
            ? parse_ini_file($envFile, false, INI_SCANNER_RAW)
            : [];

        $host = trim((string)($env['DB_HOST'] ?? ''));
        $port = trim((string)($env['DB_PORT'] ?? '3306'));
        $name = trim((string)($env['DB_NAME'] ?? ''));
        $user = trim((string)($env['DB_USER'] ?? ''));
        $pass = trim((string)($env['DB_PASSWORD'] ?? ''));

        // Log de debug (utile pour diagnostiquer sur IONOS)
        @file_put_contents(
            __DIR__ . '/../../ppe_logs/db-debug.log',
            '[' . date('c') . '] host=' . $host .
            ' db=' . $name .
            ' user=' . $user .
            ' pass_len=' . strlen($pass) . "\n",
            FILE_APPEND
        );

        if ($host === '' || $name === '' || $user === '' || $pass === '') {
            throw new \RuntimeException('Configuration BDD incomplète dans .env');
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$pdo;
    }
}
