<?php
// /public/test_db.php

// 1. Go up one folder, then into src/Core to find your Database class
require_once __DIR__ . '/../src/core/database.php';

echo "<h1>Testing Supabase Connection...</h1>";

try {
    // 2. Attempt to open the connection
    $db = Database::getConnection();
    
    // 3. If the line above doesn't crash, it means you are connected!
    echo "<h2 style='color: green;'>✅ Connection Successful! PHP is officially talking to Supabase.</h2>";

} catch (Exception $e) {
    // 4. If something goes wrong, catch the error and print it
    echo "<h2 style='color: red;'>❌ Connection Failed.</h2>";
    echo "<p><strong>Error Details:</strong> " . $e->getMessage() . "</p>";
}