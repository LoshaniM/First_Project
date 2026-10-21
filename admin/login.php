<?php
/**
 * TRAVELMATE - Administrator Login (FR-24 to FR-28)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already authenticated
if (is_admin_logged_in()) {
    header("Location: " . BASE_URL . "admin/dashboard.php");
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    enforce_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM administrator WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                // Login successful: Regenerate session ID and store auth keys
                perform_admin_login((int)$admin['admin_id'], $admin['username']);
                set_flash('success', 'Welcome back, ' . htmlspecialchars($admin['username']) . '!');
                header("Location: " . BASE_URL . "admin/dashboard.php");
                exit;
            } else {
                // Invalid credentials error feedback
                $error = 'Invalid username or password. Please try again.';
            }
        } catch (PDOException $e) {
            error_log("Login DB error: " . $e->getMessage());
            $error = 'A database error occurred. Please ensure MySQL is running.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>
<body class="login-body">

<div class="login-card">
    <div class="login-header">
        <a href="<?= BASE_URL ?>index.php" style="text-decoration: none;">
            <div class="login-brand">
                TRAVELMATE <span style="font-size: 1rem; color: #198754; font-weight: 600;">ADMIN</span>
            </div>
        </a>
        <p class="login-sub">Sign in to manage attraction and category records</p>
    </div>

    <?= render_flash_messages() ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert" style="margin-bottom: 1.25rem;">
            <span><?= e($error) ?></span>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>admin/login.php" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= e($username) ?>" required autofocus placeholder="Enter administrator username">
        </div>

        <div class="form-group" style="margin-bottom: 1.75rem;">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem;">
            Sign In to Dashboard &rarr;
        </button>
    </form>

    <div class="login-footer-info">
        <p style="margin: 0 0 0.5rem;">
            <strong>Default Credentials:</strong> <code>admin</code> / <code>Admin@123</code>
        </p>
        <a href="<?= BASE_URL ?>index.php" style="color: var(--admin-muted); font-size: 0.85rem;">
            &larr; Return to Public Website
        </a>
    </div>
</div>

<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
