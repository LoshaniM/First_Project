<?php
/**
 * TRAVELMATE - PDO Database Connection (includes/db.php)
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    /**
     * Get singleton PDO connection instance
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log("Database Connection Error: " . $e->getMessage());

                // Friendly setup fallback screen
                http_response_code(503);
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Database Setup Notice - TRAVELMATE</title>
                    <style>
                        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f4fbf7; color: #1e3a2f; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
                        .card { background: #ffffff; padding: 2.5rem; border-radius: 14px; box-shadow: 0 10px 25px rgba(15, 81, 50, 0.1); max-width: 580px; width: 100%; border-top: 5px solid #198754; text-align: center; }
                        h1 { color: #0f5132; margin-top: 0; font-size: 1.6rem; }
                        p { line-height: 1.6; color: #495057; font-size: 0.95rem; }
                        .instructions { background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 8px; padding: 1rem; text-align: left; font-size: 0.85rem; margin-top: 1.5rem; }
                        code { background: #263238; color: #a5d6a7; padding: 2px 6px; border-radius: 4px; font-family: Consolas, monospace; }
                    </style>
                </head>
                <body>
                    <div class="card">
                        <h1>TRAVELMATE Database Setup Required</h1>
                        <p>TRAVELMATE is unable to connect to the MySQL database. Please ensure your MySQL service in XAMPP or WAMP is running and the database has been imported.</p>
                        
                        <div class="instructions">
                            <strong>Quick Setup Guide:</strong>
                            <ol style="margin-top: 8px; padding-left: 20px; line-height: 1.7;">
                                <li>Open XAMPP / WAMP Control Panel and start <b>MySQL</b>.</li>
                                <li>Open <code>http://localhost/phpmyadmin/</code></li>
                                <li>Import <code>database/schema.sql</code>, then <code>database/seed.sql</code>.</li>
                                <li>If needed, update credentials in <code>includes/config.php</code>.</li>
                            </ol>
                        </div>
                    </div>
                </body>
                </html>
                <?php
                exit;
            }
        }

        return self::$instance;
    }
}
