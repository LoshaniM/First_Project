<?php
/**
 * TRAVELMATE - Attraction Details Page (FR-02, FR-03, FR-10, FR-11)
 * Rich description, category, distance, opening times, travel tips, and dedicated interactive map
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$placeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$place = get_place_by_id($placeId);

if (!$place) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

$pageTitle = $place['place_name'];
require_once __DIR__ . '/includes/header.php';

$inPlan = is_in_day_plan((int)$place['place_id']);
$imgUrl = get_place_image_url($place['image_path']);

// Single map JS payload
$mapPlace = [
    'place_id'      => (int)$place['place_id'],
    'place_name'    => $place['place_name'],
    'category_name' => $place['category_name'],
    'latitude'      => (float)$place['latitude'],
    'longitude'     => (float)$place['longitude'],
    'distance_km'   => (float)$place['distance_km'],
    'location_text' => $place['location_text']
];
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">

    <!-- Back to listing navigation -->
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <a href="<?= BASE_URL ?>places.php" class="btn btn-outline btn-sm">
            &larr; Back to Attractions List
        </a>
        <a href="<?= BASE_URL ?>map.php" class="btn btn-secondary btn-sm">
            🗺️ View Full Rathnapura Map
        </a>
    </div>

    <div class="place-details-layout">

        <!-- Main Column: Details, Photos, Descriptions, Tips -->
        <article class="details-main">
            <div class="details-header">
                <span class="category-pill active" style="display: inline-flex; margin-bottom: 0.5rem;">
                    <?= e($place['category_name']) ?>
                </span>
                <h1 class="details-title"><?= e($place['place_name']) ?></h1>
                <p style="color: var(--dark-muted); font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>📍</span>
                    <span><?= e($place['location_text']) ?></span>
                </p>
            </div>

            <!-- Hero Image -->
            <img src="<?= e($imgUrl) ?>" alt="<?= e($place['place_name']) ?>" class="details-image-hero">

            <!-- Fast Information Bar -->
            <div class="details-info-box">
                <div class="info-box-item">
                    <span class="info-box-label">Distance from Town</span>
                    <span class="info-box-val">~<?= number_format((float)$place['distance_km'], 1) ?> km</span>
                </div>
                <div class="info-box-item">
                    <span class="info-box-label">Opening Schedule</span>
                    <span class="info-box-val"><?= e($place['opening_time']) ?></span>
                </div>
                <div class="info-box-item">
                    <span class="info-box-label">GPS Coordinates</span>
                    <span class="info-box-val" style="font-size: 0.88rem; font-family: monospace;">
                        <?= number_format((float)$place['latitude'], 4) ?>° N, <?= number_format((float)$place['longitude'], 4) ?>° E
                    </span>
                </div>
            </div>

            <!-- Description -->
            <h2 class="details-section-title">
                <span>📖</span> About This Destination
            </h2>
            <div style="font-size: 1.05rem; line-height: 1.8; color: #334155; margin-bottom: 2rem;">
                <?= nl2br(e($place['description'])) ?>
            </div>

            <!-- Travel Tips -->
            <div class="travel-tips-box">
                <strong>💡 Visitor &amp; Travel Tips:</strong>
                <p style="margin: 0; line-height: 1.6; color: #4b3621;">
                    <?= nl2br(e($place['travel_tips'])) ?>
                </p>
            </div>

            <!-- Embedded Interactive Map (FR-08 to FR-11) -->
            <h2 class="details-section-title">
                <span>🗺️</span> Location &amp; Route from Rathnapura Town
            </h2>
            <p style="font-size: 0.88rem; color: var(--dark-muted); margin-bottom: 0.75rem;">
                Showing relative position from Rathnapura Clock Tower (zero-km starting benchmark)
            </p>
            <div id="single-place-map" class="map-container map-container-sm" style="border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <!-- Map rendered via JavaScript with offline fallback -->
            </div>
        </article>

        <!-- Sidebar Column: Action Cards -->
        <aside class="details-sidebar">
            <div class="sidebar-card">
                <h3 class="sidebar-title">1-Day Visit Itinerary</h3>
                <p style="font-size: 0.9rem; color: var(--dark-muted); margin-bottom: 1.25rem;">
                    Add this attraction to your personal day plan to track your stops, calculate total travel distance, and organize your route.
                </p>

                <?php if ($inPlan): ?>
                    <form action="<?= BASE_URL ?>plan_actions.php" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="place_id" value="<?= $place['place_id'] ?>">
                        <input type="hidden" name="return_url" value="<?= e($_SERVER['REQUEST_URI']) ?>">
                        <button type="submit" class="btn btn-secondary" style="width: 100%; color: #dc3545; border-color: #dc3545; margin-bottom: 0.75rem;">
                            ✓ Remove from My Plan
                        </button>
                    </form>
                    <a href="<?= BASE_URL ?>plan.php" class="btn btn-primary" style="width: 100%;">
                        View Full Plan (<?= $planCount ?> Stops) &rarr;
                    </a>
                <?php else: ?>
                    <form action="<?= BASE_URL ?>plan_actions.php" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="place_id" value="<?= $place['place_id'] ?>">
                        <input type="hidden" name="return_url" value="<?= e($_SERVER['REQUEST_URI']) ?>">
                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 0.75rem;">
                            + Add to My Day Plan
                        </button>
                    </form>
                    <a href="<?= BASE_URL ?>plan.php" class="btn btn-outline" style="width: 100%;">
                        View Existing Plan (<?= $planCount ?>)
                    </a>
                <?php endif; ?>
            </div>

            <div class="sidebar-card">
                <h3 class="sidebar-title">Essential Quick Info</h3>
                <ul style="list-style: none; font-size: 0.88rem; color: var(--dark-muted); display: flex; flex-direction: column; gap: 0.85rem;">
                    <li style="display: flex; gap: 0.5rem;">
                        <span>🏛️</span>
                        <div><strong>Category:</strong> <?= e($place['category_name']) ?></div>
                    </li>
                    <li style="display: flex; gap: 0.5rem;">
                        <span>🚗</span>
                        <div><strong>Proximity:</strong> ~<?= number_format((float)$place['distance_km'], 1) ?> km from town centre</div>
                    </li>
                    <li style="display: flex; gap: 0.5rem;">
                        <span>⏰</span>
                        <div><strong>Hours:</strong> <?= e($place['opening_time']) ?></div>
                    </li>
                    <li style="display: flex; gap: 0.5rem;">
                        <span>ℹ️</span>
                        <div><strong>Notice:</strong> <?= APP_DISCLAIMER ?></div>
                    </li>
                </ul>
            </div>
        </aside>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const placeData = <?= json_encode($mapPlace, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        initSinglePlaceMap('single-place-map', placeData);
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
