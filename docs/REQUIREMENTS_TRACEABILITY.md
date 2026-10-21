# TRAVELMATE – Requirements Traceability Matrix

This document maps all Functional Requirements (FR-01 to FR-28) and Non-Functional Requirements (NFR-01 to NFR-17) to the codebase files and implementation modules.

---

## 1. Functional Requirements (Tourist-Side)

| Req ID | Requirement Description | Implementing File(s) / Features | Status |
| :--- | :--- | :--- | :--- |
| **FR-01** | Attraction listing page showing name, category, and photo for each attraction in a card layout | `places.php`, `assets/css/style.css` (card layout grid) | Complete |
| **FR-02** | Details page showing description, category, distance, opening times, travel tips, location, and photo | `place.php` (and `place-details.php`), `assets/css/style.css` | Complete |
| **FR-03** | Display placeholder images when custom photos are not provided | `includes/functions.php` (`get_place_image_url()`), `assets/img/default-place.jpg` | Complete |
| **FR-04** | Category filtering for Nature, Religious, Public Places, Restaurants & Fast Food | `places.php` (`$_GET['category']`), `database/seed.sql` | Complete |
| **FR-05** | Category data stored in DB and managed by administrator | `database/schema.sql`, `admin/categories.php`, `admin/category_delete.php` | Complete |
| **FR-06** | Friendly empty-state message when no attractions match category or search filter | `places.php` (empty state alert with reset filter action) | Complete |
| **FR-07** | Category pill counter showing number of attractions per category | `index.php`, `places.php`, `admin/dashboard.php` | Complete |
| **FR-08** | Interactive Leaflet.js map with OpenStreetMap tiles | `assets/js/map.js`, `places.php`, `place.php`, `map.php`, `plan.php` | Complete |
| **FR-09** | All-attractions map and dedicated full map page | `map.php`, `places.php` (`initAllPlacesMap()`) | Complete |
| **FR-10** | Single-attraction map on attraction details page | `place.php` (`initSinglePlaceMap()`) | Complete |
| **FR-11** | Approximate distance calculation and display from Rathnapura Town Centre, with friendly offline fallback | `includes/functions.php` (`distance_from_town_centre()`), `assets/js/map.js` (`renderMapFallback()`) | Complete |
| **FR-12** | One-day visit plan: add attraction to plan from listing and details pages | `plan_actions.php` (`action=add`), `places.php`, `place.php` | Complete |
| **FR-13** | Remove attraction from visit plan | `plan_actions.php` (`action=remove`), `plan.php`, `places.php` | Complete |
| **FR-14** | View plan page listing selected attractions with distance, opening times, travel tips, and route map | `plan.php`, `assets/js/map.js` (`initItineraryMap()`) | Complete |
| **FR-15** | Store plan in PHP session without requiring tourist login; show total stops and estimated route distance | `config/config.php` (`$_SESSION['day_plan']`), `plan.php`, `includes/functions.php` (`calculate_itinerary_distance()`) | Complete |
| **FR-16** | Reorder stops in day plan (Move Up / Move Down controls) and Clear Plan option | `plan_actions.php` (`move_up`, `move_down`, `clear`), `plan.php` | Complete |

---

## 2. Functional Requirements (Admin-Side)

| Req ID | Requirement Description | Implementing File(s) / Features | Status |
| :--- | :--- | :--- | :--- |
| **FR-17** | Admin dashboard displaying total attractions, total categories, and recently updated places | `admin/dashboard.php` | Complete |
| **FR-18** | Add new tourist place with required field validations (name, category, coords, distance, etc.) | `admin/place_add.php` | Complete |
| **FR-19** | Delete tourist place with confirmation prompt and cleanup | `admin/place_delete.php`, `assets/js/main.js` (`data-confirm`) | Complete |
| **FR-20** | Edit tourist place information, coordinates, travel tips, and optional image replacement | `admin/place_edit.php` | Complete |
| **FR-21** | Image upload validation (JPG/PNG/WEBP, max 3 MB, safe renaming, upload to `uploads/`) | `includes/functions.php` (`handle_image_upload()`), `uploads/.htaccess` | Complete |
| **FR-22** | Category management (list, add, edit categories) | `admin/categories.php` | Complete |
| **FR-23** | Category deletion blocked if category is currently assigned to attractions | `admin/category_delete.php`, `admin/categories.php` | Complete |
| **FR-24** | Admin login with credential validation and error messaging for invalid credentials | `admin/login.php` | Complete |
| **FR-25** | Server-side session check blocking all admin pages for unauthenticated users | `includes/auth.php` (`require_admin()`), `includes/admin_header.php` | Complete |
| **FR-26** | Admin logout handler | `admin/logout.php` (`perform_admin_logout()`) | Complete |
| **FR-27** | Password hashing with `password_hash()` and `password_verify()` | `database/seed.sql`, `admin/login.php` | Complete |
| **FR-28** | Admin places list page with search and category filtering | `admin/places.php` | Complete |

---

## 3. Non-Functional Requirements

| Req ID | Requirement Description | Implementing File(s) / Features | Status |
| :--- | :--- | :--- | :--- |
| **NFR-01** | Security: PDO prepared statements, CSRF protection on POST, XSS output escaping, session regenerate id | `includes/auth.php`, `includes/csrf.php`, `includes/db.php`, `config/config.php` | Complete |
| **NFR-02** | Secure file upload handling and blocking PHP script execution in uploads directory | `uploads/.htaccess`, `includes/functions.php` | Complete |
| **NFR-03** | Performance: indexed database columns, clean PDO queries, lightweight assets | `database/schema.sql`, `assets/css/style.css` | Complete |
| **NFR-04** | Usability: nature green/teal aesthetic, intuitive navigation, responsive mobile-first design | `assets/css/style.css`, `assets/css/admin.css`, `includes/header.php` | Complete |
| **NFR-05** | Mandatory visitor verification disclaimer notice on all information pages | `includes/footer.php`, `notice.php`, `includes/config.php` | Complete |
| **NFR-06** | Clear labeling that distances, opening times, and tips are approximate sample data | `README.md`, `includes/footer.php`, `notice.php`, `plan.php` | Complete |
| **NFR-07** | Custom branded 404 page for invalid requests and non-existent place IDs | `404.php`, `place.php` | Complete |
| **NFR-08** | Browser compatibility: standards-compliant HTML5/CSS3/Vanilla JS across all modern browsers | Tested, no framework lock-in | Complete |
| **NFR-09** | Maintainability: modular architecture, configuration in one place, clean reusable includes | `includes/`, `config/`, commented code | Complete |
| **NFR-10** | Portability: zero-configuration base URL detection for XAMPP/WAMP | `includes/config.php` (automatic document root & path discovery) | Complete |
| **NFR-11** | Reliability: graceful database error handling without raw SQL leak | `config/database.php`, `includes/db.php` | Complete |
| **NFR-12** | Out-of-scope boundaries respected (no bookings, payments, or tourist registration) | Strict compliance across code | Complete |
| **NFR-13** | Database schema with `utf8mb4` encoding and collation | `database/schema.sql` | Complete |
| **NFR-14** | Comprehensive test cases and test documentation | `docs/TEST_CASES.md` | Complete |
| **NFR-15** | Structured Git repository with clear commits | Git history with descriptive commits | Complete |
| **NFR-16** | Offline map fallback mechanism | `assets/js/map.js` (`renderMapFallback()`) | Complete |
| **NFR-17** | Accessible design: semantic HTML, contrast ratios, alt text, ARIA attributes | All templates | Complete |
