<?php
// public/api/get_students.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

// Only logged-in users may call this
if (session_status() === PHP_SESSION_NONE) session_start();
require_login();

// expect class_id as GET parameter
$class_id = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
if ($class_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing or invalid class_id']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, roll_no, name, email FROM students WHERE class_id = ? ORDER BY roll_no");
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $students]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
