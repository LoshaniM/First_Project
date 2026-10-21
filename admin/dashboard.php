<?php
/**
 * TRAVELMATE - Administrator Dashboard (FR-17)
 * Summary metrics, recently updated places, quick actions
 */

$adminTitle = "Dashboard Overview";
require_once __DIR__ . '/../includes/admin_header.php';

$totalPlaces = 0;
$totalCategories = 0;
$avgDistance = 0.0;
$recentPlaces = [];
$categoryStats = [];

try {
    $db = Database::getConnection();

    // Counts & Averages
    $totalPlaces = (int)$db->query("SELECT COUNT(*) FROM tourist_place")->fetchColumn();
    $totalCategories = (int)$db->query("SELECT COUNT(*) FROM category")->fetchColumn();
    $avgDistance = (float)$db->query("SELECT AVG(distance_km) FROM tourist_place")->fetchColumn();

    // Recently added or updated places
    $recentStmt = $db->query("
        SELECT tp.*, c.category_name 
        FROM tourist_place tp 
        JOIN category c ON tp.category_id = c.category_id 
        ORDER BY tp.updated_at DESC, tp.created_at DESC 
        LIMIT 5
    ");
    $recentPlaces = $recentStmt->fetchAll();

    // Category distribution
    $catStmt = $db->query("
        SELECT c.category_name, COUNT(tp.place_id) as count 
        FROM category c 
        LEFT JOIN tourist_place tp ON c.category_id = tp.category_id 
        GROUP BY c.category_id 
        ORDER BY count DESC
    ");
    $categoryStats = $catStmt->fetchAll();

} catch (PDOException $e) {
    error_log("Dashboard query error: " . $e->getMessage());
}
?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Dashboard Overview</h1>
        <p class="admin-page-subtitle">Welcome to the TRAVELMATE Rathnapura tourism control center</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?= BASE_URL ?>admin/place_add.php" class="btn-admin btn-admin-primary">
            + Add New Attraction
        </a>
        <a href="<?= BASE_URL ?>admin/categories.php" class="btn-admin btn-admin-secondary">
            Manage Categories
        </a>
    </div>
</div>

<!-- Metrics Overview Cards -->
<div class="metrics-grid">
    <div class="metric-card">
        <div>
            <div class="metric-title">Total Attractions</div>
            <div class="metric-val"><?= $totalPlaces ?></div>
            <div style="font-size: 0.8rem; color: var(--admin-muted); margin-top: 4px;">Within 25 km radius</div>
        </div>
        <div class="metric-icon">🏞️</div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-title">Categories</div>
            <div class="metric-val"><?= $totalCategories ?></div>
            <div style="font-size: 0.8rem; color: var(--admin-muted); margin-top: 4px;">Active tourism sectors</div>
        </div>
        <div class="metric-icon">📁</div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-title">Avg Distance</div>
            <div class="metric-val"><?= number_format($avgDistance, 1) ?> <span style="font-size: 1.1rem; font-weight: normal;">km</span></div>
            <div style="font-size: 0.8rem; color: var(--admin-muted); margin-top: 4px;">From Rathnapura Town</div>
        </div>
        <div class="metric-icon">📍</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">

    <!-- Recently Updated Places -->
    <div class="admin-card" style="margin-bottom: 0;">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Recently Added &amp; Updated Places</h2>
            <a href="<?= BASE_URL ?>admin/places.php" class="btn-admin btn-admin-secondary btn-admin-sm">
                View All Places &rarr;
            </a>
        </div>

        <?php if (empty($recentPlaces)): ?>
            <p style="color: var(--admin-muted); text-align: center; padding: 2rem 0;">No attractions recorded yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Attraction Name</th>
                            <th>Category</th>
                            <th>Distance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPlaces as $place): ?>
                            <?php $imgUrl = get_place_image_url($place['image_path']); ?>
                            <tr>
                                <td style="width: 70px;">
                                    <img src="<?= e($imgUrl) ?>" alt="<?= e($place['place_name']) ?>" class="table-thumb">
                                </td>
                                <td>
                                    <strong><?= e($place['place_name']) ?></strong><br>
                                    <small style="color: var(--admin-muted);"><?= e($place['location_text']) ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-default"><?= e($place['category_name']) ?></span>
                                </td>
                                <td>
                                    ~<?= number_format((float)$place['distance_km'], 1) ?> km
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= BASE_URL ?>admin/place_edit.php?id=<?= $place['place_id'] ?>" class="btn-admin btn-admin-secondary btn-admin-sm">
                                            Edit
                                        </a>
                                        <a href="<?= BASE_URL ?>place.php?id=<?= $place['place_id'] ?>" target="_blank" class="btn-admin btn-admin-secondary btn-admin-sm" title="Preview Public Page">
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Category Distribution -->
    <div class="admin-card" style="margin-bottom: 0;">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Category Breakdown</h2>
            <a href="<?= BASE_URL ?>admin/categories.php" class="btn-admin btn-admin-secondary btn-admin-sm">
                Manage
            </a>
        </div>

        <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.85rem;">
            <?php foreach ($categoryStats as $c): ?>
                <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.85rem; background: #f8fafc; border-radius: 6px; border: 1px solid var(--admin-border);">
                    <span style="font-weight: 600; font-size: 0.9rem;"><?= e($c['category_name']) ?></span>
                    <span class="badge badge-nature" style="font-size: 0.82rem; padding: 3px 10px;">
                        <?= (int)$c['count'] ?> Places
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>

        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--admin-border); text-align: center;">
            <a href="<?= BASE_URL ?>admin/place_add.php" class="btn-admin btn-admin-primary" style="width: 100%;">
                + Add Place to a Category
            </a>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
