<?php
// public/api/reports.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_login();

$class_id = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
$from = isset($_GET['from']) ? $_GET['from'] : null;
$to = isset($_GET['to']) ? $_GET['to'] : null;

// Validate inputs
if ($class_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing or invalid class_id']);
    exit;
}
if (!$from) $from = date('Y-m-01'); // default from = first day of month
if (!$to) $to = date('Y-m-d');      // default to = today

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid date format. Use YYYY-MM-DD']);
    exit;
}

try {
    // 1) get list of students in class
    $stmt = $pdo->prepare("SELECT id, roll_no, name FROM students WHERE class_id = ? ORDER BY roll_no");
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll();

    // 2) for each student compute present_count and total_days in range
    $report = [];

    $countStmt = $pdo->prepare("
        SELECT
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_count,
            COUNT(*) AS total_records
        FROM attendance
        WHERE student_id = ? AND class_id = ? AND attendance_date BETWEEN ? AND ?
    ");

    foreach ($students as $s) {
        $countStmt->execute([$s['id'], $class_id, $from, $to]);
        $counts = $countStmt->fetch();

        $present = isset($counts['present_count']) ? (int)$counts['present_count'] : 0;
        $total = isset($counts['total_records']) ? (int)$counts['total_records'] : 0;
        $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0.00;

        $report[] = [
            'student_id' => (int)$s['id'],
            'roll_no'    => $s['roll_no'],
            'name'       => $s['name'],
            'present'    => $present,
            'total'      => $total,
            'percentage' => $percentage
        ];
    }

    echo json_encode(['success' => true, 'data' => $report, 'meta' => ['from' => $from, 'to' => $to, 'class_id' => $class_id]]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
