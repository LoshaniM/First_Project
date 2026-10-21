-- ===================================================================
-- TRAVELMATE Seed Data
-- 10 Verified Attractions Around Rathnapura Town (Within 25 km Radius)
-- ===================================================================

USE `travelmate_db`;

-- -------------------------------------------------------------------
-- 1. Default Administrator Credentials
-- Username: admin
-- Password: Admin@123
-- (Bcrypt hash: $2y$10$lNgDkDup7iNe7P7Krogx9uRE/lgHsr69GpYUn0B9SWnsKAiuZEPJ6)
-- NOTE: Please update the administrator password immediately upon first login!
-- -------------------------------------------------------------------
INSERT INTO `administrator` (`admin_id`, `username`, `password_hash`, `created_at`) 
VALUES (1, 'admin', '$2y$10$lNgDkDup7iNe7P7Krogx9uRE/lgHsr69GpYUn0B9SWnsKAiuZEPJ6', NOW())
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

-- -------------------------------------------------------------------
-- 2. Attraction Categories
-- -------------------------------------------------------------------
INSERT INTO `category` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'Nature', 'Scenic waterfalls, natural greenery, riverbanks, and outdoor excursions.', NOW()),
(2, 'Religious', 'Historic devalayas, sacred cave temples, and Buddhist cultural heritage.', NOW()),
(3, 'Public Places', 'Town landmarks, national museums, and historic gem mining centers.', NOW()),
(4, 'Restaurants & Fast Food', 'Cafés, sweet treats, bakeries, and traveler refreshment dining.', NOW())
ON DUPLICATE KEY UPDATE `category_name` = VALUES(`category_name`);

