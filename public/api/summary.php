<?php
// public/api/summary.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_login();

try {
    // Total students
    $totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();

    // Total classes
    $totalClasses = (int)$pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();

    // Today's attendance counts (use server date)
    $today = date('Y-m-d');

    $presentToday = (int)$pdo->prepare("SELECT COUNT(*) FROM attendance WHERE attendance_date = ? AND status = 'present'")->execute([$today]) ? 0 : 0;
    // above line written defensively — replace with safe fetch below
    $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM attendance WHERE attendance_date = ? AND status = 'present'");
    $stmt->execute([$today]);
    $presentToday = (int)$stmt->fetchColumn();

    $totalMarkedToday = 0;
    $stmt2 = $pdo->prepare("SELECT COUNT(*) as c FROM attendance WHERE attendance_date = ?");
    $stmt2->execute([$today]);
    $totalMarkedToday = (int)$stmt2->fetchColumn();

    $overallPercentToday = $totalMarkedToday > 0 ? round(($presentToday / $totalMarkedToday) * 100, 2) : 0.00;

    // Per-class breakdown for today
    $stmt3 = $pdo->prepare("
        SELECT c.id, c.name,
               COALESCE(SUM(att.status = 'present'),0) AS present,
               COALESCE(COUNT(att.id),0) AS total
        FROM classes c
        LEFT JOIN attendance att
          ON att.class_id = c.id AND att.attendance_date = ?
        GROUP BY c.id, c.name
        ORDER BY c.name
    ");
    $stmt3->execute([$today]);
    $perClass = $stmt3->fetchAll();

    // Normalize types
    foreach ($perClass as &$pc) {
        $pc['present'] = (int)$pc['present'];
        $pc['total'] = (int)$pc['total'];
        $pc['percentage'] = $pc['total'] > 0 ? round(($pc['present'] / $pc['total']) * 100, 2) : 0.00;
    }
    unset($pc);

    echo json_encode([
        'success' => true,
        'data' => [
            'total_students'   => $totalStudents,
            'total_classes'    => $totalClasses,
            'present_today'    => $presentToday,
            'marked_today'     => $totalMarkedToday,
            'overall_percent_today' => $overallPercentToday,
            'per_class'        => $perClass,
            'date'             => $today
        ]
    ]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
