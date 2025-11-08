<?php
// public/api/login.php
// Handles login form POST

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /attendance_project/public/index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: /attendance_project/public/index.php?error=' . urlencode('Please provide username and password.'));
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, username, password_hash, role, name FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // login success
        if (session_status() === PHP_SESSION_NONE) session_start();
        // store minimal safe info in session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'name' => $user['name']
        ];
        // redirect to dashboard
        header('Location: /attendance_project/public/dashboard.php');
        exit;
    } else {
        header('Location: /attendance_project/public/index.php?error=' . urlencode('Invalid credentials.'));
        exit;
    }
} catch (Exception $e) {
    // for dev, show message (in production log instead)
    header('Location: /attendance_project/public/index.php?error=' . urlencode('Server error: ' . $e->getMessage()));
    exit;
}