-- -------------------------------------------------------------------
-- 3. Tourist Attractions (Starting Reference: Rathnapura Town Centre)
-- Reference: Rathnapura Clock Tower (Lat: 6.6828000, Lon: 80.4034000)
-- -------------------------------------------------------------------
INSERT INTO `tourist_place` (
    `place_id`, 
    `place_name`, 
    `description`, 
    `category_id`, 
    `location_text`, 
    `latitude`, 
    `longitude`, 
    `distance_km`, 
    `opening_time`, 
    `travel_tips`, 
    `image_path`, 
    `created_at`, 
    `updated_at`
) VALUES
(
    1,
    'Sabaragamuwa Maha Saman Devalaya',
    'Important religious and cultural site in the Rathnapura area dedicated to God Saman, guardian deity of Sri Pada (Adam''s Peak). Featuring exquisite traditional architecture, wooden carved pillars, and rich cultural rituals on the banks of the Kalu Ganga.',
    2,
    'Panadura - Luwandugoda Rd, Ratnapura',
    6.6836000,
    80.3734000,
    3.7,
    '6:00 AM – 7:30 PM (Daily)',
    'Wear modest white attire covering shoulders and knees. Remove footwear before entering upper shrine courtyards.',
    'assets/img/saman-devalaya.jpg',
    NOW(),
    NOW()
),
(
    2,
    'Batadomba Lena Cave Temple',
    'Historically important cave site near Kuruwita in the Vithana Kanda highland with archaeological importance; prehistoric remains found; surrounding area is an archaeological reserve providing proof of early human habitation in Sri Lanka.',
    2,
    'Vithana Kanda highland, Kuruwita',
    6.7786000,
    80.4183000,
    18.0,
    '7:30 AM – 5:00 PM (Daily)',
    'Wear sturdy non-slip footwear for the steady hillside hike. Carry drinking water and treat all prehistoric rock surfaces with care.',
    'assets/img/batadombalena.jpg',
    NOW(),
    NOW()
),
(
    3,
    'Rathnapura Town Clock Tower',
    'Recognisable landmark on the A4 road; easy-to-access place to experience the town centre and urban environment. Serves as the central orientation landmark and baseline starting point for day tours.',
    3,
    'Town Hall Roundabout, A4 Main Rd, Rathnapura',
    6.6828000,
    80.4034000,
    1.0,
    'Open 24 Hours (Public Landmark)',
    'A central meeting location with local tea shops, ATMs, and transport within a 2-minute stroll. Best photo angle is from the park footpath.',
    'assets/img/clock-tower.jpg',
    NOW(),
    NOW()
),
(
    4,
    'Rathnapura National Museum',
    'Preserves and displays historical, archaeological and cultural objects; administered by the Department of National Museums. Housed in the historic Ehelepola Walawwa, featuring prehistoric tools and gemological fossil exhibits.',
    3,
    'Museum Road, Rathnapura',
    6.6853000,
    80.4005000,
    1.8,
    '9:00 AM – 5:00 PM (Tue – Sat, Closed Sun & Mon)',
    'Purchase nominal admission tickets at the entrance counter. Allocate 45–60 minutes for a relaxed, informative indoor walk.',
    'assets/img/national-museum.jpg',
    NOW(),
    NOW()
),
(
    5,
    'Dhanaja Gem Museum & Local Gem Mines',
    'Learn about gemstones and the local gem-mining tradition Rathnapura is famous for. Features displays of rough corundum crystals, traditional pit mining models, and demonstrations of gem cutting and identification.',
    3,
    'Main Street / Gem Market Promenade, Rathnapura',
    6.6811000,
    80.4022000,
    1.6,
    '8:30 AM – 5:30 PM (Daily)',
    'Ask the resident gemologists for demonstrations of refraction and color identification; polite inquiries are welcomed.',
    'assets/img/gem-museum.jpg',
    NOW(),
    NOW()
),
(
    6,
    'Katugas Ella Falls',
    'Scenic waterfall surrounded by greenery; suitable for a short nature excursion. The cascade tumbles over rock ledges into a shaded pool just a brief drive from the town borders.',
    1,
    'Katugas Ella Rd, Ratnapura',
    6.7118000,
    80.3882000,
    3.4,
    '7:00 AM – 6:00 PM (Daily)',
    'The wet riverside boulders can be slippery; stay along designated viewpoints and exercise safety with cameras and small children.',
    'assets/img/katugas-ella.jpg',
    NOW(),
    NOW()
),
(
    7,
    'Bopath Ella Waterfall',
    'Well-known waterfall with a distinctive shape; popular for sightseeing and nature visits. Cascades 30 meters down like the sacred leaf of the sacred Bo tree into a wide rocky pool.',
    1,
    'Agalawatta Rd, Kuruvita',
    6.7937000,
    80.3694000,
    14.0,
    '7:00 AM – 6:00 PM (Daily)',
    'Arrive before 10:30 AM on weekends for tranquil sightseeing and photography. Several local fruit and snack vendors are situated by the path.',
    'assets/img/bopath-ella.jpg',
    NOW(),
    NOW()
),
(
    8,
    'Rajanawa Waterfall',
    'Scenic waterfall in lush greenery; good for nature, photography, short outdoor trips. Nestled amidst quiet vegetation and natural streams off the Pelmadulla route.',
    1,
    'Rajanawa Village, Ratnapura District',
    6.6436000,
    80.4350000,
    9.0,
    '7:30 AM – 5:30 PM (Daily)',
    'Ideal spot for nature photography in soft morning light. Please preserve the natural setting by taking all personal litter back with you.',
    'assets/img/rajanawa-ella.jpg',
    NOW(),
    NOW()
),
(
    9,
    'Mellona Chocolate Cafe',
    'At 226A, New Town Shopping Complex, Rathnapura; coffee, tea, chocolate products, cakes, waffles, pancakes. A welcoming haven for day-trippers craving sweet refreshments.',
    4,
    '226A, New Town Shopping Complex, Rathnapura',
    6.6965000,
    80.4082000,
    2.0,
    '9:00 AM – 9:30 PM (Daily)',
    'Try their signature hot chocolate and Belgian-style waffles. Free customer Wi-Fi and air-conditioned seating available.',
    'assets/img/mellona-cafe.jpg',
    NOW(),
    NOW()
),
(
    10,
    'Garland Café',
    'Café/restaurant in New Town, Rathnapura with international-style and local dishes, cakes and desserts. Perfect culinary stop during your midday visit break.',
    4,
    'New Town Road, Rathnapura',
    6.6970000,
    80.4095000,
    2.0,
    '10:00 AM – 10:00 PM (Daily)',
    'Great choice for a relaxed lunch or chilled espresso beverages. Offers a welcoming ambiance for families and travel groups.',
    'assets/img/garland-cafe.jpg',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE 
    `place_name` = VALUES(`place_name`),
    `description` = VALUES(`description`),
    `category_id` = VALUES(`category_id`),
    `location_text` = VALUES(`location_text`),
    `latitude` = VALUES(`latitude`),
    `longitude` = VALUES(`longitude`),
    `distance_km` = VALUES(`distance_km`),
    `opening_time` = VALUES(`opening_time`),
    `travel_tips` = VALUES(`travel_tips`),
    `image_path` = VALUES(`image_path`);
