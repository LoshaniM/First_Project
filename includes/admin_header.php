<?php
/**
 * TRAVELMATE - Admin Navigation & Session Header
 * Blocked for unauthenticated users (Server-side session check: FR-25)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Enforce authentication on every admin page
require_admin();

$currentFile = basename($_SERVER['PHP_SELF']);
$adminUser = $_SESSION['admin_username'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($adminTitle) ? e($adminTitle) . ' | Admin - ' . APP_NAME : 'Admin Dashboard | ' . APP_NAME ?></title>
    
    <!-- Admin Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>
<body>

<header class="admin-header">
    <div class="admin-nav-container">
        <a href="<?= BASE_URL ?>admin/dashboard.php" class="admin-brand">
            <?= APP_NAME ?> <span>ADMIN</span>
            <span class="admin-badge-label">Control Panel</span>
        </a>

        <ul class="admin-menu">
            <li>
                <a href="<?= BASE_URL ?>admin/dashboard.php" class="admin-menu-link <?= ($currentFile === 'dashboard.php') ? 'active' : '' ?>">Dashboard</a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>admin/places.php" class="admin-menu-link <?= in_array($currentFile, ['places.php', 'place-add.php', 'place-edit.php', 'place_add.php', 'place_edit.php']) ? 'active' : '' ?>">Attractions</a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>admin/categories.php" class="admin-menu-link <?= ($currentFile === 'categories.php') ? 'active' : '' ?>">Categories</a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>places.php" target="_blank" class="admin-menu-link" title="Open Public Website">View Site &nearr;</a>
            </li>
        </ul>

        <div class="admin-user-profile">
            <span class="admin-username">👤 <?= e($adminUser) ?></span>
            <a href="<?= BASE_URL ?>admin/logout.php" class="btn-admin btn-admin-danger btn-admin-sm" data-confirm="Are you sure you want to log out of the admin panel?">
                Log Out
            </a>
        </div>
    </div>
</header>

<div class="admin-container">
    <?= render_flash_messages() ?>
