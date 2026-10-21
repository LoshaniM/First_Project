-- ===================================================================
-- TRAVELMATE Database Schema
-- Local Tourist Day-Visit Planner and Information System (Rathnapura)
-- Charset: utf8mb4, Collation: utf8mb4_unicode_ci
-- ===================================================================

CREATE DATABASE IF NOT EXISTS `travelmate_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `travelmate_db`;

-- Drop existing tables in reverse dependency order
DROP TABLE IF EXISTS `visit_plan_item`;
DROP TABLE IF EXISTS `visit_plan`;
DROP TABLE IF EXISTS `tourist_place`;
DROP TABLE IF EXISTS `category`;
DROP TABLE IF EXISTS `administrator`;

-- -------------------------------------------------------------------
-- Table 1: administrator
-- Stores administrative users who manage attraction & category data
-- -------------------------------------------------------------------
CREATE TABLE `administrator` (
    `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------
-- Table 2: category
-- Tourist attraction categories (e.g., Nature, Religious, etc.)
-- -------------------------------------------------------------------
CREATE TABLE `category` (
    `category_id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_name` VARCHAR(50) NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_category_name` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------
-- Table 3: tourist_place
-- Attractions within 25 km radius of Rathnapura town centre
-- Starting point reference: Rathnapura Clock Tower (6.6828° N, 80.4034° E)
-- -------------------------------------------------------------------
CREATE TABLE `tourist_place` (
    `place_id` INT AUTO_INCREMENT PRIMARY KEY,
    `place_name` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `category_id` INT NOT NULL,
    `location_text` VARCHAR(150) NOT NULL,
    `latitude` DECIMAL(10, 7) NOT NULL,
    `longitude` DECIMAL(10, 7) NOT NULL,
    `distance_km` DECIMAL(5, 1) NOT NULL,
    `opening_time` VARCHAR(100) NOT NULL,
    `travel_tips` TEXT NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_place_category` FOREIGN KEY (`category_id`) 
        REFERENCES `category` (`category_id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    INDEX `idx_place_category` (`category_id`),
    INDEX `idx_place_distance` (`distance_km`),
    INDEX `idx_place_name` (`place_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------
-- Future-use Tables: visit_plan & visit_plan_item
-- Note: Core application operates seamlessly via PHP session without login.
-- These tables are included for future database persistence enhancements.
-- -------------------------------------------------------------------
CREATE TABLE `visit_plan` (
    `plan_id` INT AUTO_INCREMENT PRIMARY KEY,
    `session_identifier` VARCHAR(100) NULL,
    `title` VARCHAR(100) DEFAULT 'My Rathnapura Day Plan',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `visit_plan_item` (
    `item_id` INT AUTO_INCREMENT PRIMARY KEY,
    `plan_id` INT NOT NULL,
    `place_id` INT NOT NULL,
    `position` INT NOT NULL DEFAULT 1,
    CONSTRAINT `fk_plan_item_plan` FOREIGN KEY (`plan_id`) 
        REFERENCES `visit_plan` (`plan_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_plan_item_place` FOREIGN KEY (`place_id`) 
        REFERENCES `tourist_place` (`place_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
