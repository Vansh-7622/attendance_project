<?php
// public/api/add_student.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

if (session_status() === PHP_SESSION_NONE) session_start();
// allow admin or teacher to add students - change to require_login(['admin']) to restrict
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Only POST allowed']);
    exit;
}

// Read POST values
$roll_no = trim($_POST['roll_no'] ?? '');
$name = trim($_POST['name'] ?? '');
$class_id = isset($_POST['class_id']) ? (int) $_POST['class_id'] : 0;
$email = trim($_POST['email'] ?? '');

if ($roll_no === '' || $name === '' || $class_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'roll_no, name and class_id are required']);
    exit;
}

// Optional: basic email validation
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

try {
    // Ensure class exists
    $stmt = $pdo->prepare("SELECT id FROM classes WHERE id = ?");
    $stmt->execute([$class_id]);
    if (!$stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Specified class does not exist']);
        exit;
    }

    // Ensure unique roll_no within class
    $stmt = $pdo->prepare("SELECT id FROM students WHERE roll_no = ? AND class_id = ?");
    $stmt->execute([$roll_no, $class_id]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'A student with this roll number already exists in the class']);
        exit;
    }

    // Insert
    $ins = $pdo->prepare("INSERT INTO students (roll_no, name, class_id, email) VALUES (?, ?, ?, ?)");
    $ins->execute([$roll_no, $name, $class_id, $email !== '' ? $email : null]);

    $newId = (int)$pdo->lastInsertId();
    echo json_encode(['success' => true, 'id' => $newId, 'roll_no' => $roll_no, 'name' => $name]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
