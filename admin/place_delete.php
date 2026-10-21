<?php
/**
 * TRAVELMATE - Delete Tourist Place Controller (FR-19)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Enforce admin login
require_admin();

// Enforce CSRF token
enforce_csrf();

$placeId = isset($_POST['place_id']) ? (int)$_POST['place_id'] : 0;

if ($placeId > 0) {
    try {
        $db = Database::getConnection();

        // Retrieve existing place record
        $stmt = $db->prepare("SELECT * FROM tourist_place WHERE place_id = ? LIMIT 1");
        $stmt->execute([$placeId]);
        $place = $stmt->fetch();

        if ($place) {
            // Delete record
            $delStmt = $db->prepare("DELETE FROM tourist_place WHERE place_id = ?");
            $delStmt->execute([$placeId]);

            // If an uploaded image was used (not default), optionally delete the file
            if (!empty($place['image_path']) && strpos($place['image_path'], 'uploads/') !== false) {
                $filePath = dirname(__DIR__) . '/' . ltrim($place['image_path'], '/');
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // Remove from active tourist plan session if present
            if (isset($_SESSION['day_plan']) && is_array($_SESSION['day_plan'])) {
                $idx = array_search($placeId, $_SESSION['day_plan']);
                if ($idx !== false) {
                    unset($_SESSION['day_plan'][$idx]);
                    $_SESSION['day_plan'] = array_values($_SESSION['day_plan']);
                }
            }

            set_flash('success', 'Attraction "' . $place['place_name'] . '" was permanently deleted.');
        } else {
            set_flash('error', 'Attraction not found.');
        }

    } catch (PDOException $e) {
        error_log("Delete place error: " . $e->getMessage());
        set_flash('error', 'A database error occurred while trying to delete this attraction.');
    }
} else {
    set_flash('error', 'Invalid attraction ID specified.');
}

header("Location: " . BASE_URL . "admin/places.php");
exit;
