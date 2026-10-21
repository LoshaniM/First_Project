<?php
/**
 * TRAVELMATE - Visitor Information & Verification Notice (NFR-05, NFR-06)
 */

$pageTitle = "Visitor Notice & Information Disclaimer";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 2.5rem; padding-bottom: 4.5rem;">
    <div style="max-width: 820px; margin: 0 auto; background: #ffffff; border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 2.5rem; box-shadow: var(--card-shadow);">

        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: #fff3cd; color: #856404; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                ⚠️
            </div>
            <div>
                <h1 style="font-size: 1.75rem; color: var(--primary-dark); margin: 0;">Visitor Information Notice</h1>
                <p style="color: var(--dark-muted); font-size: 0.88rem; margin: 0;">Verification Guidelines for Day-Trippers in Rathnapura</p>
            </div>
        </div>

        <div class="travel-tips-box" style="margin: 1.5rem 0;">
            <strong style="font-size: 1.05rem;">Official Notice (NFR-05 &amp; NFR-06):</strong>
            <p style="font-size: 1rem; color: #5a3c10; margin-top: 0.35rem; line-height: 1.6;">
                "<?= APP_DISCLAIMER ?>"
            </p>
        </div>

        <h2 style="font-size: 1.25rem; color: var(--primary); margin-top: 2rem; margin-bottom: 0.75rem;">
            1. Geographic Starting Benchmark
        </h2>
        <p style="color: #334155; line-height: 1.7; margin-bottom: 1rem;">
            All attraction distances displayed throughout TRAVELMATE are calculated with reference to the <strong>Rathnapura Town Clock Tower</strong> (GPS Coordinates: <code>6.6828° N, 80.4034° E</code>). All destinations in this system are strictly confined to a <strong>25 km radius</strong> around this central reference point.
        </p>

        <h2 style="font-size: 1.25rem; color: var(--primary); margin-top: 1.75rem; margin-bottom: 0.75rem;">
            2. Estimated Route Distance &amp; Road Topography
        </h2>
        <p style="color: #334155; line-height: 1.7; margin-bottom: 1rem;">
            The cumulative distance calculated in the <strong>1-Day Visit Itinerary</strong> accounts for straight-line distances with a road curvature factor applied to simulate hill-country Sabaragamuwa terrain. Actual driving or tuk-tuk odometer distances may vary depending on road conditions, detours, and selected transit routes.
        </p>

        <h2 style="font-size: 1.25rem; color: var(--primary); margin-top: 1.75rem; margin-bottom: 0.75rem;">
            3. Cultural &amp; Safety Guidelines
        </h2>
        <ul style="color: #334155; line-height: 1.8; margin-left: 1.5rem; margin-bottom: 1.5rem;">
            <li><strong>Religious Sites (Devalayas &amp; Temples):</strong> Please wear modest attire covering shoulders and knees. Footwear and hats must be removed prior to stepping onto consecrated shrine grounds.</li>
            <li><strong>Natural Cascades &amp; Riverbanks:</strong> Granite rocks around waterfalls can be extremely slippery due to river mist. Adhere strictly to signposted warnings and avoid bathing in deep turbulent gorges.</li>
            <li><strong>Opening Hours:</strong> Temple thevava ceremonies and public museum hours may vary during full moon Poya holidays and national festivals. Direct local inquiry is recommended.</li>
        </ul>

        <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <a href="<?= BASE_URL ?>places.php" class="btn btn-primary">
                Explore Attractions &rarr;
            </a>
            <a href="<?= BASE_URL ?>plan.php" class="btn btn-outline">
                Open My 1-Day Plan
            </a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
