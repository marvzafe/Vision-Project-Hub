<?php
// /src/Core/Database.php

class Database {
    private static $connection = null;
    private static $env = null; // Cache the env data

    // 1. New reusable method to grab the .env variables
    public static function getEnv() {
        if (self::$env === null) {
            $envPath = __DIR__ . '/../../.env';
            if (!file_exists($envPath)) {
                die("Configuration error: .env file not found.");
            }
            self::$env = parse_ini_file($envPath);
        }
        return self::$env;
    }

    public static function getConnection() {
        if (self::$connection === null) {
            
            // 2. Call our new method here
            $env = self::getEnv();

            $host = $env['DB_HOST']; 
            $port = $env['DB_PORT']; 
            $dbname = $env['DB_NAME'];
            $user = $env['DB_USER'];
            $password = $env['DB_PASSWORD'];

            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

            try {
                self::$connection = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
                    PDO::ATTR_EMULATE_PREPARES => false, 
                ]);
            } catch (PDOException $e) {
                die("Supabase Connection Failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}