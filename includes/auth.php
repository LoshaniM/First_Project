<?php
/**
 * TRAVELMATE - Authentication & Security Helpers
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/csrf.php';

/**
 * Escape output for safe HTML rendering (XSS prevention)
 */
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Check if the currently logged-in user is an administrator
 */
function is_admin_logged_in(): bool {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }

    // 2-hour inactivity session expiration
    $maxInactivity = 7200; // 2 hours
    if (isset($_SESSION['admin_last_activity']) && (time() - $_SESSION['admin_last_activity'] > $maxInactivity)) {
        admin_logout_silent();
        return false;
    }

    $_SESSION['admin_last_activity'] = time();
    return true;
}

/**
 * Enforce authentication on all admin-protected routes
 */
function require_admin(): void {
    if (!is_admin_logged_in()) {
        $_SESSION['flash_error'] = 'Access restricted. Please log in to access the administration panel.';
        $redirectUrl = BASE_URL . 'admin/login.php';
        header("Location: " . $redirectUrl);
        exit;
    }
}

/**
 * Perform secure admin login
 */
function perform_admin_login(int $adminId, string $username): void {
    // Regenerate session ID to mitigate session fixation attacks
    session_regenerate_id(true);

    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $adminId;
    $_SESSION['admin_username'] = $username;
    $_SESSION['admin_last_activity'] = time();

    // Refresh CSRF token on login
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Silently clear admin session keys without destroying tourist plan
 */
function admin_logout_silent(): void {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_last_activity']);
}

/**
 * Full admin logout with redirect
 */
function perform_admin_logout(): void {
    admin_logout_silent();
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
    $_SESSION['flash_success'] = 'You have successfully logged out.';
    header("Location: " . BASE_URL . 'admin/login.php');
    exit;
}
