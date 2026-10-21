<?php
/**
 * TRAVELMATE - Utility & Helper Functions
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

/**
 * Set a session flash message
 */
function set_flash(string $type, string $message): void {
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][$type] = $message;
}

/**
 * Retrieve and clear a session flash message
 */
function get_flash(string $type): ?string {
    if (isset($_SESSION['flash'][$type])) {
        $msg = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $msg;
    }
    if ($type === 'success' && isset($_SESSION['flash_success'])) {
        $msg = $_SESSION['flash_success'];
        unset($_SESSION['flash_success']);
        return $msg;
    }
    if ($type === 'error' && isset($_SESSION['flash_error'])) {
        $msg = $_SESSION['flash_error'];
        unset($_SESSION['flash_error']);
        return $msg;
    }
    return null;
}

/**
 * Render HTML alerts for any active flash messages
 */
function render_flash_messages(): string {
    $html = '';
    $types = ['success' => 'alert-success', 'error' => 'alert-danger', 'info' => 'alert-info', 'warning' => 'alert-warning'];

    foreach ($types as $key => $cssClass) {
        $msg = get_flash($key);
        if ($msg) {
            $html .= '<div class="alert ' . $cssClass . ' alert-dismissible" role="alert">';
            $html .= '<div class="alert-content">';
            $html .= '<span>' . e($msg) . '</span>';
            $html .= '</div>';
            $html .= '<button type="button" class="btn-close" onclick="this.parentElement.remove();" aria-label="Close">&times;</button>';
            $html .= '</div>';
        }
    }

    return $html;
}

/**
 * Calculate Great-Circle Distance between two coordinates in kilometers (Haversine Formula)
 */
function calculate_distance(float $lat1, float $lon1, float $lat2, float $lon2): float {
    $earthRadius = 6371.0; // km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return round($earthRadius * $c, 1);
}

/**
 * Calculate approximate distance of a location from Rathnapura Town Centre
 */
function distance_from_town_centre(float $lat, float $lon): float {
    return calculate_distance(TOWN_CENTRE_LAT, TOWN_CENTRE_LNG, $lat, $lon);
}

/**
 * Calculate total estimated trip distance for an ordered itinerary of attractions
 * Starting from Rathnapura town centre, sequentially visiting each stop
 */
function calculate_itinerary_distance(array $places): float {
    if (empty($places)) {
        return 0.0;
    }

    $total = 0.0;
    $prevLat = TOWN_CENTRE_LAT;
    $prevLng = TOWN_CENTRE_LNG;

    foreach ($places as $place) {
        $pLat = (float)($place['latitude'] ?? 0);
        $pLng = (float)($place['longitude'] ?? 0);

        if ($pLat != 0.0 && $pLng != 0.0) {
            $legDist = calculate_distance($prevLat, $prevLng, $pLat, $pLng);
            $estimatedRoadDist = round($legDist * 1.25, 1);
            $total += $estimatedRoadDist;

            $prevLat = $pLat;
            $prevLng = $pLng;
        }
    }

    return round($total, 1);
}

/**
 * Safe Image Upload Handler
 * Validates extension, MIME type, file size, image headers, and generates unique file name
 * Returns relative path on success, throws Exception on failure
 */
function handle_image_upload(array $file): string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('Uploaded image exceeds the maximum permitted file size of 3 MB.');
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No image file was provided.');
            default:
                throw new Exception('An error occurred during image upload (Code ' . $file['error'] . ').');
        }
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        throw new Exception('Image file is too large. Maximum allowed size is 3 MB.');
    }

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!array_key_exists($mimeType, $allowedMimes)) {
        throw new Exception('Invalid file format. Only JPG, PNG, and WEBP image files are allowed.');
    }

    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new Exception('Uploaded file is not a valid or readable image.');
    }

    if (!is_dir(UPLOAD_DIR)) {
        if (!mkdir(UPLOAD_DIR, 0755, true)) {
            throw new Exception('Server error: Failed to create uploads directory.');
        }
    }

    $extension = $allowedMimes[$mimeType];
    $newFileName = 'place_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $targetPath = UPLOAD_DIR . $newFileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to save uploaded image to destination folder.');
    }

    return 'uploads/' . $newFileName;
}

/**
 * Return absolute URL or proper fallback URL for place image
 */
function get_place_image_url(?string $imagePath): string {
    if (!empty($imagePath)) {
        $cleanPath = ltrim($imagePath, '/');
        $localPath = dirname(__DIR__) . '/' . $cleanPath;
        if (file_exists($localPath)) {
            return BASE_URL . $cleanPath;
        }
        // Check assets/img fallback if path was assets/images
        if (strpos($cleanPath, 'assets/images/') === 0) {
            $altPath = 'assets/img/' . substr($cleanPath, strlen('assets/images/'));
            if (file_exists(dirname(__DIR__) . '/' . $altPath)) {
                return BASE_URL . $altPath;
            }
        }
    }
    // Default placeholder
    if (file_exists(dirname(__DIR__) . '/assets/img/default-place.jpg')) {
        return BASE_URL . 'assets/img/default-place.jpg';
    }
    return BASE_URL . 'assets/images/default-place.jpg';
}

/**
 * Fetch all active categories from DB
 */
function get_all_categories(): array {
    try {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM category ORDER BY category_name ASC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Fetch a tourist place by ID
 */
function get_place_by_id(int $placeId): ?array {
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT tp.*, c.category_name 
            FROM tourist_place tp 
            JOIN category c ON tp.category_id = c.category_id 
            WHERE tp.place_id = ?
            LIMIT 1
        ");
        $stmt->execute([$placeId]);
        $place = $stmt->fetch();
        return $place ?: null;
    } catch (PDOException $e) {
        error_log("Error fetching place #{$placeId}: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if a place is currently in the tourist's day plan
 */
function is_in_day_plan(int $placeId): bool {
    if (!isset($_SESSION['day_plan']) || !is_array($_SESSION['day_plan'])) {
        return false;
    }
    return in_array($placeId, $_SESSION['day_plan']);
}
