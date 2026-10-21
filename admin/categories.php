<?php
/**
 * TRAVELMATE - Category Management (FR-22 & FR-23)
 * List, Add, Edit categories with foreign key check before deletion
 */

$adminTitle = "Category Management";
require_once __DIR__ . '/../includes/admin_header.php';

$errors = [];
$editCategory = null;
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;

$db = Database::getConnection();

// Handle Edit Mode Load
if ($editId > 0) {
    $editStmt = $db->prepare("SELECT * FROM category WHERE category_id = ? LIMIT 1");
    $editStmt->execute([$editId]);
    $editCategory = $editStmt->fetch();
}

// Handle Form Submission (Add or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();

    $action       = $_POST['form_action'] ?? 'add';
    $catId        = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $categoryName = trim($_POST['category_name'] ?? '');
    $description  = trim($_POST['description'] ?? '');

    if (empty($categoryName)) {
        $errors[] = 'Category Name is required.';
    } elseif (mb_strlen($categoryName) > 50) {
        $errors[] = 'Category Name cannot exceed 50 characters.';
    }

    if (empty($errors)) {
        try {
            if ($action === 'edit' && $catId > 0) {
                // Check uniqueness excluding current ID
                $checkStmt = $db->prepare("SELECT COUNT(*) FROM category WHERE category_name = ? AND category_id != ?");
                $checkStmt->execute([$categoryName, $catId]);
                if ($checkStmt->fetchColumn() > 0) {
                    $errors[] = 'Another category with this name already exists.';
                } else {
                    $upStmt = $db->prepare("UPDATE category SET category_name = ?, description = ? WHERE category_id = ?");
                    $upStmt->execute([$categoryName, $description, $catId]);
                    set_flash('success', 'Category "' . $categoryName . '" updated successfully!');
                    header("Location: " . BASE_URL . "admin/categories.php");
                    exit;
                }
            } else {
                // Add new category
                $checkStmt = $db->prepare("SELECT COUNT(*) FROM category WHERE category_name = ?");
                $checkStmt->execute([$categoryName]);
                if ($checkStmt->fetchColumn() > 0) {
                    $errors[] = 'A category with this name already exists.';
                } else {
                    $inStmt = $db->prepare("INSERT INTO category (category_name, description, created_at) VALUES (?, ?, NOW())");
                    $inStmt->execute([$categoryName, $description]);
                    set_flash('success', 'Category "' . $categoryName . '" created successfully!');
                    header("Location: " . BASE_URL . "admin/categories.php");
                    exit;
                }
            }
        } catch (PDOException $e) {
            error_log("Category save error: " . $e->getMessage());
            $errors[] = 'Database error while saving the category.';
        }
    }
}

// Fetch all categories with associated attraction count
$catList = [];
try {
    $listStmt = $db->query("
        SELECT c.*, COUNT(tp.place_id) as place_count 
        FROM category c 
        LEFT JOIN tourist_place tp ON c.category_id = tp.category_id 
        GROUP BY c.category_id 
        ORDER BY c.category_name ASC
    ");
    $catList = $listStmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error listing categories: " . $e->getMessage());
}
?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Attraction Categories</h1>
        <p class="admin-page-subtitle">Manage classification categories for Rathnapura tourist places</p>
    </div>
    <?php if ($editCategory): ?>
        <a href="<?= BASE_URL ?>admin/categories.php" class="btn-admin btn-admin-secondary">
            + Add New Category Instead
        </a>
    <?php endif; ?>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger" role="alert">
        <div>
            <strong>Error:</strong>
            <ul style="margin: 0.35rem 0 0 1.25rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-bottom: 2rem;">

    <!-- Add / Edit Form Card -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">
                <?= $editCategory ? 'Edit Category' : 'Add New Category' ?>
            </h2>
        </div>

        <form action="<?= BASE_URL ?>admin/categories.php<?= $editCategory ? '?edit=' . $editCategory['category_id'] : '' ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="form_action" value="<?= $editCategory ? 'edit' : 'add' ?>">
            <?php if ($editCategory): ?>
                <input type="hidden" name="category_id" value="<?= $editCategory['category_id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="category_name" class="form-label">Category Name <span class="required">*</span></label>
                <input type="text" id="category_name" name="category_name" class="form-control" required value="<?= e($editCategory['category_name'] ?? $_POST['category_name'] ?? '') ?>" placeholder="e.g. Eco Tourism">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description (Optional)</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief explanation of this attraction category..."><?= e($editCategory['description'] ?? $_POST['description'] ?? '') ?></textarea>
            </div>

            <div style="margin-top: 1.25rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn-admin btn-admin-primary" style="flex: 1;">
                    <?= $editCategory ? 'Update Category' : 'Create Category' ?>
                </button>
                <?php if ($editCategory): ?>
                    <a href="<?= BASE_URL ?>admin/categories.php" class="btn-admin btn-admin-secondary">
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Category List Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Existing Categories (<?= count($catList) ?>)</h2>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Attractions</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($catList as $cat): ?>
                        <?php 
                            $placeCount = (int)$cat['place_count']; 
                            $isInUse = ($placeCount > 0);
                        ?>
                        <tr>
                            <td>
                                <strong><?= e($cat['category_name']) ?></strong>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--admin-muted);">
                                <?= e($cat['description'] ?? '—') ?>
                            </td>
                            <td>
                                <span class="badge <?= $isInUse ? 'badge-nature' : 'badge-default' ?>">
                                    <?= $placeCount ?> Places
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div class="btn-group">
                                    <a href="<?= BASE_URL ?>admin/categories.php?edit=<?= $cat['category_id'] ?>" class="btn-admin btn-admin-secondary btn-admin-sm">
                                        Edit
                                    </a>

                                    <?php if ($isInUse): ?>
                                        <!-- Blocked from deleting because attractions depend on it -->
                                        <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" style="opacity: 0.5; cursor: not-allowed;" title="Cannot delete: <?= $placeCount ?> attractions use this category.">
                                            In Use
                                        </button>
                                    <?php else: ?>
                                        <form action="<?= BASE_URL ?>admin/category_delete.php" method="POST" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                                            <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm" data-confirm="Delete category '<?= e(addslashes($cat['category_name'])) ?>'?">
                                                Delete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
