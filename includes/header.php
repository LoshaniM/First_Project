<?php
/**
 * TRAVELMATE - Tourist Public Header
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$currentFile = basename($_SERVER['PHP_SELF']);
$planCount = isset($_SESSION['day_plan']) && is_array($_SESSION['day_plan']) ? count($_SESSION['day_plan']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TRAVELMATE helps tourists and day-trippers discover attractions within a 25 km radius of Rathnapura town, Sri Lanka, and plan a seamless one-day visit.">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' . APP_NAME : APP_NAME . ' – Rathnapura Day-Visit Planner' ?></title>

    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <!-- App Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="<?= BASE_URL ?>index.php" class="brand" aria-label="TRAVELMATE Homepage">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            <span>TRAVELMATE</span>
            <span class="brand-badge">Rathnapura</span>
        </a>

        <button class="mobile-toggle" aria-label="Toggle navigation menu" aria-expanded="false">
            &#9776;
        </button>

        <nav>
            <ul class="nav-menu">
                <li>
                    <a href="<?= BASE_URL ?>index.php" class="nav-link <?= ($currentFile === 'index.php') ? 'active' : '' ?>">Home</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>places.php" class="nav-link <?= in_array($currentFile, ['places.php', 'place.php', 'place-details.php']) ? 'active' : '' ?>">Attractions</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>map.php" class="nav-link <?= ($currentFile === 'map.php') ? 'active' : '' ?>">Map</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>plan.php" class="nav-link <?= ($currentFile === 'plan.php') ? 'active' : '' ?>">
                        Day Plan <span class="plan-badge"><?= $planCount ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>notice.php" class="nav-link <?= ($currentFile === 'notice.php') ? 'active' : '' ?>">Notice</a>
                </li>
                <?php if (is_admin_logged_in()): ?>
                    <li>
                        <a href="<?= BASE_URL ?>admin/dashboard.php" class="btn btn-sm btn-primary" style="padding: 0.35rem 0.8rem;">
                            Admin Panel
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="<?= BASE_URL ?>admin/login.php" class="nav-link" style="color: var(--primary-light);">Admin Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main class="main-content">
    <div class="container" style="padding-top: 1.25rem;">
        <?= render_flash_messages() ?>
    </div>
