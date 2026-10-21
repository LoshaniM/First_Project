<?php
/**
 * TRAVELMATE - One-Day Visit Plan Itinerary
 * Session-based plan viewer: Ordered stops, reordering, total estimated distance, route map
 */

$pageTitle = "My 1-Day Visit Itinerary";
require_once __DIR__ . '/includes/header.php';

$planPlaceIds = $_SESSION['day_plan'] ?? [];
$planPlaces = [];
$totalStops = count($planPlaceIds);

if (!empty($planPlaceIds)) {
    try {
        $db = Database::getConnection();

        // Fetch place details for all planned IDs
        $inClause = implode(',', array_fill(0, count($planPlaceIds), '?'));
        $stmt = $db->prepare("
            SELECT tp.*, c.category_name 
            FROM tourist_place tp 
            JOIN category c ON tp.category_id = c.category_id 
            WHERE tp.place_id IN ($inClause)
        ");
        $stmt->execute($planPlaceIds);
        $fetched = $stmt->fetchAll();

        // Index by place_id for preserving user-defined order
        $placesById = [];
        foreach ($fetched as $item) {
            $placesById[$item['place_id']] = $item;
        }

        // Rebuild in exact session order
        foreach ($planPlaceIds as $id) {
            if (isset($placesById[$id])) {
                $planPlaces[] = $placesById[$id];
            }
        }

    } catch (PDOException $e) {
        error_log("Error loading plan places: " . $e->getMessage());
    }
}

// Calculate cumulative approximate itinerary travel distance
$totalEstDistance = calculate_itinerary_distance($planPlaces);

// Prepare Leaflet map JSON data
$mapPlaces = array_map(function($p) {
    return [
        'place_id'      => (int)$p['place_id'],
        'place_name'    => $p['place_name'],
        'category_name' => $p['category_name'],
        'latitude'      => (float)$p['latitude'],
        'longitude'     => (float)$p['longitude'],
        'distance_km'   => (float)$p['distance_km'],
        'opening_time'  => $p['opening_time']
    ];
}, $planPlaces);
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">

    <!-- Plan Header -->
    <div class="section-header">
        <div>
            <h1 class="section-title">My 1-Day Rathnapura Itinerary</h1>
            <p class="section-subtitle">
                Organize your personalized day-trip schedule starting from Rathnapura Town Centre
            </p>
        </div>
        <?php if (!empty($planPlaces)): ?>
            <div style="display: flex; gap: 0.5rem;">
                <button onclick="window.print();" class="btn btn-outline btn-sm">
                    🖨️ Print Plan
                </button>
                <form action="<?= BASE_URL ?>plan_actions.php" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="clear">
                    <input type="hidden" name="return_url" value="<?= BASE_URL ?>plan.php">
                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to clear your entire day plan?">
                        Clear Plan
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php if (empty($planPlaces)): ?>
        <!-- Empty Plan State -->
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <h3>Your Day Plan is Empty</h3>
            <p>
                You haven't added any attractions to your 1-day itinerary yet. Browse through our curated attractions around Rathnapura and click <strong>"+ Add to Plan"</strong> to build your day trip!
            </p>
            <a href="<?= BASE_URL ?>places.php" class="btn btn-primary">
                Browse Rathnapura Attractions &rarr;
            </a>
        </div>
    <?php else: ?>

        <!-- Summary Banner (FR-15: Total Stops & Estimated Distance) -->
        <div class="plan-summary-banner">
            <div class="plan-stats-grid">
                <div class="plan-stat">
                    <span class="plan-stat-val"><?= $totalStops ?></span>
                    <span class="plan-stat-label">Planned <?= $totalStops === 1 ? 'Stop' : 'Stops' ?></span>
                </div>
                <div class="plan-stat" style="border-left: 1px solid rgba(255,255,255,0.25); padding-left: 2rem;">
                    <span class="plan-stat-val">~<?= number_format($totalEstDistance, 1) ?> km</span>
                    <span class="plan-stat-label">Estimated Total Route Distance *</span>
                </div>
            </div>

            <div style="text-align: right; font-size: 0.85rem; opacity: 0.9; max-width: 320px;">
                * Starting from Rathnapura Clock Tower, visiting each stop in order. Distances are approximate estimates.
            </div>
        </div>

        <!-- Interactive Route Map (FR-14) -->
        <div class="map-container-wrap">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <div style="font-weight: 700; color: var(--primary-dark); font-size: 0.95rem;">
                    🗺️ Itinerary Route Map &amp; Sequential Stop Order
                </div>
                <span style="font-size: 0.82rem; color: var(--dark-muted);">
                    Start: Town Centre &rarr; Follow numbered pins
                </span>
            </div>
            <div id="itinerary-map" class="map-container">
                <!-- Map initialized via map.js -->
            </div>
        </div>

        <!-- Ordered Itinerary Stop List (FR-13 & FR-16: Reordering and Removal) -->
        <h2 style="font-size: 1.4rem; color: var(--primary-dark); font-weight: 700; margin-bottom: 1.25rem;">
            Itinerary Schedule
        </h2>

        <div class="plan-list">
            <?php foreach ($planPlaces as $index => $place): ?>
                <?php 
                    $stopNum = $index + 1;
                    $isFirst = ($index === 0);
                    $isLast  = ($index === count($planPlaces) - 1);
                    $imgUrl  = get_place_image_url($place['image_path']);
                ?>
                <article class="plan-item-card">
                    <!-- Order number indicator -->
                    <div class="plan-order-badge" title="Stop Number <?= $stopNum ?>">
                        <?= $stopNum ?>
                    </div>

                    <!-- Thumbnail -->
                    <img src="<?= e($imgUrl) ?>" alt="<?= e($place['place_name']) ?>" class="plan-item-img">

                    <!-- Info -->
                    <div class="plan-item-details">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2px;">
                            <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; background: #e8f5e9; color: var(--primary-dark); padding: 1px 6px; border-radius: 4px;">
                                <?= e($place['category_name']) ?>
                            </span>
                            <span style="font-size: 0.82rem; color: var(--dark-muted);">
                                📍 ~<?= number_format((float)$place['distance_km'], 1) ?> km from town centre
                            </span>
                        </div>

                        <h3 class="plan-item-title">
                            <a href="<?= BASE_URL ?>place.php?id=<?= $place['place_id'] ?>">
                                <?= e($place['place_name']) ?>
                            </a>
                        </h3>

                        <div class="plan-item-meta">
                            <div><strong>🕒 Hours:</strong> <?= e($place['opening_time']) ?></div>
                            <div><strong>💡 Travel Tip:</strong> <?= e(mb_strimwidth($place['travel_tips'], 0, 90, '...')) ?></div>
                        </div>
                    </div>

                    <!-- Reorder (Move Up / Down) and Remove Controls (FR-16) -->
                    <div class="plan-item-controls">
                        <!-- Move Up -->
                        <form action="<?= BASE_URL ?>plan_actions.php" method="POST" style="display: inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="move_up">
                            <input type="hidden" name="place_id" value="<?= $place['place_id'] ?>">
                            <input type="hidden" name="return_url" value="<?= BASE_URL ?>plan.php">
                            <button type="submit" class="btn btn-outline btn-icon" <?= $isFirst ? 'disabled style="opacity:0.35; cursor:not-allowed;"' : '' ?> title="Move Up in Itinerary">
                                ▲ Up
                            </button>
                        </form>

                        <!-- Move Down -->
                        <form action="<?= BASE_URL ?>plan_actions.php" method="POST" style="display: inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="move_down">
                            <input type="hidden" name="place_id" value="<?= $place['place_id'] ?>">
                            <input type="hidden" name="return_url" value="<?= BASE_URL ?>plan.php">
                            <button type="submit" class="btn btn-outline btn-icon" <?= $isLast ? 'disabled style="opacity:0.35; cursor:not-allowed;"' : '' ?> title="Move Down in Itinerary">
                                ▼ Down
                            </button>
                        </form>

                        <!-- Remove -->
                        <form action="<?= BASE_URL ?>plan_actions.php" method="POST" style="display: inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="place_id" value="<?= $place['place_id'] ?>">
                            <input type="hidden" name="return_url" value="<?= BASE_URL ?>plan.php">
                            <button type="submit" class="btn btn-danger btn-icon" data-confirm="Remove <?= e($place['place_name']) ?> from your itinerary?" title="Remove from Itinerary">
                                &times; Remove
                            </button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <a href="<?= BASE_URL ?>places.php" class="btn btn-outline">
                + Add More Attractions
            </a>
            <div style="font-size: 0.85rem; color: var(--dark-muted);">
                Your itinerary is saved in your current session.
            </div>
        </div>

    <?php endif; ?>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const placesData = <?= json_encode($mapPlaces, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        initItineraryMap('itinerary-map', placesData);
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
