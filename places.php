<?php
/**
 * TRAVELMATE - Attractions Listing & Interactive Map
 * Category filtering, proximity display, Leaflet map overview
 */

$pageTitle = "Attractions Around Rathnapura";
require_once __DIR__ . '/includes/header.php';

$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$searchQuery = trim($_GET['q'] ?? '');

$categories = [];
$places = [];

try {
    $db = Database::getConnection();

    // Fetch all categories for filter pills
    $catStmt = $db->query("
        SELECT c.*, COUNT(tp.place_id) as count 
        FROM category c 
        LEFT JOIN tourist_place tp ON c.category_id = tp.category_id 
        GROUP BY c.category_id 
        ORDER BY c.category_name ASC
    ");
    $categories = $catStmt->fetchAll();

    // Build query with optional category and search filters
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

    if (!empty($searchQuery)) {
        $sql .= " AND (tp.place_name LIKE ? OR tp.description LIKE ? OR tp.location_text LIKE ?)";
        $wildcard = '%' . $searchQuery . '%';
        $params[] = $wildcard;
        $params[] = $wildcard;
        $params[] = $wildcard;
    }

    $sql .= " ORDER BY tp.distance_km ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $places = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Error fetching places: " . $e->getMessage());
}

// Prepare JSON payload for Leaflet map markers
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

<div class="container" style="padding-top: 2rem; padding-bottom: 3.5rem;">

    <!-- Breadcrumb & Header -->
    <div class="section-header">
        <div>
            <h1 class="section-title">Explore Rathnapura Attractions</h1>
            <p class="section-subtitle">
                Destinations within a 25 km radius of Rathnapura Town Centre (Clock Tower benchmark)
            </p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>plan.php" class="btn btn-primary btn-sm">
                View My Day Plan (<?= $planCount ?>) &rarr;
            </a>
        </div>
    </div>

    <!-- Category Filter Bar (FR-04 to FR-07) -->
    <div class="category-filter-bar" role="navigation" aria-label="Attraction categories">
        <a href="<?= BASE_URL ?>places.php" class="category-pill <?= ($selectedCategory === 0) ? 'active' : '' ?>">
            <span>All Categories</span>
            <span class="pill-count"><?= array_sum(array_column($categories, 'count')) ?></span>
        </a>

        <?php foreach ($categories as $cat): ?>
            <a href="<?= BASE_URL ?>places.php?category=<?= $cat['category_id'] ?>" 
               class="category-pill <?= ($selectedCategory === (int)$cat['category_id']) ? 'active' : '' ?>">
                <span><?= e($cat['category_name']) ?></span>
                <span class="pill-count"><?= (int)$cat['count'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Interactive Leaflet Map (FR-08 to FR-11) -->
    <div class="map-container-wrap">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <div style="font-weight: 700; color: var(--primary-dark); font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>🗺️ Interactive Attractions Map</span>
                <span style="font-weight: normal; font-size: 0.82rem; color: var(--dark-muted);">(Showing <?= count($places) ?> places)</span>
            </div>
            <span style="font-size: 0.8rem; color: var(--dark-muted);">Click markers for details</span>
        </div>
        <div id="all-places-map" class="map-container">
            <!-- Fallback rendered by map.js if offline or error -->
        </div>
    </div>

    <!-- Attractions Grid (FR-01 to FR-03) -->
    <?php if (empty($places)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🔍</div>
            <h3>No Attractions Found</h3>
            <p>
                <?php if ($selectedCategory > 0): ?>
                    There are currently no attractions matching the selected category.
                <?php else: ?>
                    No attractions matched your search criteria.
                <?php endif; ?>
            </p>
            <a href="<?= BASE_URL ?>places.php" class="btn btn-outline btn-sm">Reset Filter to Show All</a>
        </div>
    <?php else: ?>
        <div class="places-grid">
            <?php foreach ($places as $place): ?>
                <?php 
                    $inPlan = is_in_day_plan((int)$place['place_id']);
                    $imgUrl = get_place_image_url($place['image_path']);
                ?>
                <article class="place-card">
                    <div class="place-card-img-wrap">
                        <img src="<?= e($imgUrl) ?>" alt="<?= e($place['place_name']) ?>" class="place-card-img" loading="lazy">
                        <span class="place-category-badge"><?= e($place['category_name']) ?></span>
                        <span class="place-distance-badge">
                            📍 ~<?= number_format((float)$place['distance_km'], 1) ?> km
                        </span>
                    </div>

                    <div class="place-card-body">
                        <h3 class="place-card-title"><?= e($place['place_name']) ?></h3>
                        <p class="place-card-desc"><?= e($place['description']) ?></p>

                        <div class="place-card-meta">
                            <div class="meta-item">
                                <span>🕒</span>
                                <span><?= e($place['opening_time']) ?></span>
                            </div>
                            <div class="meta-item">
                                <span>📍</span>
                                <span><?= e($place['location_text']) ?></span>
                            </div>
                        </div>

                        <div class="place-card-actions">
                            <a href="<?= BASE_URL ?>place.php?id=<?= $place['place_id'] ?>" class="btn btn-outline btn-sm">
                                View Details
                            </a>

                            <?php if ($inPlan): ?>
                                <form action="<?= BASE_URL ?>plan_actions.php" method="POST" style="flex: 1;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="place_id" value="<?= $place['place_id'] ?>">
                                    <input type="hidden" name="return_url" value="<?= e($_SERVER['REQUEST_URI']) ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; color: #dc3545; border-color: #dc3545;" title="Remove from Day Plan">
                                        ✓ In Plan (Remove)
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="<?= BASE_URL ?>plan_actions.php" method="POST" style="flex: 1;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="place_id" value="<?= $place['place_id'] ?>">
                                    <input type="hidden" name="return_url" value="<?= e($_SERVER['REQUEST_URI']) ?>">
                                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                                        + Add to Plan
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<!-- Initialize All Places Leaflet Map with Data -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const placesData = <?= json_encode($mapPlaces, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        initAllPlacesMap('all-places-map', placesData);
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
