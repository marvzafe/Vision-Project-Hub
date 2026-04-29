<?php
// /src/Core/Database.php

class Database {
    private static $connection = null;

    public static function getConnection() {
        
        if (self::$connection === null) {
            
            // 1. Locate the .env file (assuming it's two directories up in the root)
            $envPath = __DIR__ . '/../../.env';
            
            // 2. Parse the file. Throw an error if it doesn't exist.
            if (!file_exists($envPath)) {
                die("Configuration error: .env file not found.");
            }
            
            $env = parse_ini_file($envPath);

            // 3. Assign the variables from the parsed .env array
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