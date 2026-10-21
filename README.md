# TRAVELMATE – Local Tourist Day-Visit Planner and Information System

A complete, responsive, web-based local tourism information system and day-visit itinerary planner focused on attractions within a **25 km radius of Rathnapura town**, Sri Lanka.

Developed for academic and practical demonstration using modern vanilla technologies.

---

## 1. Project Overview

Rathnapura ("The City of Gems") is surrounded by rich biodiversity, ancient religious temples, sacred pilgrimage history, cascades, prehistoric caves, and gemstone mining tradition. **TRAVELMATE** enables tourists, day-trippers, and cultural enthusiasts to:
1. Discover verified attractions within 25 km of Rathnapura town centre.
2. Filter attractions by categories (Nature, Religious, Public Places, Restaurants & Fast Food).
3. View exact GPS locations and approximate travel distances from the town clock tower.
4. Build a custom **1-Day Visit Itinerary** stored in the PHP session (no tourist login required), reorder stops (up/down), calculate cumulative approximate trip distance, and inspect their sequential route on an interactive Leaflet map.
5. Provide a protected **Administrator Panel** to manage attraction records, image uploads, and categories.

---

## 2. Technology Stack

- **Frontend:** Semantic HTML5, Mobile-First Responsive Vanilla CSS (nature green/teal aesthetic), minimal Vanilla JavaScript for maps and UI interactions (no frontend frameworks).
- **Backend:** Native PHP (PHP 7.4 / 8.0 / 8.1 / 8.2 / 8.3 / 8.4) with **PDO** and prepared statements (no frameworks).
- **Database:** MySQL / MariaDB (Charset: `utf8mb4`, Collation: `utf8mb4_unicode_ci`).
- **Interactive Maps:** Leaflet.js with OpenStreetMap tiles (no API keys required; includes graceful offline fallback).
- **Local Environment:** Standard XAMPP or WAMP stack (Apache + PHP + MySQL).

---

## 3. Directory Structure

```
travelmate/
├── index.php, places.php, place.php, map.php, plan.php, notice.php, 404.php
├── plan_actions.php            # Add/remove/move stops (POST + CSRF protection)
├── admin/
│   ├── login.php               # Admin login with credential validation
│   ├── logout.php              # Secure logout handler
│   ├── dashboard.php           # Statistics (places, categories, recent places)
│   ├── places.php              # Attractions management table with search & filter
│   ├── place_add.php           # Add attraction with validation & image upload
│   ├── place_edit.php          # Edit attraction details & replace photo
│   ├── place_delete.php        # Delete attraction with confirmation
│   ├── categories.php          # Category management (add/edit/delete)
│   ├── category_delete.php     # Block category deletion if in-use by attractions
│   └── index.php               # Admin router
├── includes/
│   ├── config.php              # Database credentials, zero-config base URL, settings
│   ├── db.php                  # PDO singleton connection & setup error page
│   ├── auth.php                # Access control, session management, XSS escaping
│   ├── csrf.php                # CSRF token generation and validation helpers
│   ├── functions.php           # Utilities, distance calculation, image upload handler
│   ├── header.php              # Tourist header & navigation
│   ├── footer.php              # Tourist footer with mandatory verification disclaimer
│   ├── admin_header.php        # Admin navigation & server-side session check
│   └── admin_footer.php        # Admin footer
├── assets/
│   ├── css/
│   │   ├── style.css           # Responsive mobile-first tourist styles (green/teal)
│   │   └── admin.css           # Clean administrative dashboard stylesheet
│   ├── js/
│   │   ├── map.js              # Leaflet maps (overview, single, route, offline fallback)
│   │   └── main.js             # Mobile drawer toggle, auto-fade alerts, confirmations
│   └── img/
│       ├── default-place.jpg   # Default fallback image
│       ├── hero-bg.jpg         # Hero banner background
│       └── *.jpg               # 10 Rathnapura attraction placeholder images
├── uploads/
│   ├── .htaccess               # Strictly blocks PHP script execution in uploads
│   └── .gitkeep
├── database/
│   ├── schema.sql              # MySQL schema (utf8mb4, tables, foreign keys, indexes)
│   └── seed.sql                # Default admin account, 4 categories, 10 attractions
├── docs/
│   ├── REQUIREMENTS_TRACEABILITY.md # FR-01..28 & NFR-01..17 traceability matrix
│   └── TEST_CASES.md           # 23 functional test cases & verification report
├── README.md                   # Installation guide & project documentation
└── .gitignore                  # Git ignore rules
```

