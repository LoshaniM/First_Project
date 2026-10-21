<?php
/**
 * TRAVELMATE - Interactive Map Explorer (FR-08, FR-09, FR-11)
 * Dedicated full map view of attractions within 25 km of Rathnapura
 */

$pageTitle = "Interactive Map of Rathnapura Attractions";
require_once __DIR__ . '/includes/header.php';

$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$categories = [];
$places = [];

try {
    $db = Database::getConnection();

    $catStmt = $db->query("
        SELECT c.*, COUNT(tp.place_id) as count 
        FROM category c 
        LEFT JOIN tourist_place tp ON c.category_id = tp.category_id 
        GROUP BY c.category_id 
        ORDER BY c.category_name ASC
    ");
    $categories = $catStmt->fetchAll();

    $sql = "
        SELECT tp.*, c.category_name 
        FROM tourist_place tp 
        JOIN category c ON tp.category_id = c.category_id 
        WHERE 1=1
    ";
    $params = [];

    if ($selectedCategory > 0) {
        $sql .= " AND tp.category_id = ?";
        $params[] = $selectedCategory;
    }

    $sql .= " ORDER BY tp.distance_km ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $places = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Map page query error: " . $e->getMessage());
}

$mapPlaces = array_map(function($p) {
    return [
        'place_id'      => (int)$p['place_id'],
        'place_name'    => $p['place_name'],
        'category_name' => $p['category_name'],
        'latitude'      => (float)$p['latitude'],
        'longitude'     => (float)$p['longitude'],
        'distance_km'   => (float)$p['distance_km'],
        'location_text' => $p['location_text'],
        'opening_time'  => $p['opening_time']
    ];
}, $places);
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">

    <div class="section-header">
        <div>
            <h1 class="section-title">Rathnapura Attractions Map</h1>
            <p class="section-subtitle">
                Interactive geographic distribution within a 25 km radius of Rathnapura Town Centre
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?= BASE_URL ?>places.php" class="btn btn-outline btn-sm">
                📋 Switch to Card View
            </a>
            <a href="<?= BASE_URL ?>plan.php" class="btn btn-primary btn-sm">
                My Plan (<?= $planCount ?>)
            </a>
        </div>
    </div>

    <!-- Category filter buttons -->
    <div class="category-filter-bar" style="margin-bottom: 1.25rem;">
        <a href="<?= BASE_URL ?>map.php" class="category-pill <?= ($selectedCategory === 0) ? 'active' : '' ?>">
            <span>All Categories</span>
            <span class="pill-count"><?= array_sum(array_column($categories, 'count')) ?></span>
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= BASE_URL ?>map.php?category=<?= $cat['category_id'] ?>" 
               class="category-pill <?= ($selectedCategory === (int)$cat['category_id']) ? 'active' : '' ?>">
                <span><?= e($cat['category_name']) ?></span>
                <span class="pill-count"><?= (int)$cat['count'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Map container -->
    <div class="map-container-wrap">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <div style="font-weight: 700; color: var(--primary-dark); font-size: 0.95rem;">
                📍 Rathnapura Town Centre Starting Benchmark (Green Pin) &bull; <?= count($places) ?> Mapped Places
            </div>
            <span style="font-size: 0.82rem; color: var(--dark-muted);">Click on any pin to view details</span>
        </div>
        <div id="full-screen-map" class="map-container" style="height: 580px;">
            <!-- Map rendered via JavaScript -->
        </div>
    </div>

    <!-- Map Legend -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: center; font-size: 0.88rem; color: var(--dark-muted);">
        <div><strong style="color: var(--dark);">Map Legend:</strong></div>
        <div style="display: flex; align-items: center; gap: 6px;">
            <span style="background: #0f5132; color: #fff; width: 18px; height: 18px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 10px;">📍</span>
            <span>Rathnapura Clock Tower (Origin)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px;">
            <span style="background: #198754; color: #fff; width: 16px; height: 16px; border-radius: 50%; display: inline-block;"></span>
            <span>Attraction Location</span>
        </div>
        <div style="margin-left: auto; font-size: 0.82rem;">
            <em>All plotted places are within 25 km of the town centre</em>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const placesData = <?= json_encode($mapPlaces, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        initAllPlacesMap('full-screen-map', placesData);
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
