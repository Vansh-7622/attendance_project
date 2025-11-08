<?php
// public/api/add_class.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_login(['admin']); // only admin can add classes (change if you want teachers to add)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Only POST allowed']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$subject_id = isset($_POST['subject_id']) ? (int) $_POST['subject_id'] : null;

if ($name === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Class name is required']);
    exit;
}

try {
    // Optional: ensure class name is unique (within DB)
    $stmt = $pdo->prepare("SELECT id FROM classes WHERE name = ?");
    $stmt->execute([$name]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'A class with that name already exists']);
        exit;
    }

    $ins = $pdo->prepare("INSERT INTO classes (name, subject_id) VALUES (?, ?)");
    $ins->execute([$name, $subject_id ?: null]);

    $newId = (int)$pdo->lastInsertId();
    echo json_encode(['success' => true, 'id' => $newId, 'name' => $name]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
