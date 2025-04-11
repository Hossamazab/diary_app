<?php

// if (session_status() === PHP_SESSION_NONE) {
//     session_start([
//         'cookie_lifetime' => 86400,
//         'read_and_close' => false,
//     ]);
// }
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'diary_app');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Fixed this line
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Base URL
define('BASE_URL', 'http://localhost/diary_app');
?>