---

## 4. Local Installation & Setup (XAMPP / WAMP)

### Step 1: Clone or Copy Project Folder
Copy or move the `Travelmate` folder into your local web server root directory:
- **XAMPP (Windows):** `C:\xampp\htdocs\Travelmate`
- **WAMP (Windows):** `C:\wamp64\www\Travelmate`

### Step 2: Start Apache & MySQL
1. Open the **XAMPP Control Panel** (or WAMP Manager).
2. Click **Start** for **Apache**.
3. Click **Start** for **MySQL**.

### Step 3: Import Database Schema & Seed Data
1. Open your web browser and navigate to **phpMyAdmin**:
   ```
   http://localhost/phpmyadmin/
   ```
2. Click on the **Import** tab (or create a database named `travelmate_db`).
3. Choose and import:
   - First: `database/schema.sql` (Creates tables, foreign keys, and indexes).
   - Second: `database/seed.sql` (Seeds default admin, 4 categories, and 10 places).
   
*Alternatively via MySQL CLI:*
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

### Step 4: Configuration Check
Open `config/config.php` if your MySQL uses a custom port or non-empty root password:
```php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'travelmate_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change if your MySQL has a password
```
*(Dynamic Base URL detection is built-in; no URL hardcoding needed!)*

### Step 5: Launch the Application
- **Tourist Public Portal:**
  ```
  http://localhost/Travelmate/
  ```
- **Administrator Login Portal:**
  ```
  http://localhost/Travelmate/admin/login.php
  ```

---

## 5. Administrator Credentials

