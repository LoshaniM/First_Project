<?php
/**
 * TRAVELMATE - Custom 404 Error Page
 */

$pageTitle = "Page or Attraction Not Found (404)";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 4rem; padding-bottom: 5rem;">
    <div class="empty-state" style="max-width: 580px; margin: 0 auto;">
        <div class="empty-state-icon" style="font-size: 4rem;">🧭</div>
        <h1 style="font-size: 2rem; color: var(--primary-dark); margin-bottom: 0.75rem;">Page or Attraction Not Found</h1>
        <p style="color: var(--dark-muted); font-size: 1rem; line-height: 1.6; margin-bottom: 2rem;">
            The attraction or page you were looking for does not exist, has been removed, or the link may be out of date.
        </p>

        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?= BASE_URL ?>places.php" class="btn btn-primary">
                Browse Attractions
            </a>
            <a href="<?= BASE_URL ?>index.php" class="btn btn-outline">
                Return to Homepage
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
