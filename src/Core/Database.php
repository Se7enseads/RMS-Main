<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::$connection = self::createConnection();
        }
        return self::$connection;
    }

    private static function createConnection(): PDO
    {
        $db_host = getenv('RMS_DB_HOST') ?: "127.0.0.1";
        $db_name = getenv('RMS_DB_NAME') ?: "rms";
        $db_port = getenv('RMS_DB_PORT') ?: "3306";

        $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";

        $db_user = getenv('RMS_DB_USER') ?: "user";
        $db_pass = getenv('RMS_DB_PASS') ?: "password";

        try {
            return new PDO(
                $dsn,
                $db_user,
                $db_pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ],
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Connection failed: ' . $e->getMessage());
        }
    }
}
