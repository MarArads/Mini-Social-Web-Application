<?php

class Database {
    private static $instance = null;

    public static function connect() {
        if (self::$instance === null) {
            $host = 'localhost';
            $dbname = 'social_app';
            $username = 'root';
            $password = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                $dsnWithoutDb = "mysql:host=$host;charset=$charset";
                $pdo = new PDO($dsnWithoutDb, $username, $password, $options);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                self::$instance = new PDO($dsn, $username, $password, $options);
            }
        }
        return self::$instance;
    }
}
