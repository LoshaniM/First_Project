<?php
/**
 * TRAVELMATE - Plan Action Controller (FR-12, FR-13, FR-15, FR-16)
 * Handles Session-based Day Itinerary: Add, Remove, Reorder (Up/Down), Clear
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Verify CSRF token for all state-modifying requests
enforce_csrf();

$action    = $_POST['action'] ?? '';
$placeId   = isset($_POST['place_id']) ? (int)$_POST['place_id'] : 0;
$returnUrl = $_POST['return_url'] ?? (BASE_URL . 'plan.php');

if (!isset($_SESSION['day_plan']) || !is_array($_SESSION['day_plan'])) {
    $_SESSION['day_plan'] = [];
}

switch ($action) {
    case 'add':
        if ($placeId > 0) {
            $place = get_place_by_id($placeId);
            if ($place) {
                if (!in_array($placeId, $_SESSION['day_plan'])) {
                    $_SESSION['day_plan'][] = $placeId;
                    set_flash('success', '"' . $place['place_name'] . '" added to your 1-day itinerary.');
                } else {
                    set_flash('info', '"' . $place['place_name'] . '" is already in your itinerary.');
                }
            } else {
                set_flash('error', 'Attraction could not be found.');
            }
        }
        break;

    case 'remove':
        if ($placeId > 0) {
            $key = array_search($placeId, $_SESSION['day_plan']);
            if ($key !== false) {
                $place = get_place_by_id($placeId);
                $name = $place ? $place['place_name'] : 'Attraction';
                unset($_SESSION['day_plan'][$key]);
                $_SESSION['day_plan'] = array_values($_SESSION['day_plan']);
                set_flash('success', '"' . $name . '" was removed from your day itinerary.');
            }
        }
        break;

    case 'move_up':
        if ($placeId > 0) {
            $key = array_search($placeId, $_SESSION['day_plan']);
            if ($key !== false && $key > 0) {
                $temp = $_SESSION['day_plan'][$key - 1];
                $_SESSION['day_plan'][$key - 1] = $_SESSION['day_plan'][$key];
                $_SESSION['day_plan'][$key] = $temp;
                set_flash('success', 'Stop reordered up.');
            }
        }
        break;

    case 'move_down':
        if ($placeId > 0) {
            $key = array_search($placeId, $_SESSION['day_plan']);
            if ($key !== false && $key < count($_SESSION['day_plan']) - 1) {
                $temp = $_SESSION['day_plan'][$key + 1];
                $_SESSION['day_plan'][$key + 1] = $_SESSION['day_plan'][$key];
                $_SESSION['day_plan'][$key] = $temp;
                set_flash('success', 'Stop reordered down.');
            }
        }
        break;

    case 'clear':
        $_SESSION['day_plan'] = [];
        set_flash('info', 'Your day-visit plan has been cleared.');
        break;

    default:
        set_flash('error', 'Invalid action specified.');
        break;
}

header("Location: " . $returnUrl);
exit;
