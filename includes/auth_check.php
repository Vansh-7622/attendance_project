<?php
// includes/auth_check.php
// Simple session-based auth helpers. Include this at top of pages/APIs that need auth.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * require_login($allowed_roles = [])
 * - If not logged in, redirects to login page.
 * - If $allowed_roles is non-empty, ensures user role is one of them.
 *
 * Usage:
 *   require_login(); // any logged-in user
 *   require_login(['admin']); // only admin
 */
function require_login(array $allowed_roles = []) {
    if (empty($_SESSION['user']) || empty($_SESSION['user']['id'])) {
        // Not logged in — redirect to login page
        header('Location: /attendance_project/index.php');
        exit;
    }

    if (!empty($allowed_roles) && !in_array($_SESSION['user']['role'], $allowed_roles, true)) {
        // Logged in but role not permitted
        http_response_code(403);
        echo "Forbidden: you don't have access to this resource.";
        exit;
    }
}

/**
 * get_current_user()
 * Returns the logged in user's session info or null.
 */
function get_logged_in_user() {
    return $_SESSION['user'] ?? null;
}

