<?php
/**
 * TRAVELMATE - Home Page
 * Local Tourist Day-Visit Planner and Information System (Rathnapura)
 */

$pageTitle = "Discover & Plan Your Day-Visit to Rathnapura";
require_once __DIR__ . '/includes/header.php';

// Fetch categories with counts
$categories = [];
$featuredPlaces = [];
$totalPlaces = 0;

try {
    $db = Database::getConnection();

    // Get categories with place counts
    $catQuery = "
        SELECT c.*, COUNT(tp.place_id) as place_count 
        FROM category c 
        LEFT JOIN tourist_place tp ON c.category_id = tp.category_id 
        GROUP BY c.category_id 
        ORDER BY c.category_name ASC
    ";
    $categories = $db->query($catQuery)->fetchAll();

    // Get 6 featured/top attractions
    $placesQuery = "
        SELECT tp.*, c.category_name 
        FROM tourist_place tp 
        JOIN category c ON tp.category_id = c.category_id 
        ORDER BY tp.distance_km ASC 
        LIMIT 6
    ";
    $featuredPlaces = $db->query($placesQuery)->fetchAll();

    $countStmt = $db->query("SELECT COUNT(*) FROM tourist_place");
    $totalPlaces = (int)$countStmt->fetchColumn();

} catch (PDOException $e) {
    error_log("Database query error on home page: " . $e->getMessage());
}
?>

<!-- Hero Banner -->
<section class="hero">
    <div class="container">
        <h1>Discover Rathnapura in a Single Day</h1>
        <p>
            Explore breathtaking waterfalls, sacred ancient shrines, legendary gem heritage, and authentic Sri Lankan dining — all within a 25 km radius of Rathnapura town centre.
        </p>
        <div class="hero-actions">
            <a href="<?= BASE_URL ?>places.php" class="btn btn-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                Explore <?= $totalPlaces ?> Attractions
            </a>
            <a href="<?= BASE_URL ?>plan.php" class="btn btn-outline" style="border-color: #ffffff; color: #ffffff;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
                View 1-Day Itinerary Plan (<?= $planCount ?>)
            </a>
        </div>
    </div>
</section>

<div class="container" style="padding-top: 3rem; padding-bottom: 3rem;">

    <!-- Categories Section -->
    <section style="margin-bottom: 3.5rem;">
        <div class="section-header">
            <div>
                <h2 class="section-title">Browse by Experience</h2>
                <p class="section-subtitle">Select a category to discover curated destinations around Rathnapura</p>
            </div>
            <a href="<?= BASE_URL ?>places.php" class="btn btn-outline btn-sm">View All &rarr;</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
            <?php foreach ($categories as $cat): ?>
                <a href="<?= BASE_URL ?>places.php?category=<?= $cat['category_id'] ?>" class="place-card" style="padding: 1.5rem; text-decoration: none; border-top: 4px solid var(--primary-light);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                        <h3 style="font-size: 1.2rem; color: var(--primary-dark); font-weight: 700;">
                            <?= e($cat['category_name']) ?>
                        </h3>
                        <span class="pill-count" style="background: #e8f5e9; color: var(--primary-dark); font-weight: 700; padding: 4px 9px;">
                            <?= (int)$cat['place_count'] ?> Places
                        </span>
                    </div>
                    <p style="font-size: 0.88rem; color: var(--dark-muted); line-height: 1.5; margin-bottom: 0.75rem;">
                        <?= e($cat['description'] ?? 'Curated places in this category.') ?>
                    </p>
                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary-light); margin-top: auto;">
                        Explore <?= e($cat['category_name']) ?> &rarr;
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Featured Attractions -->
    <section>
        <div class="section-header">
            <div>
                <h2 class="section-title">Closest Highlights to Rathnapura Town</h2>
                <p class="section-subtitle">Attractions sorted by proximity from the town clock tower (25 km radius)</p>
            </div>
            <a href="<?= BASE_URL ?>places.php" class="btn btn-primary btn-sm">Browse All Attractions</a>
        </div>

        <?php if (empty($featuredPlaces)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🏞️</div>
                <h3>No Attractions Available</h3>
                <p>No tourist attractions have been registered in the system yet. Please seed the database or check back soon.</p>
            </div>
        <?php else: ?>
            <div class="places-grid">
                <?php foreach ($featuredPlaces as $place): ?>
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
                                    <span>📌</span>
                                    <span><?= e($place['location_text']) ?></span>
                                </div>
                            </div>

                            <div class="place-card-actions">
                                <a href="<?= BASE_URL ?>place.php?id=<?= $place['place_id'] ?>" class="btn btn-outline btn-sm">
                                    Details
                                </a>

                                <?php if ($inPlan): ?>
                                    <form action="<?= BASE_URL ?>plan_actions.php" method="POST" style="flex: 1;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="place_id" value="<?= $place['place_id'] ?>">
                                        <input type="hidden" name="return_url" value="<?= e($_SERVER['REQUEST_URI']) ?>">
                                        <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; color: #dc3545; border-color: #dc3545;" title="Remove from Day Plan">
                                            ✓ In Plan
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
    </section>

    <!-- Itinerary Banner -->
    <section class="plan-summary-banner" style="margin-top: 1rem;">
        <div>
            <h3 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 0.5rem;">Plan Your Day Itinerary</h3>
            <p style="font-size: 0.95rem; opacity: 0.95; max-width: 580px; margin: 0;">
                No registration or app download needed. Simply add attractions as you browse, reorder stops, view estimated travel distance, and follow your customized route map!
            </p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>plan.php" class="btn btn-secondary" style="font-size: 1rem; padding: 0.8rem 1.75rem;">
                Open Itinerary (<?= $planCount ?> <?= $planCount === 1 ? 'Stop' : 'Stops' ?>) &rarr;
            </a>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<! --Viva test-->