| Role | Username | Default Password | Notes |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin` | `Admin@123` | Password is encrypted with `PASSWORD_BCRYPT`. Change upon first production deployment. |

---

## 6. Seed Data (10 Attractions; Starting Point: Rathnapura Town)

All 10 seeded attractions are situated within a 25 km radius of the Rathnapura town centre (Clock Tower benchmark: 6.6828° N, 80.4034° E):

| # | Place Name | Category | Approx Distance | Description Summary |
| :- | :--- | :--- | :--- | :--- |
| 1 | **Sabaragamuwa Maha Saman Devalaya** | Religious | 3.7 km | Important religious and cultural site in the Rathnapura area dedicated to God Saman. |
| 2 | **Batadomba Lena Cave Temple** | Religious | 18.0 km | Historically important cave site near Kuruwita in the Vithana Kanda highland with archaeological importance; prehistoric remains found; surrounding area is an archaeological reserve. |
| 3 | **Rathnapura Town Clock Tower** | Public Places | 1.0 km | Recognisable landmark on the A4 road; easy-to-access place to experience the town centre and urban environment. |
| 4 | **Rathnapura National Museum** | Public Places | 1.8 km | Preserves and displays historical, archaeological and cultural objects; administered by the Department of National Museums. |
| 5 | **Dhanaja Gem Museum & Local Gem Mines** | Public Places | 1.6 km | Learn about gemstones and the local gem-mining tradition Rathnapura is famous for. |
| 6 | **Katugas Ella Falls** | Nature | 3.4 km | Scenic waterfall surrounded by greenery; suitable for a short nature excursion. |
| 7 | **Bopath Ella Waterfall** | Nature | 14.0 km | Well-known waterfall with a distinctive shape; popular for sightseeing and nature visits. |
| 8 | **Rajanawa Waterfall** | Nature | 9.0 km | Scenic waterfall in lush greenery; good for nature, photography, short outdoor trips. |
| 9 | **Mellona Chocolate Cafe** | Restaurants & Fast Food | 2.0 km | At 226A, New Town Shopping Complex, Rathnapura; coffee, tea, chocolate products, cakes, waffles, pancakes. |
| 10 | **Garland Café** | Restaurants & Fast Food | 2.0 km | Café/restaurant in New Town, Rathnapura with international-style and local dishes, cakes and desserts. |

> [!IMPORTANT]
> **Sample Data Verification Notice:** The GPS coordinates, opening schedules, distances, and visitor travel tips provided in `database/seed.sql` are plausible sample data that the administrator must verify before publishing to official tourist audiences. Local default placeholder images (`assets/img/`) are provided out of the box so the application functions with zero external dependencies and does not hotlink copyrighted images. The administrator can replace any image via the Admin Panel (`admin/place_edit.php`).

---

## 7. Functional Highlights

### Tourist-Side Features
- **Attraction Browsing & Details (FR-01 to FR-03):** Card layout with photos, categories, distance, opening times, location text, and travel advice.
- **Category Filtering (FR-04 to FR-07):** Dynamic filtering with attraction count badges and friendly empty state when no items match.
- **Interactive Maps (FR-08 to FR-11):** Leaflet maps for all attractions and individual attraction view, showing benchmark distance from Rathnapura Town Centre with graceful offline fallback.
- **1-Day Itinerary Planner (FR-12 to FR-16):**
  - Session-based persistence (no tourist login required).
  - Add, remove, and clear stops.
  - Reorder stops sequentially (**Move Up** / **Move Down**).
  - Calculates total stops and cumulative approximate itinerary travel distance.
  - Generates a sequential route map connecting stops in order.
- **Mandatory Information Disclaimer (NFR-05 & NFR-06):**
  > *"Distances, opening times and travel tips are approximate and may require verification before your visit."*

### Administrator-Side Features
- **Secure Authentication (FR-24 to FR-28):** Password verification via `password_verify()`, session regeneration on login, session timeout guards, and strict access denial for unauthenticated visitors.
- **Dashboard Overview (FR-17):** Real-time statistics (total places, categories, average distance from town) and recently updated records.
- **Attraction Management (FR-18 to FR-21):** Add, edit, and delete attractions with server-side validation (ensuring places fall within the 25 km radius), photo upload with MIME/size limits, and delete confirmations.
- **Category Management (FR-22 & FR-23):** Add, edit, and delete categories with a **foreign-key safeguard** preventing deletion of any category currently assigned to attractions.
- **Search & Filtering:** Search attractions by name, location, or description.

---

## 8. Security Implementations (NFR-01)

- **SQL Injection Prevention:** 100% of database interactions execute through PDO prepared statements with parameter binding.
- **Cross-Site Scripting (XSS) Mitigation:** Output escaping through `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` via the global `e()` helper function.
- **Cross-Site Request Forgery (CSRF):** Cryptographically secure CSRF tokens generated per session, validated on every state-changing POST submission (`enforce_csrf()`).
- **Session Fixation Defense:** `session_regenerate_id(true)` called upon successful authentication.
- **Secure Cookie Parameters:** `httponly = true` and `samesite = Lax` session cookie options.
- **File Upload Hardening:** Extension whitelisting (`jpg`, `png`, `webp`), MIME type verification with `finfo`, `getimagesize()` structural verification, 3 MB file size capping, and randomized filename generation.
- **Error Handling:** Private error logging (`error_log`) with user-friendly error views, preventing exposure of raw SQL errors or server paths.

---

## 9. Verification & Testing Checklist

- [x] Database schema imports cleanly with UTF-8 character encoding.
- [x] Seed data populates default admin, 4 categories, and 10 Rathnapura places.
- [x] Admin login with default credentials succeeds; invalid credentials show friendly error.
- [x] Admin dashboard displays correct counts and recent places.
- [x] Adding, editing, and deleting an attraction functions with validation and image upload.
- [x] Deleting a category that contains attractions is strictly blocked.
- [x] Category filtering on `places.php` filters attractions properly.
- [x] Interactive Leaflet maps render with town centre benchmark and popups.
- [x] Adding/removing attractions from 1-day plan updates session count in navigation.
- [x] Moving stops Up/Down changes itinerary order, recalculates route distance, and updates map pins.
- [x] Footer notice displays mandatory disclaimer on all public pages.
- [x] Custom 404 page handles non-existent pages gracefully.

---

## 10. License & Academic Attribution
Developed for BIT 4th Semester Project. Free to use and adapt for educational and demonstration purposes.
