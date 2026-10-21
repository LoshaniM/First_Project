<?php
/**
 * TRAVELMATE - Edit Tourist Attraction (FR-18, FR-20, FR-21)
 */

$adminTitle = "Edit Attraction";
require_once __DIR__ . '/../includes/admin_header.php';

$placeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$place = get_place_by_id($placeId);

if (!$place) {
    set_flash('error', 'The requested attraction could not be found.');
    header("Location: " . BASE_URL . "admin/places.php");
    exit;
}

$errors = [];
$categories = get_all_categories();

// Pre-fill existing values
$placeName    = $place['place_name'];
$categoryId   = (int)$place['category_id'];
$locationText = $place['location_text'];
$latitude     = (string)$place['latitude'];
$longitude    = (string)$place['longitude'];
$distanceKm   = (string)$place['distance_km'];
$openingTime  = $place['opening_time'];
$description  = $place['description'];
$travelTips   = $place['travel_tips'];
$currentImage = $place['image_path'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    enforce_csrf();

    $placeName    = trim($_POST['place_name'] ?? '');
    $categoryId   = (int)($_POST['category_id'] ?? 0);
    $locationText = trim($_POST['location_text'] ?? '');
    $latitude     = trim($_POST['latitude'] ?? '');
    $longitude    = trim($_POST['longitude'] ?? '');
    $distanceKm   = trim($_POST['distance_km'] ?? '');
    $openingTime  = trim($_POST['opening_time'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $travelTips   = trim($_POST['travel_tips'] ?? '');

    // Server-side validation
    if (empty($placeName)) {
        $errors[] = 'Attraction Name is required.';
    } elseif (mb_strlen($placeName) > 100) {
        $errors[] = 'Attraction Name cannot exceed 100 characters.';
    }

    if ($categoryId <= 0) {
        $errors[] = 'Please select a valid Category.';
    }

    if (empty($locationText)) {
        $errors[] = 'Location description text is required.';
    }

    if ($latitude === '' || !is_numeric($latitude) || $latitude < -90 || $latitude > 90) {
        $errors[] = 'Valid Latitude coordinate (-90 to +90) is required.';
    }

    if ($longitude === '' || !is_numeric($longitude) || $longitude < -180 || $longitude > 180) {
        $errors[] = 'Valid Longitude coordinate (-180 to +180) is required.';
    }

    if ($distanceKm === '' || !is_numeric($distanceKm) || $distanceKm < 0) {
        if (is_numeric($latitude) && is_numeric($longitude)) {
            $distanceKm = (string)distance_from_town_centre((float)$latitude, (float)$longitude);
        } else {
            $errors[] = 'Valid Distance from town centre in km is required.';
        }
    }

    if (is_numeric($distanceKm) && (float)$distanceKm > 25.0) {
        $errors[] = 'Note: The attraction distance (' . $distanceKm . ' km) exceeds the 25 km radius of Rathnapura town.';
    }

    if (empty($openingTime)) {
        $errors[] = 'Opening schedule / hours information is required.';
    }

    if (empty($description)) {
        $errors[] = 'Attraction description is required.';
    }

    if (empty($travelTips)) {
        $errors[] = 'Travel tips and visitor advice are required.';
    }

    // Handle optional image replacement
    $imagePath = $currentImage;
    if (isset($_FILES['place_image']) && $_FILES['place_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        try {
            $imagePath = handle_image_upload($_FILES['place_image']);
        } catch (Exception $e) {
            $errors[] = 'Image upload error: ' . $e->getMessage();
        }
    }

    // Update if valid
    if (empty($errors)) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                UPDATE tourist_place 
                SET place_name = ?, description = ?, category_id = ?, location_text = ?, 
                    latitude = ?, longitude = ?, distance_km = ?, opening_time = ?, 
                    travel_tips = ?, image_path = ?, updated_at = NOW() 
                WHERE place_id = ?
            ");
            $stmt->execute([
                $placeName,
                $description,
                $categoryId,
                $locationText,
                (float)$latitude,
                (float)$longitude,
                (float)$distanceKm,
                $openingTime,
                $travelTips,
                $imagePath,
                $placeId
            ]);

            set_flash('success', 'Attraction "' . $placeName . '" was successfully updated!');
            header("Location: " . BASE_URL . "admin/places.php");
            exit;
        } catch (PDOException $e) {
            error_log("Update place error: " . $e->getMessage());
            $errors[] = 'Database error while updating the attraction record.';
        }
    }
}

