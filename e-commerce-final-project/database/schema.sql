-- Furniture store database schema (structure only, no data).
-- Built from the CrownCare store tables: customers and sign-in, catalogue,
-- cart, delivery areas, orders and payments. Haircare, quiz and consultation
-- tables are not included.
--
-- Local setup: create an empty database (e.g. furniture_store) in
-- phpMyAdmin, select it, and import this file. New changes go in
-- database/migrations/ as numbered files (001_..., 002_...).

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `cc_customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_pass` varchar(150) NOT NULL,
  `customer_country` varchar(30) NOT NULL,
  `customer_city` varchar(30) NOT NULL,
  `customer_contact` varchar(15) NOT NULL,
  `customer_image` varchar(100) DEFAULT NULL,
  `user_role` int(11) NOT NULL DEFAULT 2,
  `customer_status` varchar(20) NOT NULL DEFAULT 'active',
  `closed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `unique_customer_email` (`customer_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `cc_login_attempts` (
  `attempt_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempted_at` datetime NOT NULL,
  PRIMARY KEY (`attempt_id`),
  KEY `cc_idx_login_email_time` (`email`,`attempted_at`),
  KEY `cc_idx_login_ip_time` (`ip_address`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `cc_password_resets` (
  `reset_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`reset_id`),
  UNIQUE KEY `cc_uniq_reset_token` (`token_hash`),
  KEY `cc_idx_reset_customer` (`customer_id`),
  CONSTRAINT `cc_fk_reset_customer` FOREIGN KEY (`customer_id`) REFERENCES `cc_customer` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `cc_categories` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(100) NOT NULL,
  `parent_category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `fk_category_parent` (`parent_category_id`),
  CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_category_id`) REFERENCES `cc_categories` (`cat_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `cc_products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_cat` int(11) NOT NULL,
  `product_title` varchar(200) NOT NULL,
  `product_price` double NOT NULL,
  `product_desc` varchar(500) DEFAULT NULL,
  `product_image` varchar(100) DEFAULT NULL,
  `product_keywords` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_stock` int(11) NOT NULL DEFAULT 20,
  PRIMARY KEY (`product_id`),
  KEY `idx_product_cat` (`product_cat`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`product_cat`) REFERENCES `cc_categories` (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `cc_product_sizes` (
  `size_id` int(11) NOT NULL AUTO_INCREMENT,
  `size_name` varchar(50) NOT NULL,
  PRIMARY KEY (`size_id`),
  UNIQUE KEY `unique_size_name` (`size_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `cc_product_size_options` (
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `price` double DEFAULT NULL,
  `stock_qty` int(11) NOT NULL DEFAULT 20,
  PRIMARY KEY (`product_id`,`size_id`),
  KEY `fk_product_size_size` (`size_id`),
  CONSTRAINT `fk_product_size_product` FOREIGN KEY (`product_id`) REFERENCES `cc_products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_product_size_size` FOREIGN KEY (`size_id`) REFERENCES `cc_product_sizes` (`size_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `cc_cart` (
  `c_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL DEFAULT 0,
  `qty` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`c_id`,`p_id`,`size_id`),
  KEY `idx_cart_product` (`p_id`),
  CONSTRAINT `fk_cart_customer` FOREIGN KEY (`c_id`) REFERENCES `cc_customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`p_id`) REFERENCES `cc_products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `cc_delivery_areas` (
  `area_id` int(11) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(30) NOT NULL,
  `delivery_fee` decimal(8,2) NOT NULL DEFAULT 20.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`area_id`),
  UNIQUE KEY `cc_uniq_area_name` (`area_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `cc_orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `invoice_no` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `order_status` varchar(100) NOT NULL DEFAULT 'pending',
  `delivery_name` varchar(100) NOT NULL DEFAULT '',
  `delivery_phone` varchar(20) NOT NULL DEFAULT '',
  `delivery_city` varchar(100) NOT NULL DEFAULT '',
  `delivery_address` varchar(255) NOT NULL DEFAULT '',
  `delivery_notes` varchar(255) DEFAULT NULL,
  `subtotal` double NOT NULL DEFAULT 0,
  `delivery_fee` double NOT NULL DEFAULT 0,
  `total_amount` double NOT NULL DEFAULT 0,
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `delivery_area_id` int(11) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `status_updated_at` datetime DEFAULT NULL,
  `stock_deducted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`order_id`),
  KEY `idx_orders_customer` (`customer_id`),
  CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `cc_customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `cc_orderdetails` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL DEFAULT 0,
  `qty` int(11) NOT NULL,
  `unit_price` double DEFAULT NULL,
  PRIMARY KEY (`order_id`,`product_id`,`size_id`),
  KEY `idx_orderdetails_product` (`product_id`),
  CONSTRAINT `fk_orderdetails_order` FOREIGN KEY (`order_id`) REFERENCES `cc_orders` (`order_id`),
  CONSTRAINT `fk_orderdetails_product` FOREIGN KEY (`product_id`) REFERENCES `cc_products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `cc_payment` (
  `pay_id` int(11) NOT NULL AUTO_INCREMENT,
  `amt` double NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `channel` varchar(50) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `payment_date` date NOT NULL,
  PRIMARY KEY (`pay_id`),
  UNIQUE KEY `unique_payment_reference` (`reference`),
  KEY `idx_payment_customer` (`customer_id`),
  KEY `idx_payment_order` (`order_id`),
  CONSTRAINT `fk_payment_customer` FOREIGN KEY (`customer_id`) REFERENCES `cc_customer` (`customer_id`),
  CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `cc_orders` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
