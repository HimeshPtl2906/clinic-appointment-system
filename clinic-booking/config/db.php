<?php
// Database connection (PDO) + session start. Har page me ye file include hoti hai.
session_start();

$host = 'localhost';
$db   = 'clinic_db';
$user = 'root';      // XAMPP default
$pass = '';          // XAMPP default empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

define('BASE_URL', '/clinic-booking');   // htdocs me folder ka naam
define('SLOT_MINUTES', 30);               // ek appointment ka time
