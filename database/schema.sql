-- Jyoti Hardware database schema and safe upgrade script.
-- Import with: C:\xampp\mysql\bin\mysql.exe -u root < database\schema.sql

CREATE DATABASE IF NOT EXISTS `jyoti_hardware`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `jyoti_hardware`;

CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(150) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admins_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`),
  KEY `idx_categories_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `brands` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_brands_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `brand_id` INT UNSIGNED DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) DEFAULT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `product_url` VARCHAR(2048) DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock` INT NOT NULL DEFAULT 0,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_products_status` (`status`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_brand` (`brand_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_products_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_product_images_product` (`product_id`),
  CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `quotes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED DEFAULT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `company` VARCHAR(150) DEFAULT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `message` TEXT DEFAULT NULL,
  `status` ENUM('Pending', 'Contacted', 'Completed') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_quotes_product` (`product_id`),
  KEY `idx_quotes_status` (`status`),
  CONSTRAINT `fk_quotes_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id` INT UNSIGNED NOT NULL,
  `transaction_uuid` VARCHAR(100) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(30) NOT NULL DEFAULT 'esewa',
  `payment_status` ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
  `response_data` LONGTEXT DEFAULT NULL,
  `paid_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_transaction_uuid` (`transaction_uuid`),
  UNIQUE KEY `uq_orders_quote` (`quote_id`),
  KEY `idx_orders_payment_status` (`payment_status`),
  CONSTRAINT `fk_orders_quote` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Minimal catalog data for a new installation. Existing records are preserved.
INSERT INTO `admins` (`fullname`, `username`, `password`)
SELECT 'Administrator', 'admin', '$2y$10$esOV8MhtOlT.PpJAO.XOXOfJ5/btSZBA4L4ievtt7pVj65BogdUWm'
WHERE NOT EXISTS (SELECT 1 FROM `admins` WHERE `username` = 'admin');

INSERT INTO `categories` (`name`, `slug`, `status`)
SELECT 'Power Tools', 'power-tools', 1 WHERE NOT EXISTS (SELECT 1 FROM `categories` WHERE `slug` = 'power-tools');
INSERT INTO `categories` (`name`, `slug`, `status`)
SELECT 'Hand Tools', 'hand-tools', 1 WHERE NOT EXISTS (SELECT 1 FROM `categories` WHERE `slug` = 'hand-tools');
INSERT INTO `categories` (`name`, `slug`, `status`)
SELECT 'Electrical', 'electrical', 1 WHERE NOT EXISTS (SELECT 1 FROM `categories` WHERE `slug` = 'electrical');
INSERT INTO `categories` (`name`, `slug`, `status`)
SELECT 'Plumbing', 'plumbing', 1 WHERE NOT EXISTS (SELECT 1 FROM `categories` WHERE `slug` = 'plumbing');
INSERT INTO `categories` (`name`, `slug`, `status`)
SELECT 'Paints', 'paints', 1 WHERE NOT EXISTS (SELECT 1 FROM `categories` WHERE `slug` = 'paints');

INSERT INTO `brands` (`name`, `status`)
SELECT 'Bosch', 1 WHERE NOT EXISTS (SELECT 1 FROM `brands` WHERE `name` = 'Bosch');
INSERT INTO `brands` (`name`, `status`)
SELECT 'Dongcheng', 1 WHERE NOT EXISTS (SELECT 1 FROM `brands` WHERE `name` = 'Dongcheng');
INSERT INTO `brands` (`name`, `status`)
SELECT 'Ingco', 1 WHERE NOT EXISTS (SELECT 1 FROM `brands` WHERE `name` = 'Ingco');
INSERT INTO `brands` (`name`, `status`)
SELECT 'Makita', 1 WHERE NOT EXISTS (SELECT 1 FROM `brands` WHERE `name` = 'Makita');
INSERT INTO `brands` (`name`, `status`)
SELECT 'Stanley', 1 WHERE NOT EXISTS (SELECT 1 FROM `brands` WHERE `name` = 'Stanley');

-- Adds the supplied plumbing and water-storage photo catalog to new installations.
SOURCE database/catalog-images.sql;

-- Upgrades an existing installation without changing or deleting any data.
ALTER TABLE `quotes` ADD COLUMN IF NOT EXISTS `product_id` INT UNSIGNED DEFAULT NULL AFTER `id`;
ALTER TABLE `quotes` ADD INDEX IF NOT EXISTS `idx_quotes_product` (`product_id`);
ALTER TABLE `quotes` ADD INDEX IF NOT EXISTS `idx_quotes_status` (`status`);
ALTER TABLE `products` ADD INDEX IF NOT EXISTS `idx_products_status` (`status`);
ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `product_url` VARCHAR(2048) DEFAULT NULL AFTER `image`;
ALTER TABLE `categories` ADD INDEX IF NOT EXISTS `idx_categories_status` (`status`);
ALTER TABLE `brands` ADD INDEX IF NOT EXISTS `idx_brands_status` (`status`);
ALTER TABLE `products` DROP COLUMN IF EXISTS `badge`;
