<?php
// public/api/get_classes.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

// Only logged-in users may call this
if (session_status() === PHP_SESSION_NONE) session_start();
require_login();

try {
    $stmt = $pdo->query("SELECT id, name FROM classes ORDER BY name");
    $classes = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $classes]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
