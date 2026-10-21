<?php
/**
 * TRAVELMATE - Tourist Public Footer
 * Contains Mandatory Disclaimer (NFR-05/NFR-06) and scripts
 */
require_once __DIR__ . '/../config/config.php';
?>
</main> <!-- /main-content -->

<footer class="site-footer">
    <div class="container">
        <!-- Mandatory Tourist Information Disclaimer Notice (NFR-05 & NFR-06) -->
        <div class="footer-disclaimer-box" role="note" aria-label="Travel verification disclaimer">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <div>
                <strong>Important Visitor Notice:</strong> <?= APP_DISCLAIMER ?>
            </div>
        </div>

        <div class="footer-top">
            <div class="footer-brand">
                <h4><?= APP_NAME ?> – Rathnapura</h4>
                <p>
                    A dedicated day-trip planner and information system designed for domestic and international travellers exploring the natural wonders, sacred heritage, and gemological treasures within a 25 km radius of Rathnapura, Sabaragamuwa Province, Sri Lanka.
                </p>
            </div>

            <div class="footer-col">
                <h5>Quick Navigation</h5>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>index.php">Home Page</a></li>
                    <li><a href="<?= BASE_URL ?>places.php">Explore Attractions</a></li>
                    <li><a href="<?= BASE_URL ?>plan.php">My 1-Day Itinerary</a></li>
                    <li><a href="<?= BASE_URL ?>admin/login.php">Administrator Portal</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>Starting Benchmark</h5>
                <p style="font-size: 0.9rem; color: #a3cfbb; line-height: 1.6;">
                    <strong>Zero-Km Reference:</strong><br>
                    <?= TOWN_CENTRE_NAME ?><br>
                    Coordinates: 6.6828° N, 80.4034° E<br>
                    Radius: Within 25 km
                </p>
            </div>
        </div>

        <div class="footer-bottom">
            <div>
                &copy; <?= date('Y') ?> <?= APP_NAME ?>. Developed for Academic Project (BIT 4th Semester).
            </div>
            <div>
                Powered by OpenStreetMap &amp; Leaflet.js
            </div>
        </div>
    </div>
</footer>

<!-- Leaflet Map JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- App Scripts -->
<script src="<?= BASE_URL ?>assets/js/map.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>

</body>
</html>
