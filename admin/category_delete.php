<?php
/**
 * TRAVELMATE - Safe Category Deletion Handler (FR-23)
 * Strictly prevents deleting categories that have attached attractions
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();
enforce_csrf();

$categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;

if ($categoryId > 0) {
    try {
        $db = Database::getConnection();

        // 1. Fetch category details
        $stmt = $db->prepare("SELECT * FROM category WHERE category_id = ? LIMIT 1");
        $stmt->execute([$categoryId]);
        $cat = $stmt->fetch();

        if ($cat) {
            // 2. Check if any attractions currently classify under this category
            $countStmt = $db->prepare("SELECT COUNT(*) FROM tourist_place WHERE category_id = ?");
            $countStmt->execute([$categoryId]);
            $usedCount = (int)$countStmt->fetchColumn();

            if ($usedCount > 0) {
                // Deny deletion: foreign key in use
                set_flash('error', 'Cannot delete category "' . $cat['category_name'] . '" because it is currently assigned to ' . $usedCount . ' attraction(s). Please reassign or delete those attractions first.');
            } else {
                // Safe to delete
                $delStmt = $db->prepare("DELETE FROM category WHERE category_id = ?");
                $delStmt->execute([$categoryId]);
                set_flash('success', 'Category "' . $cat['category_name'] . '" was successfully removed.');
            }
        } else {
            set_flash('error', 'Category not found.');
        }

    } catch (PDOException $e) {
        error_log("Delete category error: " . $e->getMessage());
        set_flash('error', 'A database constraint prevented deleting this category.');
    }
} else {
    set_flash('error', 'Invalid category ID.');
}

header("Location: " . BASE_URL . "admin/categories.php");
exit;