$currentImgUrl = get_place_image_url($currentImage);
?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Edit Attraction: <?= e($place['place_name']) ?></h1>
        <p class="admin-page-subtitle">Update information, coordinates, travel tips, and photos</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="<?= BASE_URL ?>place-details.php?id=<?= $placeId ?>" target="_blank" class="btn-admin btn-admin-secondary">
            View Public Page &nearr;
        </a>
        <a href="<?= BASE_URL ?>admin/places.php" class="btn-admin btn-admin-secondary">
            &larr; Back to List
        </a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger" role="alert">
        <div>
            <strong>Please resolve the following errors:</strong>
            <ul style="margin: 0.5rem 0 0 1.25rem; font-size: 0.9rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form action="<?= BASE_URL ?>admin/place_edit.php?id=<?= $placeId ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-grid">

            <div class="form-group full-width">
                <label for="place_name" class="form-label">Attraction Name <span class="required">*</span></label>
                <input type="text" id="place_name" name="place_name" class="form-control" value="<?= e($placeName) ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id" class="form-label">Category <span class="required">*</span></label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= ($categoryId === (int)$cat['category_id']) ? 'selected' : '' ?>>
                            <?= e($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="location_text" class="form-label">Location / Address <span class="required">*</span></label>
                <input type="text" id="location_text" name="location_text" class="form-control" value="<?= e($locationText) ?>" required>
            </div>

            <div class="form-group">
                <label for="latitude" class="form-label">Latitude Coordinate <span class="required">*</span></label>
                <input type="number" step="0.0000001" id="latitude" name="latitude" class="form-control" value="<?= e($latitude) ?>" required>
            </div>

            <div class="form-group">
                <label for="longitude" class="form-label">Longitude Coordinate <span class="required">*</span></label>
                <input type="number" step="0.0000001" id="longitude" name="longitude" class="form-control" value="<?= e($longitude) ?>" required>
            </div>

            <div class="form-group">
                <label for="distance_km" class="form-label">Distance from Town Centre (km) <span class="required">*</span></label>
                <div style="display: flex; gap: 0.5rem;">
                    <input type="number" step="0.1" id="distance_km" name="distance_km" class="form-control" value="<?= e($distanceKm) ?>" required>
                    <button type="button" class="btn-admin btn-admin-secondary" onclick="calcDist()" style="white-space: nowrap;">
                        Auto Calculate
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="opening_time" class="form-label">Opening Schedule <span class="required">*</span></label>
                <input type="text" id="opening_time" name="opening_time" class="form-control" value="<?= e($openingTime) ?>" required>
            </div>

            <div class="form-group full-width">
                <label for="description" class="form-label">Attraction Description <span class="required">*</span></label>
                <textarea id="description" name="description" class="form-control" rows="4" required><?= e($description) ?></textarea>
            </div>

            <div class="form-group full-width">
                <label for="travel_tips" class="form-label">Travel Tips &amp; Advice <span class="required">*</span></label>
                <textarea id="travel_tips" name="travel_tips" class="form-control" rows="3" required><?= e($travelTips) ?></textarea>
            </div>

            <div class="form-group full-width">
                <label for="place_image" class="form-label">Replace Attraction Photo (Optional &bull; Max 3 MB)</label>
                <input type="file" id="place_image" name="place_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                <div class="image-preview-wrap">
                    <div>
                        <div style="font-size: 0.8rem; color: var(--admin-muted); margin-bottom: 4px;">Current Photo:</div>
                        <img src="<?= e($currentImgUrl) ?>" alt="Current Place Photo" class="image-preview-thumb">
                    </div>
                    <div id="preview_wrap" style="display: none;">
                        <div style="font-size: 0.8rem; color: var(--admin-muted); margin-bottom: 4px;">New Photo Preview:</div>
                        <img id="image_preview" src="" alt="Selected Image Preview" class="image-preview-thumb">
                    </div>
                </div>
            </div>

        </div>

        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--admin-border); display: flex; gap: 1rem;">
            <button type="submit" class="btn-admin btn-admin-primary" style="padding: 0.75rem 1.75rem; font-size: 0.95rem;">
                Update Attraction
            </button>
            <a href="<?= BASE_URL ?>admin/places.php" class="btn-admin btn-admin-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    function calcDist() {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        
        if (isNaN(lat) || isNaN(lng)) {
            alert('Please enter valid Latitude and Longitude coordinates first.');
            return;
        }

        const townLat = 6.6828;
        const townLng = 80.4034;
        const R = 6371;
        const dLat = (lat - townLat) * Math.PI / 180;
        const dLng = (lng - townLng) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(townLat * Math.PI / 180) * Math.cos(lat * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const straightDist = R * c;
        const approxRoadDist = (straightDist * 1.25).toFixed(1);
        
        document.getElementById('distance_km').value = approxRoadDist;
    }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
