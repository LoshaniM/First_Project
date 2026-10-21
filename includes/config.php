<?php
/**
 * TRAVELMATE - Application Configuration
 * Local Tourist Day-Visit Planner and Information System (Rathnapura)
 */

// Timezone for Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Error reporting settings (Log privately, avoid showing raw errors in output)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Database Credentials
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'travelmate_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

// Application Details
define('APP_NAME', 'TRAVELMATE');
define('APP_TAGLINE', 'Rathnapura Day-Visit Planner & Local Tourism Guide');
define('TOWN_CENTRE_NAME', 'Rathnapura Town Clock Tower');
define('TOWN_CENTRE_LAT', 6.6828000);
define('TOWN_CENTRE_LNG', 80.4034000);
define('MAX_RADIUS_KM', 25.0);

// Mandatory Tourist Information Notice (NFR-05 & NFR-06)
define('APP_DISCLAIMER', 'Distances, opening times and travel tips are approximate and may require verification before your visit.');

// Dynamic Base URL Detection for zero-config XAMPP / WAMP portability
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

$docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
$appDir  = str_replace('\\', '/', dirname(__DIR__));
$subPath = '';

if (!empty($docRoot) && strpos($appDir, $docRoot) === 0) {
    $subPath = substr($appDir, strlen($docRoot));
}
$subPath = '/' . ltrim(rtrim($subPath, '/'), '/');
if ($subPath === '/') {
    $subPath = '';
}

define('BASE_URL', rtrim($protocol . $host . $subPath, '/') . '/');

// Upload directory paths
define('UPLOAD_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
define('UPLOAD_URL', BASE_URL . 'uploads/');
define('MAX_UPLOAD_SIZE', 3 * 1024 * 1024); // 3 MB max image upload limit

// Secure Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    $cookieParams = [
        'lifetime' => 0,
        'path' => empty($subPath) ? '/' : $subPath,
        'domain' => '',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ];
    session_set_cookie_params($cookieParams);
    session_start();
}

// Initialize visitor's day plan list in session if not set
if (!isset($_SESSION['day_plan']) || !is_array($_SESSION['day_plan'])) {
    $_SESSION['day_plan'] = [];
}
