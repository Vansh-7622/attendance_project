<?php
// public/api/mark_attendance.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Only POST allowed']);
    exit;
}

// Read JSON payload
$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON payload']);
    exit;
}

$class_id = isset($payload['class_id']) ? (int)$payload['class_id'] : 0;
$date = isset($payload['date']) ? $payload['date'] : date('Y-m-d');
$marks = isset($payload['marks']) && is_array($payload['marks']) ? $payload['marks'] : [];

if ($class_id <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing/invalid class_id or date']);
    exit;
}

if (count($marks) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No attendance data provided']);
    exit;
}

try {
    // Use transaction and upsert (INSERT ... ON DUPLICATE KEY UPDATE)
    $pdo->beginTransaction();

    $sql = "INSERT INTO attendance (student_id, class_id, attendance_date, status, marked_by)
            VALUES (:student_id, :class_id, :attendance_date, :status, :marked_by)
            ON DUPLICATE KEY UPDATE status = VALUES(status), marked_by = VALUES(marked_by), marked_at = CURRENT_TIMESTAMP";

    $stmt = $pdo->prepare($sql);

    $userId = $_SESSION['user']['id'];

    foreach ($marks as $m) {
        $student_id = isset($m['student_id']) ? (int)$m['student_id'] : 0;
        $status = isset($m['status']) ? $m['status'] : 'present';
        if ($student_id <= 0) continue;
        if (!in_array($status, ['present','absent','late'], true)) $status = 'present';

        $stmt->execute([
            ':student_id' => $student_id,
            ':class_id' => $class_id,
            ':attendance_date' => $date,
            ':status' => $status,
            ':marked_by' => $userId
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
