<?php
// =====================================
// Fuel Station Management System v3.0
// Core Configuration
// =====================================

// App Details
define('APP_NAME', 'Fuel Station Management System v3.0');
define('APP_URL', 'http://localhost/fuel_station');

// Base Path
define('ROOT_PATH', dirname(__DIR__));

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'fuel_station_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Timezone
date_default_timezone_set('Asia/Colombo');

// =====================================
// DATABASE CONNECTION (PDO)
// =====================================
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}