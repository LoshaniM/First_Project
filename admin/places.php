<?php
/**
 * TRAVELMATE - Admin Places Management & Search (FR-17 to FR-23)
 */

$adminTitle = "Manage Tourist Attractions";
require_once __DIR__ . '/../includes/admin_header.php';

$search = trim($_GET['search'] ?? '');
$filterCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$categories = get_all_categories();
$places = [];

try {
    $db = Database::getConnection();

    $sql = "
        SELECT tp.*, c.category_name 
        FROM tourist_place tp 
        JOIN category c ON tp.category_id = c.category_id 
        WHERE 1=1
    ";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (tp.place_name LIKE ? OR tp.location_text LIKE ? OR tp.description LIKE ?)";
        $wildcard = '%' . $search . '%';
        $params[] = $wildcard;
        $params[] = $wildcard;
        $params[] = $wildcard;
    }

    if ($filterCategory > 0) {
        $sql .= " AND tp.category_id = ?";
        $params[] = $filterCategory;
    }

    $sql .= " ORDER BY tp.distance_km ASC, tp.place_name ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $places = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Admin places error: " . $e->getMessage());
}
?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Tourist Attractions (<?= count($places) ?>)</h1>
        <p class="admin-page-subtitle">Manage attractions within 25 km of Rathnapura town centre</p>
    </div>
    <a href="<?= BASE_URL ?>admin/place_add.php" class="btn-admin btn-admin-primary">
        + Add New Attraction
    </a>
</div>

<!-- Search & Filter Bar -->
<div class="admin-card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
    <form action="<?= BASE_URL ?>admin/places.php" method="GET" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <div style="flex-grow: 1; min-width: 250px;">
            <input type="text" name="search" class="form-control" placeholder="Search by name, location or keywords..." value="<?= e($search) ?>">
        </div>

        <div style="width: 200px;">
            <select name="category" class="form-control">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>" <?= ($filterCategory === (int)$cat['category_id']) ? 'selected' : '' ?>>
                        <?= e($cat['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn-admin btn-admin-primary">
                Filter
            </button>
            <?php if (!empty($search) || $filterCategory > 0): ?>
                <a href="<?= BASE_URL ?>admin/places.php" class="btn-admin btn-admin-secondary">
                    Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Places Table -->
<div class="admin-card">
    <?php if (empty($places)): ?>
        <p style="color: var(--admin-muted); text-align: center; padding: 2.5rem 0;">
            No attractions found matching the criteria.
        </p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Photo</th>
                        <th>Attraction &amp; Location</th>
                        <th>Category</th>
                        <th>Distance</th>
                        <th>Opening Schedule</th>
                        <th>Coordinates</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($places as $place): ?>
                        <?php $imgUrl = get_place_image_url($place['image_path']); ?>
                        <tr>
                            <td>
                                <img src="<?= e($imgUrl) ?>" alt="<?= e($place['place_name']) ?>" class="table-thumb">
                            </td>
                            <td>
                                <strong><?= e($place['place_name']) ?></strong>
                                <div style="font-size: 0.82rem; color: var(--admin-muted); margin-top: 2px;">
                                    📍 <?= e($place['location_text']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-default"><?= e($place['category_name']) ?></span>
                            </td>
                            <td>
                                <strong style="color: var(--admin-primary);"><?= number_format((float)$place['distance_km'], 1) ?> km</strong>
                            </td>
                            <td style="font-size: 0.85rem;">
                                <?= e($place['opening_time']) ?>
                            </td>
                            <td style="font-size: 0.8rem; font-family: monospace; color: var(--admin-muted);">
                                <?= number_format((float)$place['latitude'], 4) ?>, <?= number_format((float)$place['longitude'], 4) ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="btn-group">
                                    <a href="<?= BASE_URL ?>place.php?id=<?= $place['place_id'] ?>" target="_blank" class="btn-admin btn-admin-secondary btn-admin-sm" title="Preview Public Page">
                                        View
                                    </a>
                                    <a href="<?= BASE_URL ?>admin/place_edit.php?id=<?= $place['place_id'] ?>" class="btn-admin btn-admin-primary btn-admin-sm">
                                        Edit
                                    </a>
                                    <form action="<?= BASE_URL ?>admin/place_delete.php" method="POST" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="place_id" value="<?= $place['place_id'] ?>">
                                        <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm" data-confirm="Are you sure you want to permanently delete '<?= e(addslashes($place['place_name'])) ?>'?">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
