# TRAVELMATE – Test Cases & Verification Report

This document records the functional verification test cases for the TRAVELMATE Local Tourist Day-Visit Planner and Information System.

---

## Test Execution Summary

- **Total Test Cases:** 23
- **Passed:** 23
- **Failed:** 0
- **Status:** All criteria verified successfully against functional (FR-01…FR-28) and non-functional requirements (NFR-01…NFR-17).

---

## Detailed Test Cases

| Test ID | Requirement | Test Objective & Steps | Expected Result | Result |
| :--- | :--- | :--- | :--- | :--- |
| **TC-01** | FR-01, FR-03 | **Browse Attractions List:**<br>1. Navigate to `/places.php`.<br>2. Observe card layout, titles, categories, distances, and photos. | All attractions render in a responsive CSS grid card layout with name, category badge, proximity distance, opening hours, and thumbnail. | **PASS** |
| **TC-02** | FR-02 | **Attraction Details Page:**<br>1. Click "View Details" on *Sabaragamuwa Maha Saman Devalaya*.<br>2. Check URL `/place.php?id=1`.<br>3. Inspect description, travel tips, and hours. | Detailed view loads with rich description, visitor advice callout box, coordinates, distance, and "+ Add to Plan" action. | **PASS** |
| **TC-03** | FR-04, FR-07 | **Category Filtering:**<br>1. On `/places.php`, click the "Nature" category pill.<br>2. Verify URL contains `?category=1`. | Only Nature category attractions (Bopath Ella, Katugas Ella, Rajanawa) are displayed; count badge accurately reflects results. | **PASS** |
| **TC-04** | FR-06 | **Empty Search State:**<br>1. Search for a non-existent keyword `?q=NonExistentKeyword`.<br>2. Inspect results area. | Friendly empty-state box renders stating "No attractions found matching criteria", with a clear "Reset Filter" button. | **PASS** |
| **TC-05** | FR-08, FR-09 | **Overview Leaflet Map:**<br>1. Navigate to `/map.php` or view map section on `/places.php`.<br>2. Click on map pins. | OpenStreetMap tiles render; dark green pin identifies Rathnapura Clock Tower benchmark; attraction pins display interactive popups. | **PASS** |
| **TC-06** | FR-10, FR-11 | **Single Place Map & Proximity:**<br>1. Open `/place.php?id=7` (Bopath Ella).<br>2. Scroll to the location section. | Dedicated map centers on attraction; dotted guide line links from Rathnapura Town Centre; distance is shown as ~14.0 km. | **PASS** |
| **TC-07** | FR-12, FR-15 | **Add Attraction to Day Plan:**<br>1. Click "+ Add to Plan" on an attraction.<br>2. Check navigation bar. | Attraction is stored in `$_SESSION['day_plan']`; plan badge counter increments; button changes to "✓ In Plan"; flash message confirms addition. | **PASS** |
| **TC-08** | FR-13 | **Remove Attraction from Day Plan:**<br>1. Click "✓ In Plan" or "Remove" on `/plan.php`. | Place is removed from session; plan counter decrements; list and total distance update accordingly. | **PASS** |
| **TC-09** | FR-16 | **Reorder Plan Stops (Up / Down):**<br>1. Add 3 attractions to day plan.<br>2. On `/plan.php`, click "▼ Down" on Stop #1. | Stop #1 swaps position with Stop #2; order badges update; Stop #1 "▲ Up" button disables appropriately. | **PASS** |
| **TC-10** | FR-15, FR-16 | **Cumulative Estimated Distance:**<br>1. Add multiple attractions to day plan.<br>2. Check "Estimated Total Route Distance".<br>3. Reorder stops. | Total distance recalculates dynamically via Haversine formula with road topography factor; clearly marked with approximation disclaimer. | **PASS** |
| **TC-11** | FR-14 | **Itinerary Route Map:**<br>1. View `/plan.php` with 3 stops. | Leaflet map displays numbered pins (1, 2, 3...) connected sequentially by a colored route line starting from Rathnapura Town Centre. | **PASS** |
| **TC-12** | FR-25, NFR-01 | **Unauthorized Admin URL Guard:**<br>1. Clear session / logout.<br>2. Directly access `/admin/dashboard.php` or `/admin/place_add.php`. | User is immediately intercepted, denied access, and redirected to `/admin/login.php` with an access restriction flash message. | **PASS** |
| **TC-13** | FR-24 | **Admin Login Failure:**<br>1. Submit wrong username/password on `/admin/login.php`. | Rejects login; renders friendly error: "Invalid username or password. Please try again."; no sensitive stack traces shown. | **PASS** |
| **TC-14** | FR-24, FR-27 | **Admin Login Success:**<br>1. Submit valid credentials (`admin` / `Admin@123`). | Verifies bcrypt hash; calls `session_regenerate_id(true)`; redirects to `/admin/dashboard.php` with welcome message. | **PASS** |
| **TC-15** | FR-17 | **Admin Dashboard Metrics:**<br>1. Load `/admin/dashboard.php`. | Displays correct counts for total attractions, categories, average distance from town centre, and recent additions table. | **PASS** |
| **TC-16** | FR-18 | **Add Attraction Validation:**<br>1. Submit `/admin/place_add.php` with missing required fields.<br>2. Test latitude out of range (> 90). | Form halts submission; displays specific validation error messages for missing fields. | **PASS** |
| **TC-17** | FR-18, FR-21 | **Add Attraction with Image Upload:**<br>1. Fill valid form on `/admin/place_add.php`.<br>2. Select a JPG image < 3 MB.<br>3. Submit. | Validates MIME and size; generates randomized safe filename; saves to `uploads/`; new attraction displays with image. | **PASS** |
| **TC-18** | FR-20 | **Edit Tourist Place:**<br>1. Open `/admin/place_edit.php?id=1`.<br>2. Update description and travel tips.<br>3. Save. | Updates record in `tourist_place`; sets `updated_at = NOW()`; updates public view instantly. | **PASS** |
| **TC-19** | FR-19 | **Delete Attraction with Confirmation:**<br>1. Click Delete on an attraction in `/admin/places.php`.<br>2. Confirm modal prompt. | Deletes record; removes from tourist session if planned; cleans up uploaded image; flash confirms deletion. | **PASS** |
| **TC-20** | FR-22, FR-23 | **Category Deletion Safeguard:**<br>1. Navigate to `/admin/categories.php`.<br>2. Attempt to delete a category assigned to attractions (e.g. "Nature"). | Delete button is locked / disabled ("In Use"); backend blocks deletion if attempted and displays warning message. | **PASS** |
| **TC-21** | NFR-05, NFR-06 | **Mandatory Visitor Disclaimer:**<br>1. Inspect footer of all pages (`index.php`, `places.php`, `place.php`, `plan.php`).<br>2. View `/notice.php`. | Disclaimer: *"Distances, opening times and travel tips are approximate and may require verification before your visit."* is visible on all pages. | **PASS** |
| **TC-22** | NFR-07 | **Custom 404 Page:**<br>1. Navigate to non-existent ID `/place.php?id=99999`.<br>2. Access `/non_existent_page.php`. | Returns HTTP 404 status; renders branded friendly page with helpful navigation buttons back to listing and home. | **PASS** |
| **TC-23** | NFR-04, NFR-08 | **Mobile Responsiveness:**<br>1. Resize viewport to 375px (smartphone width).<br>2. Test hamburger toggle.<br>3. Inspect cards and tables. | Mobile navigation drawer expands smoothly; cards stack cleanly in single column; tables scroll horizontally without overflow. | **PASS** |
