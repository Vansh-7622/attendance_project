<?php
// includes/db.php
// PDO-based database connection for attendance_project
// Adjust $host/$db/$user/$pass if needed.

$host = '127.0.0.1';
$db   = 'attendance_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // In production, don't echo full errors. For development we show it to help debug.
    http_response_code(500);
    echo "Database connection failed: " . htmlspecialchars($e->getMessage());
    exit;
}
