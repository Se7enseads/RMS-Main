/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.4.12-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: rms
-- ------------------------------------------------------
-- Server version	11.4.12-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Current Database: `rms`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `rms` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `rms`;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `method` varchar(10) NOT NULL DEFAULT 'GET',
  `url` varchar(255) NOT NULL DEFAULT '',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES
(1,1,'Staff logged in','POST','/login','::1','2026-08-19 04:22:35'),
(2,1,'Staff logged out','POST','/logout','::1','2026-08-19 04:23:51'),
(3,1,'Staff logged in','POST','/login','::1','2026-08-19 04:24:02'),
(4,1,'User created: WTR001','POST','/admin/users/create','::1','2026-08-19 04:31:58'),
(5,1,'Staff logged out','POST','/logout','::1','2026-08-19 04:32:31'),
(6,1,'Staff logged in','POST','/login','::1','2026-08-19 04:32:45'),
(7,1,'Staff logged out','POST','/logout','::1','2026-08-19 04:33:03'),
(8,263,'Staff logged in','POST','/login','::1','2026-08-19 04:33:07'),
(9,263,'Staff logged out','POST','/logout','::1','2026-08-19 04:45:34'),
(10,1,'Staff logged in','POST','/login','::1','2026-08-19 04:46:11'),
(11,1,'Category deactivated: E2E Grills 5edc99','POST','/admin/categories/deactivate/4','::1','2026-08-19 04:46:21'),
(12,1,'Category deactivated: E2E Grills be800f','POST','/admin/categories/deactivate/3','::1','2026-08-19 04:46:28'),
(13,1,'Category deactivated: E2E Grills 3ef802','POST','/admin/categories/deactivate/2','::1','2026-08-19 04:46:31'),
(14,1,'Category deactivated: E2E Grills 591896','POST','/admin/categories/deactivate/1','::1','2026-08-19 04:46:36'),
(15,1,'Category created: Drinks','POST','/admin/categories/create','::1','2026-08-19 04:46:58'),
(16,1,'Category created: Mains','POST','/admin/categories/create','::1','2026-08-19 04:47:14'),
(17,1,'Ingredient deactivated: E2E Water 77cd77','POST','/store/inventory/deactivate/4','::1','2026-08-19 04:47:44'),
(18,1,'Ingredient deactivated: E2E Water 3a62ac','POST','/store/inventory/deactivate/2','::1','2026-08-19 04:47:47'),
(19,1,'Ingredient deactivated: E2E Water 4b3019','POST','/store/inventory/deactivate/3','::1','2026-08-19 04:47:52'),
(20,1,'Ingredient deactivated: E2E Water e7a011','POST','/store/inventory/deactivate/1','::1','2026-08-19 04:47:58'),
(21,1,'Ingredient created: Water (1ltr)','POST','/store/inventory/create','::1','2026-08-19 04:49:27'),
(22,1,'Staff logged in','POST','/login','::1','2026-08-19 04:53:19'),
(23,1,'Menu item created: Water (1ltr)','POST','/admin/items/create','::1','2026-08-19 04:54:18'),
(24,1,'Staff logged out','POST','/logout','::1','2026-08-19 04:54:29'),
(25,263,'Staff logged in','POST','/login','::1','2026-08-19 04:54:33'),
(26,263,'Order placed: ORD-20260819-B043','POST','/kiosk/order','::1','2026-08-19 04:56:26'),
(27,263,'Bill printed: ORD-20260819-B043','GET','/kiosk/order/1/bill','::1','2026-08-19 04:56:32'),
(28,263,'Bill printed: ORD-20260819-B043','GET','/kiosk/order/1/bill','::1','2026-08-19 04:57:09'),
(29,263,'Bill printed: ORD-20260819-B043','GET','/kiosk/order/1/bill','::1','2026-08-19 04:57:23'),
(30,263,'Bill printed: ORD-20260819-B043','GET','/kiosk/order/1/bill','::1','2026-08-19 05:03:48'),
(31,263,'Staff logged in','POST','/login','::1','2026-08-19 05:23:02'),
(32,263,'Bill printed: ORD-20260819-B043','GET','/kiosk/order/1/bill','::1','2026-08-19 05:23:05'),
(33,263,'Staff logged in','POST','/login','::1','2026-08-19 06:36:27'),
(34,263,'Bill printed: ORD-20260819-B043','GET','/kiosk/order/1/bill','::1','2026-08-19 06:36:40'),
(35,263,'Staff logged out','POST','/logout','::1','2026-08-19 06:42:48'),
(36,1,'Staff logged in','POST','/login','::1','2026-08-19 06:43:00'),
(37,1,'Staff logged in','POST','/login','::1','2026-08-19 07:19:49'),
(38,263,'Staff logged in','POST','/login','::1','2026-08-19 15:22:08'),
(39,263,'Staff logged out','POST','/logout','::1','2026-08-19 15:22:34'),
(40,1,'Staff logged in','POST','/login','::1','2026-08-19 15:22:50'),
(41,263,'Staff logged in','POST','/login','::1','2026-08-19 16:39:07'),
(42,263,'Staff logged out','POST','/logout','::1','2026-08-19 16:40:00'),
(43,1,'Staff logged in','POST','/login','::1','2026-08-19 16:40:15'),
(44,263,'Staff logged in','POST','/login','::1','2026-08-24 06:41:16'),
(45,263,'Order placed: ORD-20260824-40F6','POST','/kiosk/order','::1','2026-08-24 06:41:56'),
(46,263,'Bill printed: ORD-20260819-B043','GET','/kiosk/order/1/bill','::1','2026-08-24 06:42:50'),
(47,263,'Staff logged out','POST','/logout','::1','2026-08-24 06:43:58'),
(48,1,'Staff logged in','POST','/login','::1','2026-08-24 06:44:13'),
(49,1,'Ingredient created: Beef','POST','/store/inventory/create','::1','2026-08-24 06:47:17'),
(50,1,'Menu item created: Roast Beef','POST','/admin/items/create','::1','2026-08-24 06:47:45'),
(51,1,'Staff logged out','POST','/logout','::1','2026-08-24 06:47:50'),
(52,263,'Staff logged in','POST','/login','::1','2026-08-24 06:47:54'),
(53,263,'Order placed: ORD-20260824-807A','POST','/kiosk/order','::1','2026-08-24 06:48:08'),
(54,263,'Staff logged out','POST','/logout','::1','2026-08-24 06:48:18'),
(55,1,'Staff logged in','POST','/login','::1','2026-08-24 06:48:28'),
(56,1,'Staff logged out','POST','/logout','::1','2026-08-24 06:49:41'),
(57,1,'Staff logged in','POST','/login','::1','2026-08-24 06:50:05'),
(58,1,'Staff logged out','POST','/logout','::1','2026-08-24 06:52:09'),
(59,263,'Staff logged in','POST','/login','::1','2026-09-07 15:30:32'),
(60,263,'Staff logged out','POST','/logout','::1','2026-09-07 15:42:19'),
(61,1,'Staff logged in','POST','/login','::1','2026-09-07 15:44:37'),
(62,1,'Staff logged in','POST','/login','::1','2026-09-07 16:03:45'),
(63,1,'User created: BTR001','POST','/admin/users/create','::1','2026-09-07 16:10:59'),
(64,263,'Staff logged in','POST','/login','::1','2026-09-07 17:44:02'),
(65,263,'Order placed: ORD-20260907-6224','POST','/kiosk/order','::1','2026-09-07 17:44:59'),
(66,263,'Staff logged out','POST','/logout','::1','2026-09-07 17:45:22'),
(67,263,'Staff logged in','POST','/login','::1','2026-09-07 17:45:29'),
(68,263,'Staff logged out','POST','/logout','::1','2026-09-07 17:46:22'),
(69,264,'Staff logged in','POST','/login','::1','2026-09-07 17:46:29'),
(70,264,'Staff logged out','POST','/logout','::1','2026-09-07 17:46:47'),
(71,1,'Staff logged in','POST','/login','::1','2026-09-07 17:46:55'),
(72,1,'User created: 1234','POST','/admin/users/create','::1','2026-09-07 17:48:29'),
(73,263,'Staff logged in','POST','/login','::1','2026-09-11 14:32:12'),
(74,263,'Bill printed: ORD-20260824-807A','GET','/kiosk/order/3/bill','::1','2026-09-11 14:32:17'),
(75,263,'Staff logged in','POST','/login','::1','2026-09-12 00:38:52'),
(76,263,'Staff logged in','POST','/login','::1','2026-09-12 01:37:55'),
(77,263,'Bill printed: ORD-20260907-6224','GET','/kiosk/order/4/bill','::1','2026-09-12 01:37:59'),
(78,263,'Staff logged out','POST','/logout','::1','2026-09-12 02:02:04'),
(79,1,'Staff logged in','POST','/login','::1','2026-09-12 02:02:11'),
(80,1,'Business day closed','POST','/admin/close-day','::1','2026-09-12 02:02:35'),
(81,1,'Staff logged in','POST','/login','::1','2026-09-12 04:32:09'),
(82,1,'Staff logged out','POST','/logout','::1','2026-09-12 04:43:10'),
(83,263,'Staff logged in','POST','/login','::1','2026-09-12 04:43:15'),
(84,263,'Staff logged in','POST','/login','::1','2026-09-12 05:03:01'),
(85,263,'Staff logged out','POST','/logout','::1','2026-09-12 05:03:08'),
(86,1,'Staff logged in','POST','/login','::1','2026-09-12 05:03:10'),
(87,1,'Staff logged in','POST','/login','::1','2026-09-12 06:38:51'),
(88,1,'Staff logged in','POST','/login','::1','2026-09-14 16:39:16'),
(89,1,'Staff logged out','POST','/logout','::1','2026-09-14 16:41:07'),
(90,270,'Staff logged in','POST','/login','::1','2026-09-14 16:41:42'),
(91,263,'Staff logged in','POST','/login','::1','2026-09-14 16:52:20'),
(92,269,'Staff logged in','POST','/login','::1','2026-09-14 16:53:37'),
(93,263,'Order placed: ORD-20260914-091E','POST','/kiosk/order','::1','2026-09-14 17:01:14'),
(94,269,'Staff logged out','POST','/logout','::1','2026-09-14 17:01:28'),
(95,264,'Staff logged in','POST','/login','::1','2026-09-14 17:01:34'),
(96,264,'Bar order item served: 112','POST','/bar/serve/112','::1','2026-09-14 17:01:48'),
(97,264,'Order placed: ORD-20260914-2DBC','POST','/bar/order','::1','2026-09-14 17:02:15'),
(98,264,'Bar order item served: 113','POST','/bar/serve/113','::1','2026-09-14 17:02:19'),
(99,264,'Staff logged out','POST','/logout','::1','2026-09-14 17:02:28'),
(100,269,'Staff logged in','POST','/login','::1','2026-09-14 17:02:33'),
(101,269,'Bill printed: ORD-20260913-4AD4','GET','/kiosk/order/104/bill','::1','2026-09-14 17:09:16'),
(102,263,'Staff logged out','POST','/logout','::1','2026-09-14 17:15:39'),
(103,269,'Staff logged in','POST','/login','::1','2026-09-14 17:16:00'),
(104,265,'Staff logged in','POST','/login','::1','2026-09-14 17:16:17'),
(105,269,'Staff logged out','POST','/logout','::1','2026-09-14 17:17:15'),
(106,263,'Staff logged in','POST','/login','::1','2026-09-14 17:17:23'),
(107,269,'Payment received for ORD-20260913-6DE5 (CASH KES 1,200.00)','POST','/cashier/orders/102/pay','::1','2026-09-14 17:20:20'),
(108,269,'Payment received for ORD-20260913-95E9 (CASH KES 1,100.00)','POST','/cashier/orders/103/pay','::1','2026-09-14 17:20:22'),
(109,269,'Payment received for ORD-20260913-4AD4 (CASH KES 800.00)','POST','/cashier/orders/104/pay','::1','2026-09-14 17:20:26'),
(110,269,'Payment received for ORD-20260913-B274 (CASH KES 2,150.00)','POST','/cashier/orders/101/pay','::1','2026-09-14 17:20:28'),
(111,269,'Payment received for ORD-20260913-B201 (CASH KES 450.00)','POST','/cashier/orders/105/pay','::1','2026-09-14 17:20:35'),
(112,269,'Payment received for ORD-20260913-9144 (CASH KES 4,800.00)','POST','/cashier/orders/100/pay','::1','2026-09-14 17:20:39'),
(113,269,'Bill printed: ORD-20260913-9144','GET','/kiosk/order/100/bill','::1','2026-09-14 17:20:43'),
(114,1,'Staff logged in','POST','/login','::1','2026-09-14 17:22:00'),
(115,269,'Staff logged in','POST','/login','::1','2026-09-14 17:30:55'),
(116,264,'Staff logged in','POST','/login','::1','2026-09-14 17:31:18'),
(117,1,'Staff logged in','POST','/login','::1','2026-09-14 18:13:15'),
(118,1,'Staff logged in','POST','/login','::1','2026-09-14 19:06:30'),
(119,1,'User created: WT005','POST','/admin/users/create','::1','2026-09-14 19:07:12'),
(120,1,'Staff logged out','POST','/logout','::1','2026-09-14 19:08:00'),
(121,271,'Staff logged in','POST','/login','::1','2026-09-14 19:08:05'),
(122,271,'Staff logged out','POST','/logout','::1','2026-09-14 19:08:43'),
(123,263,'Staff logged in','POST','/login','::1','2026-09-14 19:09:03'),
(124,263,'Staff logged out','POST','/logout','::1','2026-09-14 19:20:19'),
(125,263,'Staff logged in','POST','/login','::1','2026-09-14 19:20:38'),
(126,263,'Order placed: ORD-20260914-0969','POST','/kiosk/order','::1','2026-09-14 19:21:11'),
(127,265,'Staff logged in','POST','/login','::1','2026-09-14 19:21:43'),
(128,265,'Kitchen order item served: 114','POST','/kitchen/serve/114','::1','2026-09-14 19:21:52'),
(129,263,'Bill printed: ORD-20260914-0969','GET','/kiosk/order/114/bill','::1','2026-09-14 19:22:23'),
(130,269,'Staff logged in','POST','/login','::1','2026-09-14 19:22:49'),
(131,269,'Bill printed: ORD-20260913-9144','GET','/kiosk/order/100/bill','::1','2026-09-14 19:23:04'),
(132,269,'Bill printed: ORD-20260913-9144','GET','/kiosk/order/100/bill','::1','2026-09-14 19:23:23'),
(133,263,'Staff logged out','POST','/logout','::1','2026-09-14 19:23:37'),
(134,1,'Staff logged in','POST','/login','::1','2026-09-14 19:23:41'),
(135,1,'Staff logged in','POST','/login','::1','2026-09-14 19:43:13'),
(136,263,'Staff logged in','POST','/login','::1','2026-09-15 02:43:22'),
(137,264,'Staff logged in','POST','/login','::1','2026-09-15 02:46:14'),
(138,264,'Staff logged out','POST','/logout','::1','2026-09-15 02:46:25'),
(139,1,'Staff logged in','POST','/login','::1','2026-09-15 02:46:28'),
(140,1,'Business day closed','POST','/admin/close-day','::1','2026-09-15 02:46:50');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_days`
--

DROP TABLE IF EXISTS `business_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `business_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `closed_by` int(11) DEFAULT NULL,
  `order_count` int(11) NOT NULL DEFAULT 0,
  `item_count` int(11) NOT NULL DEFAULT 0,
  `gross_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unpaid_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cash_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `card_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `mobile_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`),
  KEY `closed_by` (`closed_by`),
  KEY `is_closed` (`is_closed`),
  CONSTRAINT `business_days_ibfk_1` FOREIGN KEY (`closed_by`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_days`
--

LOCK TABLES `business_days` WRITE;
/*!40000 ALTER TABLE `business_days` DISABLE KEYS */;
INSERT INTO `business_days` VALUES
(1,'2026-09-12',1,'2026-09-12 02:02:11','2026-09-12 02:02:35',1,0,0,0.00,0.00,0.00,0.00,0.00,0.00),
(2,'2026-09-13',1,'2026-09-12 02:02:35','2026-09-15 02:46:50',1,11,8,17300.00,17300.00,0.00,1150.00,3100.00,2550.00),
(3,'2026-09-14',0,'2026-09-15 02:46:50',NULL,NULL,0,0,0.00,0.00,0.00,0.00,0.00,0.00);
/*!40000 ALTER TABLE `business_days` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `base_unit` enum('g','ml','pcs') NOT NULL DEFAULT 'pcs',
  `receive_unit` enum('g','ml','pcs','case','packet','carton','box') NOT NULL DEFAULT 'pcs',
  `units_per_container` decimal(8,2) DEFAULT NULL,
  `stock` decimal(12,3) NOT NULL DEFAULT 0.000,
  `cost_per_unit` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `reorder_level` decimal(12,3) NOT NULL DEFAULT 0.000,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES
(1,'E2E Water e7a011','2026-08-19 01:33:19','2026-08-19 04:47:58','pcs','case',12.00,24.000,50.0000,0.000,0),
(2,'E2E Water 3a62ac','2026-08-19 01:37:45','2026-08-19 04:47:47','pcs','case',12.00,24.000,50.0000,0.000,0),
(3,'E2E Water 4b3019','2026-08-19 01:44:04','2026-08-19 04:47:52','pcs','case',12.00,24.000,50.0000,0.000,0),
(4,'E2E Water 77cd77','2026-08-19 01:47:59','2026-08-19 04:47:44','pcs','case',12.00,24.000,50.0000,0.000,0),
(5,'Water (1ltr)','2026-08-19 04:49:27','2026-08-19 04:49:27','pcs','carton',12.00,3600.000,9.1667,200.000,1),
(6,'Beef','2026-08-24 06:47:17','2026-09-12 04:38:35','g','g',1000.00,8000.000,1.0000,2000.000,1),
(7,'Chicken Breast','2026-09-12 04:38:06','2026-09-12 04:38:06','g','box',20.00,8000.000,0.3500,2000.000,1),
(8,'Beef Mince','2026-09-12 04:38:06','2026-09-12 04:38:06','g','box',10.00,4000.000,0.3500,1000.000,1),
(9,'Fish Fillets','2026-09-12 04:38:06','2026-09-12 04:38:06','g','box',10.00,5000.000,0.4500,1500.000,1),
(10,'Potatoes','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',50.00,12000.000,0.0800,3000.000,1),
(11,'Cooking Oil','2026-09-12 04:38:06','2026-09-12 04:38:06','ml','case',12.00,15000.000,0.1200,4000.000,1),
(12,'Rice','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',25.00,20000.000,0.1000,5000.000,1),
(13,'Wheat Flour','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',10.00,10000.000,0.0900,3000.000,1),
(14,'Maize Flour','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',10.00,8000.000,0.0800,2000.000,1),
(15,'Collard Greens','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',5.00,6000.000,0.0500,1500.000,1),
(16,'Tomatoes','2026-09-12 04:38:06','2026-09-12 04:38:06','g','case',20.00,5000.000,0.1000,2000.000,1),
(17,'Onions','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',10.00,4000.000,0.0800,1500.000,1),
(18,'Garlic','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',5.00,1500.000,0.3000,500.000,1),
(19,'Ginger','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',5.00,1200.000,0.3000,400.000,1),
(20,'Bell Pepper','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',10.00,800.000,0.2500,300.000,1),
(21,'Salt','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',20.00,20000.000,0.0100,5000.000,1),
(22,'Black Pepper','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',5.00,1000.000,0.5000,300.000,1),
(23,'Sugar','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',20.00,15000.000,0.0600,4000.000,1),
(24,'Milk','2026-09-12 04:38:06','2026-09-12 04:38:06','ml','carton',10.00,10000.000,0.0600,3000.000,1),
(25,'Butter','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',10.00,3000.000,0.3500,800.000,1),
(26,'Mozzarella Cheese','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',5.00,1500.000,0.8000,400.000,1),
(27,'Chocolate','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',10.00,2000.000,0.7000,500.000,1),
(28,'Coffee Beans','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',5.00,4000.000,0.3000,1000.000,1),
(29,'Tea Leaves','2026-09-12 04:38:06','2026-09-12 04:38:06','g','packet',5.00,3000.000,0.2500,800.000,1),
(30,'Samosa Pastry','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','packet',50.00,1000.000,20.0000,300.000,1),
(31,'Chicken Marinade','2026-09-12 04:38:06','2026-09-12 04:38:06','ml','packet',5.00,5000.000,0.2000,1000.000,1),
(32,'Ice Cream','2026-09-12 04:38:06','2026-09-12 04:38:06','ml','carton',10.00,10000.000,0.1500,2000.000,1),
(33,'Avocado','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',50.00,800.000,80.0000,200.000,1),
(34,'Oranges','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',100.00,1500.000,30.0000,400.000,1),
(35,'Mangoes','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',50.00,1000.000,50.0000,300.000,1),
(36,'Bananas','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',100.00,1500.000,10.0000,400.000,1),
(37,'Limes','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',50.00,600.000,5.0000,200.000,1),
(38,'Lettuce','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',50.00,500.000,25.0000,150.000,1),
(39,'Wheat Buns','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',50.00,800.000,20.0000,200.000,1),
(40,'Coca-Cola','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',24.00,480.000,60.0000,120.000,1),
(41,'Fanta Orange','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',24.00,480.000,60.0000,120.000,1),
(42,'Sprite','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',24.00,480.000,60.0000,120.000,1),
(43,'Tusker Beer','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',12.00,200.000,180.0000,50.000,1),
(44,'White Cap Beer','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',12.00,160.000,180.0000,40.000,1),
(45,'Soda Water','2026-09-12 04:38:06','2026-09-12 04:38:06','pcs','case',24.00,200.000,45.0000,50.000,1);
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_movements`
--

DROP TABLE IF EXISTS `inventory_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) NOT NULL,
  `movement_type` enum('IN','OUT','ADJUSTMENT') NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `reference_type` enum('ORDER','MANUAL','STOCK_TAKE') DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `performed_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `unit` enum('g','ml','pcs','case','packet','carton','box') NOT NULL DEFAULT 'pcs',
  `unit_cost` decimal(12,4) NOT NULL DEFAULT 0.0000,
  PRIMARY KEY (`id`),
  KEY `inventory_id` (`inventory_id`),
  KEY `performed_by` (`performed_by`),
  KEY `created_at` (`created_at`),
  KEY `movement_type` (`movement_type`),
  KEY `reference_type` (`reference_type`,`reference_id`),
  CONSTRAINT `inventory_movements_ibfk_1` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`),
  CONSTRAINT `inventory_movements_ibfk_2` FOREIGN KEY (`performed_by`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_movements`
--

LOCK TABLES `inventory_movements` WRITE;
/*!40000 ALTER TABLE `inventory_movements` DISABLE KEYS */;
INSERT INTO `inventory_movements` VALUES
(1,1,'IN',2.00,'MANUAL',NULL,1,'2026-08-19 01:33:19','case',600.0000),
(2,2,'IN',2.00,'MANUAL',NULL,1,'2026-08-19 01:37:45','case',600.0000),
(3,3,'IN',2.00,'MANUAL',NULL,1,'2026-08-19 01:44:04','case',600.0000),
(4,4,'IN',2.00,'MANUAL',NULL,1,'2026-08-19 01:47:59','case',600.0000),
(5,5,'IN',300.00,'MANUAL',NULL,1,'2026-08-19 04:49:27','carton',110.0000),
(6,6,'IN',4000.00,'MANUAL',NULL,1,'2026-08-24 06:47:17','g',1000.0000);
/*!40000 ALTER TABLE `inventory_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_categories`
--

DROP TABLE IF EXISTS `menu_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `station` enum('KITCHEN','BAR') NOT NULL DEFAULT 'KITCHEN',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `menu_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menu_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_categories`
--

LOCK TABLES `menu_categories` WRITE;
/*!40000 ALTER TABLE `menu_categories` DISABLE KEYS */;
INSERT INTO `menu_categories` VALUES
(1,'E2E Grills 591896',NULL,0,'KITCHEN','2026-08-19 01:33:18','2026-08-19 04:46:36'),
(2,'E2E Grills 3ef802',NULL,0,'KITCHEN','2026-08-19 01:37:45','2026-08-19 04:46:31'),
(3,'E2E Grills be800f',NULL,0,'KITCHEN','2026-08-19 01:44:03','2026-08-19 04:46:28'),
(4,'E2E Grills 5edc99',NULL,0,'KITCHEN','2026-08-19 01:47:59','2026-08-19 04:46:21'),
(5,'Drinks',NULL,1,'BAR','2026-08-19 04:46:58','2026-08-19 04:46:58'),
(6,'Mains',NULL,1,'KITCHEN','2026-08-19 04:47:14','2026-08-19 04:47:14'),
(7,'Starters',NULL,1,'KITCHEN','2026-09-12 04:38:06','2026-09-12 04:38:06'),
(8,'Desserts',NULL,1,'KITCHEN','2026-09-12 04:38:06','2026-09-12 04:38:06');
/*!40000 ALTER TABLE `menu_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_item_ingredients`
--

DROP TABLE IF EXISTS `menu_item_ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_item_ingredients` (
  `menu_item_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  PRIMARY KEY (`menu_item_id`,`inventory_id`),
  KEY `inventory_id` (`inventory_id`),
  CONSTRAINT `menu_item_ingredients_ibfk_1` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`),
  CONSTRAINT `menu_item_ingredients_ibfk_2` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_item_ingredients`
--

LOCK TABLES `menu_item_ingredients` WRITE;
/*!40000 ALTER TABLE `menu_item_ingredients` DISABLE KEYS */;
INSERT INTO `menu_item_ingredients` VALUES
(13,8,120.00,'g'),
(13,11,30.00,'ml'),
(13,17,20.00,'g'),
(13,21,2.00,'g'),
(13,30,3.00,'pcs'),
(14,7,300.00,'g'),
(14,11,20.00,'ml'),
(14,22,2.00,'g'),
(14,31,60.00,'ml'),
(15,11,40.00,'ml'),
(15,13,60.00,'g'),
(15,17,150.00,'g'),
(15,21,2.00,'g'),
(16,18,5.00,'g'),
(16,25,15.00,'g'),
(16,26,40.00,'g'),
(16,39,1.00,'pcs'),
(17,16,80.00,'g'),
(17,17,40.00,'g'),
(17,21,2.00,'g'),
(17,22,1.00,'g'),
(17,33,1.00,'pcs'),
(18,7,250.00,'g'),
(18,11,20.00,'ml'),
(18,21,3.00,'g'),
(18,22,2.00,'g'),
(18,31,50.00,'ml'),
(19,6,400.00,'g'),
(19,16,40.00,'g'),
(19,17,40.00,'g'),
(19,21,4.00,'g'),
(19,22,2.00,'g'),
(20,12,250.00,'g'),
(20,17,40.00,'g'),
(20,18,5.00,'g'),
(20,19,5.00,'g'),
(20,21,3.00,'g'),
(21,6,250.00,'g'),
(21,11,20.00,'ml'),
(21,16,100.00,'g'),
(21,17,60.00,'g'),
(21,18,5.00,'g'),
(21,21,3.00,'g'),
(21,22,2.00,'g'),
(22,9,200.00,'g'),
(22,10,250.00,'g'),
(22,11,40.00,'ml'),
(22,13,50.00,'g'),
(22,21,3.00,'g'),
(23,16,40.00,'g'),
(23,17,30.00,'g'),
(23,26,20.00,'g'),
(23,33,1.00,'pcs'),
(23,38,1.00,'pcs'),
(23,39,1.00,'pcs'),
(24,11,15.00,'ml'),
(24,13,120.00,'g'),
(24,21,2.00,'g'),
(25,11,15.00,'ml'),
(25,14,200.00,'g'),
(25,15,120.00,'g'),
(25,21,2.00,'g'),
(26,40,1.00,'pcs'),
(27,41,1.00,'pcs'),
(28,42,1.00,'pcs'),
(29,43,1.00,'pcs'),
(30,44,1.00,'pcs'),
(31,34,3.00,'pcs'),
(32,35,2.00,'pcs'),
(33,23,10.00,'g'),
(33,37,2.00,'pcs'),
(33,45,1.00,'pcs'),
(34,19,3.00,'g'),
(34,23,15.00,'g'),
(34,24,150.00,'ml'),
(34,29,5.00,'g'),
(35,23,10.00,'g'),
(35,24,100.00,'ml'),
(35,28,15.00,'g'),
(36,13,50.00,'g'),
(36,23,40.00,'g'),
(36,24,30.00,'ml'),
(36,25,20.00,'g'),
(36,27,40.00,'g'),
(37,32,150.00,'ml'),
(38,34,1.00,'pcs'),
(38,35,1.00,'pcs'),
(38,36,1.00,'pcs'),
(39,13,80.00,'g'),
(39,23,20.00,'g'),
(39,24,80.00,'ml'),
(39,25,15.00,'g'),
(39,36,2.00,'pcs');
/*!40000 ALTER TABLE `menu_item_ingredients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_combo` tinyint(1) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `name` (`name`),
  KEY `active` (`active`),
  CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES
(11,'Water (1ltr)','',1100.00,5,0,1,'2026-08-19 04:54:18','2026-08-19 04:54:18'),
(12,'Roast Beef','Beef',1500.00,6,0,1,'2026-08-24 06:47:45','2026-08-24 06:47:45'),
(13,'Beef Samosas (3 pcs)','Crispy pastry parcels filled with spiced beef mince.',350.00,7,0,1,'2026-09-12 04:38:06','2026-09-12 04:38:06'),
(14,'Chicken Wings (6 pcs)','Grilled wings tossed in house marinade.',550.00,7,0,1,'2026-09-12 04:38:06','2026-09-12 04:38:06'),
(15,'Onion Rings','Golden battered onion rings.',300.00,7,0,1,'2026-09-12 04:38:06','2026-09-12 04:38:06'),
(16,'Garlic Cheese Bread','Toasted bread topped with garlic butter and mozzarella.',400.00,7,0,1,'2026-09-12 04:38:06','2026-09-12 04:38:06'),
(17,'Avocado Salad','Fresh avocado, tomato and onion salad.',450.00,7,0,1,'2026-09-12 04:38:06','2026-09-12 04:38:06'),
(18,'Grilled Chicken (Quarter)','Flame grilled quarter chicken with marinade.',650.00,6,0,1,'2026-09-12 04:38:06','2026-09-12 04:38:06'),
(19,'Nyama Choma','Slow roasted goat-style beef ribs, served with kachumbari.',1200.00,6,0,1,'2026-09-12 04:38:06','2026-09-12 04:38:06'),
(20,'Pilau Rice','Fragrant spiced rice cooked in beef stock.',500.00,6,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(21,'Beef Stew','Slow cooked beef in rich tomato gravy.',950.00,6,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(22,'Fish & Chips','Battered fish fillet with chunky chips.',800.00,6,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(23,'Veggie Burger','Grilled vegetable patty burger with fresh toppings.',450.00,6,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(24,'Chapati','Soft layered flatbread.',150.00,6,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(25,'Ugali & Sukuma','Stiff maize meal with sauteed collard greens.',250.00,6,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(26,'Coca-Cola (330ml)','Chilled cola in a glass bottle.',100.00,5,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(27,'Fanta Orange (330ml)','Chilled orange soda.',100.00,5,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(28,'Sprite (330ml)','Chilled lemon-lime soda.',100.00,5,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(29,'Tusker Beer (500ml)','Kenyan lager, served ice cold.',250.00,5,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(30,'White Cap Beer (500ml)','Smooth light lager.',250.00,5,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(31,'Fresh Orange Juice','Freshly squeezed oranges.',300.00,5,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(32,'Mango Juice','Thick fresh mango juice.',300.00,5,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(33,'Fresh Lime Soda','Sparkling soda with fresh lime and sugar.',200.00,5,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(34,'Masala Tea','Spiced milk tea.',150.00,5,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(35,'Cappuccino','Espresso with steamed milk and foam.',300.00,5,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(36,'Chocolate Cake (Slice)','Rich moist chocolate cake.',350.00,8,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(37,'Vanilla Ice Cream','Two scoops of vanilla ice cream.',250.00,8,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(38,'Fruit Salad','Seasonal mixed fruit cup.',250.00,8,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35'),
(39,'Banana Pancakes','Fluffy pancakes with caramelised banana.',280.00,8,0,1,'2026-09-12 04:38:35','2026-09-12 04:38:35');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `price_at_time` decimal(12,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `served` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `menu_item_id` (`menu_item_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES
(6,6,31,300.00,2,0,'2026-09-12 04:39:09'),
(7,6,33,200.00,3,0,'2026-09-12 04:39:09'),
(8,7,19,1200.00,3,0,'2026-09-12 04:39:09'),
(9,8,39,280.00,3,0,'2026-09-12 04:39:09'),
(10,8,28,100.00,3,0,'2026-09-12 04:39:09'),
(11,8,35,300.00,2,0,'2026-09-12 04:39:09'),
(12,9,37,250.00,3,0,'2026-09-12 04:39:09'),
(13,9,29,250.00,2,0,'2026-09-12 04:39:09'),
(14,9,34,150.00,1,0,'2026-09-12 04:39:09'),
(15,10,14,550.00,1,0,'2026-09-12 04:39:09'),
(16,10,16,400.00,1,0,'2026-09-12 04:39:09'),
(17,10,33,200.00,3,0,'2026-09-12 04:39:09'),
(18,11,36,350.00,3,0,'2026-09-12 04:39:09'),
(19,12,22,800.00,3,0,'2026-09-12 04:39:09'),
(20,12,24,150.00,1,0,'2026-09-12 04:39:09'),
(21,13,20,500.00,3,0,'2026-09-12 04:39:09'),
(22,13,38,250.00,1,0,'2026-09-12 04:39:09'),
(23,13,32,300.00,2,0,'2026-09-12 04:39:09'),
(24,14,37,250.00,2,0,'2026-09-12 04:39:09'),
(25,14,26,100.00,2,0,'2026-09-12 04:39:09'),
(26,15,13,350.00,1,0,'2026-09-12 04:39:09'),
(27,15,21,950.00,2,0,'2026-09-12 04:39:09'),
(28,16,22,800.00,3,0,'2026-09-12 04:39:09'),
(29,17,16,400.00,1,0,'2026-09-12 04:39:09'),
(30,17,31,300.00,3,0,'2026-09-12 04:39:09'),
(31,17,33,200.00,3,0,'2026-09-12 04:39:09'),
(32,18,16,400.00,3,0,'2026-09-12 04:39:09'),
(33,19,15,300.00,3,0,'2026-09-12 04:39:09'),
(34,19,29,250.00,1,0,'2026-09-12 04:39:09'),
(35,19,32,300.00,2,0,'2026-09-12 04:39:09'),
(36,20,17,450.00,3,0,'2026-09-12 04:39:09'),
(37,20,31,300.00,2,0,'2026-09-12 04:39:09'),
(38,21,18,650.00,3,0,'2026-09-12 04:39:09'),
(39,21,27,100.00,2,0,'2026-09-12 04:39:09'),
(40,22,19,1200.00,3,0,'2026-09-12 04:39:09'),
(41,22,25,250.00,2,0,'2026-09-12 04:39:09'),
(42,22,34,150.00,2,0,'2026-09-12 04:39:09'),
(43,23,38,250.00,1,0,'2026-09-12 04:39:09'),
(44,23,28,100.00,3,0,'2026-09-12 04:39:09'),
(45,24,21,950.00,1,0,'2026-09-12 04:39:09'),
(46,25,21,950.00,3,0,'2026-09-12 04:39:09'),
(47,26,15,300.00,1,0,'2026-09-12 04:39:09'),
(48,26,30,250.00,2,0,'2026-09-12 04:39:09'),
(49,26,35,300.00,1,0,'2026-09-12 04:39:09'),
(50,27,28,100.00,1,0,'2026-09-12 04:39:09'),
(51,28,24,150.00,3,0,'2026-09-12 04:39:09'),
(52,28,37,250.00,2,0,'2026-09-12 04:39:09'),
(53,29,14,550.00,2,0,'2026-09-12 04:39:09'),
(54,29,27,100.00,2,0,'2026-09-12 04:39:09'),
(55,30,20,500.00,3,0,'2026-09-12 04:39:09'),
(56,30,36,350.00,2,0,'2026-09-12 04:39:09'),
(57,31,18,650.00,2,0,'2026-09-12 04:39:09'),
(58,31,24,150.00,2,0,'2026-09-12 04:39:09'),
(59,31,39,280.00,3,0,'2026-09-12 04:39:09'),
(60,32,26,100.00,3,0,'2026-09-12 04:39:09'),
(61,33,33,200.00,1,0,'2026-09-12 04:39:09'),
(62,34,21,950.00,3,0,'2026-09-12 04:39:09'),
(63,34,28,100.00,3,0,'2026-09-12 04:39:09'),
(64,34,35,300.00,2,0,'2026-09-12 04:39:09'),
(65,35,36,350.00,1,0,'2026-09-12 04:39:09'),
(66,35,27,100.00,2,0,'2026-09-12 04:39:09'),
(67,35,31,300.00,2,0,'2026-09-12 04:39:09'),
(68,36,18,650.00,3,0,'2026-09-12 04:39:09'),
(69,37,19,1200.00,3,0,'2026-09-12 04:39:09'),
(70,37,32,300.00,2,0,'2026-09-12 04:39:09'),
(71,37,35,300.00,3,0,'2026-09-12 04:39:09'),
(72,38,16,400.00,2,0,'2026-09-12 04:39:09'),
(73,39,19,1200.00,3,0,'2026-09-12 04:39:09'),
(74,39,21,950.00,2,0,'2026-09-12 04:39:09'),
(75,39,33,200.00,3,0,'2026-09-12 04:39:09'),
(76,40,16,400.00,3,0,'2026-09-12 04:39:10'),
(77,40,26,100.00,3,0,'2026-09-12 04:39:10'),
(78,40,34,150.00,3,0,'2026-09-12 04:39:10'),
(79,41,17,450.00,1,0,'2026-09-12 04:39:10'),
(80,41,23,450.00,1,0,'2026-09-12 04:39:10'),
(81,41,32,300.00,1,0,'2026-09-12 04:39:10'),
(82,42,21,950.00,1,0,'2026-09-12 04:39:10'),
(83,43,16,400.00,2,0,'2026-09-12 04:39:10'),
(84,44,14,550.00,2,0,'2026-09-12 04:39:10'),
(85,44,22,800.00,2,0,'2026-09-12 04:39:10'),
(86,44,29,250.00,3,0,'2026-09-12 04:39:10'),
(87,45,36,350.00,2,0,'2026-09-12 04:39:10'),
(88,45,33,200.00,2,0,'2026-09-12 04:39:10'),
(89,46,14,550.00,2,0,'2026-09-12 04:39:10'),
(90,46,18,650.00,2,0,'2026-09-12 04:39:10'),
(91,46,35,300.00,3,0,'2026-09-12 04:39:10'),
(92,47,19,1200.00,1,0,'2026-09-12 04:39:10'),
(93,47,33,200.00,2,0,'2026-09-12 04:39:10'),
(94,48,28,100.00,1,0,'2026-09-12 04:39:10'),
(95,49,13,350.00,1,0,'2026-09-12 04:39:10'),
(96,49,16,400.00,1,0,'2026-09-12 04:39:10'),
(97,50,21,950.00,1,0,'2026-09-12 04:39:10'),
(98,50,30,250.00,3,0,'2026-09-12 04:39:10'),
(99,51,13,350.00,2,0,'2026-09-12 04:39:10'),
(100,52,23,450.00,3,0,'2026-09-12 04:39:10'),
(101,52,30,250.00,2,0,'2026-09-12 04:39:10'),
(102,52,34,150.00,3,0,'2026-09-12 04:39:10'),
(103,53,22,800.00,2,0,'2026-09-12 04:39:10'),
(104,53,38,250.00,3,0,'2026-09-12 04:39:10'),
(105,53,31,300.00,2,0,'2026-09-12 04:39:10'),
(106,54,31,300.00,2,0,'2026-09-12 04:39:10'),
(107,54,33,200.00,1,0,'2026-09-12 04:39:10'),
(108,55,28,100.00,1,0,'2026-09-12 04:39:10'),
(109,55,31,300.00,3,0,'2026-09-12 04:39:10'),
(110,56,16,400.00,2,0,'2026-09-12 04:39:10'),
(111,56,17,450.00,2,0,'2026-09-12 04:39:10'),
(112,56,24,150.00,2,0,'2026-09-12 04:39:10'),
(113,57,37,250.00,1,0,'2026-09-12 04:39:10'),
(114,57,39,280.00,1,0,'2026-09-12 04:39:10'),
(115,58,20,500.00,1,0,'2026-09-12 04:39:10'),
(116,58,22,800.00,3,0,'2026-09-12 04:39:10'),
(117,59,18,650.00,2,0,'2026-09-12 04:39:10'),
(118,59,24,150.00,2,0,'2026-09-12 04:39:10'),
(119,59,31,300.00,3,0,'2026-09-12 04:39:10'),
(120,60,23,450.00,2,0,'2026-09-12 04:39:10'),
(121,61,33,200.00,1,0,'2026-09-12 04:39:10'),
(122,62,30,250.00,3,0,'2026-09-12 04:39:10'),
(123,63,18,650.00,2,0,'2026-09-12 04:39:10'),
(124,64,34,150.00,3,0,'2026-09-12 04:39:10'),
(125,65,30,250.00,3,0,'2026-09-12 04:39:10'),
(126,66,16,400.00,3,0,'2026-09-12 04:39:10'),
(127,66,27,100.00,2,0,'2026-09-12 04:39:10'),
(128,67,13,350.00,1,0,'2026-09-12 04:39:10'),
(129,67,36,350.00,3,0,'2026-09-12 04:39:10'),
(130,68,15,300.00,2,0,'2026-09-12 04:39:10'),
(131,68,39,280.00,3,0,'2026-09-12 04:39:10'),
(132,69,23,450.00,3,0,'2026-09-12 04:39:10'),
(133,69,31,300.00,3,0,'2026-09-12 04:39:10'),
(134,69,33,200.00,2,0,'2026-09-12 04:39:10'),
(135,70,28,100.00,2,0,'2026-09-12 04:39:10'),
(136,71,32,300.00,2,0,'2026-09-12 04:39:10'),
(137,72,14,550.00,3,0,'2026-09-12 04:39:10'),
(138,72,36,350.00,1,0,'2026-09-12 04:39:10'),
(139,72,27,100.00,1,0,'2026-09-12 04:39:10'),
(140,73,14,550.00,1,0,'2026-09-12 04:39:10'),
(141,73,23,450.00,2,0,'2026-09-12 04:39:10'),
(142,73,24,150.00,1,0,'2026-09-12 04:39:10'),
(143,74,14,550.00,2,0,'2026-09-12 04:39:10'),
(144,74,36,350.00,3,0,'2026-09-12 04:39:10'),
(145,74,32,300.00,1,0,'2026-09-12 04:39:10'),
(146,75,20,500.00,2,0,'2026-09-12 04:39:10'),
(147,75,22,800.00,1,0,'2026-09-12 04:39:10'),
(148,75,36,350.00,2,0,'2026-09-12 04:39:10'),
(149,76,18,650.00,1,0,'2026-09-12 04:39:10'),
(150,76,19,1200.00,3,0,'2026-09-12 04:39:10'),
(151,77,14,550.00,3,0,'2026-09-12 04:39:10'),
(152,77,15,300.00,1,0,'2026-09-12 04:39:10'),
(153,78,21,950.00,1,0,'2026-09-12 04:39:10'),
(154,78,26,100.00,1,0,'2026-09-12 04:39:10'),
(155,79,21,950.00,3,0,'2026-09-12 04:39:10'),
(156,80,25,250.00,2,0,'2026-09-12 04:39:10'),
(157,81,36,350.00,2,0,'2026-09-12 04:39:10'),
(158,81,35,300.00,2,0,'2026-09-12 04:39:10'),
(159,82,29,250.00,2,0,'2026-09-12 04:39:10'),
(160,82,30,250.00,3,0,'2026-09-12 04:39:10'),
(161,83,39,280.00,3,0,'2026-09-12 04:39:10'),
(162,83,32,300.00,2,0,'2026-09-12 04:39:10'),
(163,84,29,250.00,1,0,'2026-09-12 04:39:10'),
(164,85,21,950.00,3,0,'2026-09-12 04:39:10'),
(165,85,35,300.00,1,0,'2026-09-12 04:39:10'),
(166,86,37,250.00,2,0,'2026-09-12 04:39:10'),
(167,86,39,280.00,2,0,'2026-09-12 04:39:10'),
(168,86,31,300.00,3,0,'2026-09-12 04:39:10'),
(169,87,38,250.00,3,0,'2026-09-12 04:39:10'),
(170,88,27,100.00,1,0,'2026-09-12 04:39:10'),
(171,89,20,500.00,2,0,'2026-09-12 04:39:10'),
(172,89,34,150.00,3,0,'2026-09-12 04:39:10'),
(173,90,36,350.00,2,0,'2026-09-12 04:39:10'),
(174,90,37,250.00,3,0,'2026-09-12 04:39:10'),
(175,91,16,400.00,1,1,'2026-09-12 04:39:10'),
(176,92,14,550.00,1,1,'2026-09-12 04:39:10'),
(177,92,15,300.00,1,1,'2026-09-12 04:39:10'),
(178,93,24,150.00,3,0,'2026-09-12 04:39:10'),
(179,94,20,500.00,3,0,'2026-09-12 04:39:10'),
(180,94,25,250.00,2,0,'2026-09-12 04:39:10'),
(181,95,15,300.00,3,0,'2026-09-12 04:39:10'),
(182,96,34,150.00,3,0,'2026-09-12 04:39:10'),
(183,97,35,300.00,1,0,'2026-09-12 04:39:10'),
(184,98,20,500.00,3,0,'2026-09-12 04:39:10'),
(185,98,26,100.00,1,0,'2026-09-12 04:39:10'),
(186,99,19,1200.00,2,0,'2026-09-12 04:39:10'),
(187,99,27,100.00,1,0,'2026-09-12 04:39:10'),
(188,99,31,300.00,3,0,'2026-09-12 04:39:10'),
(189,100,17,450.00,2,0,'2026-09-12 04:39:10'),
(190,100,21,950.00,3,0,'2026-09-12 04:39:10'),
(191,100,36,350.00,3,0,'2026-09-12 04:39:10'),
(192,101,16,400.00,3,0,'2026-09-12 04:39:10'),
(193,101,22,800.00,1,0,'2026-09-12 04:39:10'),
(194,101,24,150.00,1,0,'2026-09-12 04:39:10'),
(195,102,18,650.00,1,0,'2026-09-12 04:39:10'),
(196,102,28,100.00,3,0,'2026-09-12 04:39:10'),
(197,102,30,250.00,1,0,'2026-09-12 04:39:10'),
(198,103,36,350.00,1,0,'2026-09-12 04:39:10'),
(199,103,34,150.00,1,0,'2026-09-12 04:39:10'),
(200,103,35,300.00,2,0,'2026-09-12 04:39:10'),
(201,104,16,400.00,2,0,'2026-09-12 04:39:10'),
(202,105,28,100.00,3,0,'2026-09-12 04:39:10'),
(203,105,34,150.00,1,0,'2026-09-12 04:39:10'),
(204,107,31,300.00,3,0,'2026-09-12 05:31:19'),
(205,107,36,350.00,3,0,'2026-09-12 05:31:19'),
(206,108,34,150.00,1,0,'2026-09-12 05:31:19'),
(207,108,20,500.00,2,0,'2026-09-12 05:31:19'),
(208,109,20,500.00,3,0,'2026-09-12 05:31:19'),
(209,109,36,350.00,3,0,'2026-09-12 05:31:19'),
(210,110,28,100.00,1,0,'2026-09-12 05:31:19'),
(211,111,13,350.00,3,0,'2026-09-12 05:31:19'),
(212,112,11,1100.00,3,1,'2026-09-14 17:01:14'),
(213,113,26,100.00,2,1,'2026-09-14 17:02:15'),
(214,114,21,950.00,2,1,'2026-09-14 19:21:11'),
(215,114,24,150.00,2,1,'2026-09-14 19:21:11');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `status` enum('PLACED','SERVED','PAYED','CANCELLED') NOT NULL,
  `type` enum('DINE_IN','TAKEAWAY','DELIVERY') NOT NULL,
  `user_id` int(11) NOT NULL,
  `table_id` int(11) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `table_id` (`table_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  KEY `user_id` (`user_id`,`created_at`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `staff` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
(6,'ORD-20260906-D2E2','PAYED','DINE_IN',263,1,1200.00,'2026-09-06 12:49:00','2026-09-06 12:49:00','2026-09-12 04:39:09'),
(7,'ORD-20260906-9CB9','PAYED','DINE_IN',263,1,3600.00,'2026-09-06 13:47:00','2026-09-06 13:47:00','2026-09-12 04:39:09'),
(8,'ORD-20260906-2B14','PAYED','DINE_IN',263,1,1740.00,'2026-09-06 12:09:00','2026-09-06 12:09:00','2026-09-12 04:39:09'),
(9,'ORD-20260906-DA04','PLACED','TAKEAWAY',263,NULL,1400.00,NULL,'2026-09-06 10:36:00','2026-09-12 04:39:09'),
(10,'ORD-20260906-2914','PAYED','TAKEAWAY',263,NULL,1550.00,'2026-09-06 08:43:00','2026-09-06 08:43:00','2026-09-12 04:39:09'),
(11,'ORD-20260906-98D9','PAYED','DINE_IN',263,1,1050.00,'2026-09-06 18:37:00','2026-09-06 18:37:00','2026-09-12 04:39:09'),
(12,'ORD-20260906-84DD','PAYED','DINE_IN',263,1,2550.00,'2026-09-06 10:06:00','2026-09-06 10:06:00','2026-09-12 04:39:09'),
(13,'ORD-20260906-E4A3','PAYED','DINE_IN',263,1,2350.00,'2026-09-06 11:34:00','2026-09-06 11:34:00','2026-09-12 04:39:09'),
(14,'ORD-20260906-871C','PLACED','DINE_IN',263,1,700.00,NULL,'2026-09-06 16:17:00','2026-09-12 04:39:09'),
(15,'ORD-20260906-8070','PAYED','DINE_IN',263,1,2250.00,'2026-09-06 16:39:00','2026-09-06 16:39:00','2026-09-12 04:39:09'),
(16,'ORD-20260906-7B2F','PAYED','DINE_IN',263,1,2400.00,'2026-09-06 16:10:00','2026-09-06 16:10:00','2026-09-12 04:39:09'),
(17,'ORD-20260906-5A54','PAYED','DINE_IN',263,1,1900.00,'2026-09-06 09:22:00','2026-09-06 09:22:00','2026-09-12 04:39:09'),
(18,'ORD-20260907-56DD','PLACED','TAKEAWAY',263,NULL,1200.00,NULL,'2026-09-07 10:38:00','2026-09-12 04:39:09'),
(19,'ORD-20260907-6DC7','PLACED','DINE_IN',263,1,1750.00,NULL,'2026-09-07 09:55:00','2026-09-12 04:39:09'),
(20,'ORD-20260907-15AE','PAYED','DINE_IN',263,1,1950.00,'2026-09-07 12:58:00','2026-09-07 12:58:00','2026-09-12 04:39:09'),
(21,'ORD-20260907-05BB','CANCELLED','DINE_IN',263,1,2150.00,NULL,'2026-09-07 12:02:00','2026-09-12 04:39:09'),
(22,'ORD-20260907-195A','PAYED','DINE_IN',263,1,4400.00,'2026-09-07 15:16:00','2026-09-07 15:16:00','2026-09-12 04:39:09'),
(23,'ORD-20260907-4CA0','PLACED','DINE_IN',263,1,550.00,NULL,'2026-09-07 18:26:00','2026-09-12 04:39:09'),
(24,'ORD-20260907-587C','PLACED','DINE_IN',263,1,950.00,NULL,'2026-09-07 17:38:00','2026-09-12 04:39:09'),
(25,'ORD-20260907-A14C','PAYED','DINE_IN',263,1,2850.00,'2026-09-07 17:10:00','2026-09-07 17:10:00','2026-09-12 04:39:09'),
(26,'ORD-20260907-19FD','PAYED','DINE_IN',263,1,1100.00,'2026-09-07 14:17:00','2026-09-07 14:17:00','2026-09-12 04:39:09'),
(27,'ORD-20260907-7D0D','PAYED','DINE_IN',263,1,100.00,'2026-09-07 13:26:00','2026-09-07 13:26:00','2026-09-12 04:39:09'),
(28,'ORD-20260908-BEAF','PAYED','DINE_IN',263,1,950.00,'2026-09-08 12:47:00','2026-09-08 12:47:00','2026-09-12 04:39:09'),
(29,'ORD-20260908-D768','PAYED','DINE_IN',263,1,1300.00,'2026-09-08 09:57:00','2026-09-08 09:57:00','2026-09-12 04:39:09'),
(30,'ORD-20260908-24FD','PAYED','DINE_IN',263,1,2200.00,'2026-09-08 15:42:00','2026-09-08 15:42:00','2026-09-12 04:39:09'),
(31,'ORD-20260908-1F21','PAYED','DINE_IN',263,1,2440.00,'2026-09-08 09:14:00','2026-09-08 09:14:00','2026-09-12 04:39:09'),
(32,'ORD-20260908-6EF7','CANCELLED','DINE_IN',263,1,300.00,NULL,'2026-09-08 09:24:00','2026-09-12 04:39:09'),
(33,'ORD-20260908-F6FC','PLACED','DINE_IN',263,1,200.00,NULL,'2026-09-08 12:14:00','2026-09-12 04:39:09'),
(34,'ORD-20260908-074E','PLACED','DINE_IN',263,1,3750.00,NULL,'2026-09-08 10:13:00','2026-09-12 04:39:09'),
(35,'ORD-20260908-28A1','PAYED','TAKEAWAY',263,NULL,1150.00,'2026-09-08 18:21:00','2026-09-08 18:21:00','2026-09-12 04:39:09'),
(36,'ORD-20260908-872D','PAYED','DINE_IN',263,1,1950.00,'2026-09-08 16:46:00','2026-09-08 16:46:00','2026-09-12 04:39:09'),
(37,'ORD-20260908-812F','PLACED','DINE_IN',263,1,5100.00,NULL,'2026-09-08 08:46:00','2026-09-12 04:39:09'),
(38,'ORD-20260908-6D33','PAYED','DINE_IN',263,1,800.00,'2026-09-08 17:35:00','2026-09-08 17:35:00','2026-09-12 04:39:09'),
(39,'ORD-20260908-51FF','PLACED','DINE_IN',263,1,6100.00,NULL,'2026-09-08 18:58:00','2026-09-12 04:39:09'),
(40,'ORD-20260908-D4CB','PAYED','DINE_IN',263,1,1950.00,'2026-09-08 11:14:00','2026-09-08 11:14:00','2026-09-12 04:39:10'),
(41,'ORD-20260909-12F5','PAYED','DINE_IN',263,1,1200.00,'2026-09-09 15:45:00','2026-09-09 15:45:00','2026-09-12 04:39:10'),
(42,'ORD-20260909-D0DF','PAYED','DINE_IN',263,1,950.00,'2026-09-09 16:49:00','2026-09-09 16:49:00','2026-09-12 04:39:10'),
(43,'ORD-20260909-DA3F','PLACED','DINE_IN',263,1,800.00,NULL,'2026-09-09 16:09:00','2026-09-12 04:39:10'),
(44,'ORD-20260909-ECEF','PAYED','DINE_IN',263,1,3450.00,'2026-09-09 16:48:00','2026-09-09 16:48:00','2026-09-12 04:39:10'),
(45,'ORD-20260909-99F2','PAYED','DINE_IN',263,1,1100.00,'2026-09-09 10:45:00','2026-09-09 10:45:00','2026-09-12 04:39:10'),
(46,'ORD-20260909-8F3F','PLACED','DINE_IN',263,1,3300.00,NULL,'2026-09-09 12:47:00','2026-09-12 04:39:10'),
(47,'ORD-20260909-2E46','PAYED','DINE_IN',263,1,1600.00,'2026-09-09 12:10:00','2026-09-09 12:10:00','2026-09-12 04:39:10'),
(48,'ORD-20260909-7B2D','PAYED','DINE_IN',263,1,100.00,'2026-09-09 15:32:00','2026-09-09 15:32:00','2026-09-12 04:39:10'),
(49,'ORD-20260909-C024','PLACED','DINE_IN',263,1,750.00,NULL,'2026-09-09 18:22:00','2026-09-12 04:39:10'),
(50,'ORD-20260910-6F2D','CANCELLED','DINE_IN',263,1,1700.00,NULL,'2026-09-10 08:36:00','2026-09-12 04:39:10'),
(51,'ORD-20260910-7299','PAYED','TAKEAWAY',263,NULL,700.00,'2026-09-10 16:18:00','2026-09-10 16:18:00','2026-09-12 04:39:10'),
(52,'ORD-20260910-D2D6','PAYED','DINE_IN',263,1,2300.00,'2026-09-10 10:10:00','2026-09-10 10:10:00','2026-09-12 04:39:10'),
(53,'ORD-20260910-CBB1','PLACED','DINE_IN',263,1,2950.00,NULL,'2026-09-10 11:22:00','2026-09-12 04:39:10'),
(54,'ORD-20260910-0E43','PAYED','DINE_IN',263,1,800.00,'2026-09-10 11:43:00','2026-09-10 11:43:00','2026-09-12 04:39:10'),
(55,'ORD-20260910-216A','PAYED','TAKEAWAY',263,NULL,1000.00,'2026-09-10 09:55:00','2026-09-10 09:55:00','2026-09-12 04:39:10'),
(56,'ORD-20260910-4058','PAYED','TAKEAWAY',263,NULL,2000.00,'2026-09-10 14:42:00','2026-09-10 14:42:00','2026-09-12 04:39:10'),
(57,'ORD-20260910-755B','PLACED','DINE_IN',263,1,530.00,NULL,'2026-09-10 14:23:00','2026-09-12 04:39:10'),
(58,'ORD-20260910-F62F','CANCELLED','DINE_IN',263,1,2900.00,NULL,'2026-09-10 17:49:00','2026-09-12 04:39:10'),
(59,'ORD-20260910-FB16','PAYED','DINE_IN',263,1,2500.00,'2026-09-10 16:53:00','2026-09-10 16:53:00','2026-09-12 04:39:10'),
(60,'ORD-20260910-D384','PAYED','DINE_IN',263,1,900.00,'2026-09-10 17:38:00','2026-09-10 17:38:00','2026-09-12 04:39:10'),
(61,'ORD-20260910-954B','PLACED','TAKEAWAY',263,NULL,200.00,NULL,'2026-09-10 13:59:00','2026-09-12 04:39:10'),
(62,'ORD-20260910-6362','PAYED','TAKEAWAY',263,NULL,750.00,'2026-09-10 11:07:00','2026-09-10 11:07:00','2026-09-12 04:39:10'),
(63,'ORD-20260910-10B1','PAYED','DINE_IN',263,1,1300.00,'2026-09-10 16:24:00','2026-09-10 16:24:00','2026-09-12 04:39:10'),
(64,'ORD-20260911-974B','PAYED','DINE_IN',263,1,450.00,'2026-09-11 11:44:00','2026-09-11 11:44:00','2026-09-12 04:39:10'),
(65,'ORD-20260911-DFA2','PAYED','TAKEAWAY',263,NULL,750.00,'2026-09-11 14:36:00','2026-09-11 14:36:00','2026-09-12 04:39:10'),
(66,'ORD-20260911-A9AC','PAYED','DINE_IN',263,1,1400.00,'2026-09-11 18:04:00','2026-09-11 18:04:00','2026-09-12 04:39:10'),
(67,'ORD-20260911-BFB6','PAYED','DINE_IN',263,1,1400.00,'2026-09-11 15:53:00','2026-09-11 15:53:00','2026-09-12 04:39:10'),
(68,'ORD-20260911-291C','PAYED','DINE_IN',263,1,1440.00,'2026-09-11 11:28:00','2026-09-11 11:28:00','2026-09-12 04:39:10'),
(69,'ORD-20260911-468B','PAYED','DINE_IN',263,1,2650.00,'2026-09-11 11:07:00','2026-09-11 11:07:00','2026-09-12 04:39:10'),
(70,'ORD-20260911-64EF','PAYED','TAKEAWAY',263,NULL,200.00,'2026-09-11 14:10:00','2026-09-11 14:10:00','2026-09-12 04:39:10'),
(71,'ORD-20260911-874A','PAYED','DINE_IN',263,1,600.00,'2026-09-11 08:11:00','2026-09-11 08:11:00','2026-09-12 04:39:10'),
(72,'ORD-20260911-73C6','PAYED','DINE_IN',263,1,2100.00,'2026-09-11 12:47:00','2026-09-11 12:47:00','2026-09-12 04:39:10'),
(73,'ORD-20260911-05B1','PAYED','DINE_IN',263,1,1600.00,'2026-09-11 08:24:00','2026-09-11 08:24:00','2026-09-12 04:39:10'),
(74,'ORD-20260911-1678','PLACED','DINE_IN',263,1,2450.00,NULL,'2026-09-11 16:05:00','2026-09-12 04:39:10'),
(75,'ORD-20260911-6870','PLACED','DINE_IN',263,1,2500.00,NULL,'2026-09-11 13:00:00','2026-09-12 04:39:10'),
(76,'ORD-20260911-9FF1','PAYED','DINE_IN',263,1,4250.00,'2026-09-11 12:45:00','2026-09-11 12:45:00','2026-09-12 04:39:10'),
(77,'ORD-20260911-D1D6','PAYED','DINE_IN',263,1,1950.00,'2026-09-11 12:42:00','2026-09-11 12:42:00','2026-09-12 04:39:10'),
(78,'ORD-20260911-B3B4','PLACED','DINE_IN',263,1,1050.00,NULL,'2026-09-11 16:26:00','2026-09-12 04:39:10'),
(79,'ORD-20260911-2142','PAYED','DINE_IN',263,1,2850.00,'2026-09-11 09:54:00','2026-09-11 09:54:00','2026-09-12 04:39:10'),
(80,'ORD-20260911-E33E','CANCELLED','DINE_IN',263,1,500.00,NULL,'2026-09-11 17:24:00','2026-09-12 04:39:10'),
(81,'ORD-20260911-B355','PAYED','DINE_IN',263,1,1300.00,'2026-09-11 09:52:00','2026-09-11 09:52:00','2026-09-12 04:39:10'),
(82,'ORD-20260911-5E3A','PAYED','DINE_IN',263,1,1250.00,'2026-09-11 16:19:00','2026-09-11 16:19:00','2026-09-12 04:39:10'),
(83,'ORD-20260912-0F95','PAYED','DINE_IN',263,1,1440.00,'2026-09-12 14:41:00','2026-09-12 14:41:00','2026-09-12 04:39:10'),
(84,'ORD-20260912-6578','PAYED','DINE_IN',263,1,250.00,'2026-09-12 16:58:00','2026-09-12 16:58:00','2026-09-12 04:39:10'),
(85,'ORD-20260912-4EA8','PAYED','DINE_IN',263,1,3150.00,'2026-09-12 18:24:00','2026-09-12 18:24:00','2026-09-12 04:39:10'),
(86,'ORD-20260912-F9D7','PAYED','DINE_IN',263,1,1960.00,'2026-09-12 14:49:00','2026-09-12 14:49:00','2026-09-12 04:39:10'),
(87,'ORD-20260912-60FD','PAYED','DINE_IN',263,1,750.00,'2026-09-12 08:00:00','2026-09-12 08:00:00','2026-09-12 04:39:10'),
(88,'ORD-20260912-896E','PAYED','TAKEAWAY',263,NULL,100.00,'2026-09-12 11:16:00','2026-09-12 11:16:00','2026-09-12 04:39:10'),
(89,'ORD-20260912-CE14','PAYED','TAKEAWAY',263,NULL,1450.00,'2026-09-12 10:21:00','2026-09-12 10:21:00','2026-09-12 04:39:10'),
(90,'ORD-20260912-939E','PAYED','TAKEAWAY',263,NULL,1450.00,'2026-09-12 18:50:00','2026-09-12 18:50:00','2026-09-12 04:39:10'),
(91,'ORD-20260912-4154','SERVED','DINE_IN',263,1,400.00,NULL,'2026-09-12 18:31:00','2026-09-12 04:39:10'),
(92,'ORD-20260912-95B5','SERVED','DINE_IN',263,1,850.00,NULL,'2026-09-12 10:28:00','2026-09-12 04:39:10'),
(93,'ORD-20260912-097F','PLACED','TAKEAWAY',263,NULL,450.00,NULL,'2026-09-12 11:50:00','2026-09-12 04:39:10'),
(94,'ORD-20260912-F825','PLACED','DINE_IN',263,1,2000.00,NULL,'2026-09-12 14:10:00','2026-09-12 04:39:10'),
(95,'ORD-20260912-303D','PLACED','DINE_IN',263,1,900.00,NULL,'2026-09-12 12:19:00','2026-09-12 04:39:10'),
(96,'ORD-20260912-C03D','PLACED','DINE_IN',263,1,450.00,NULL,'2026-09-12 18:10:00','2026-09-12 04:39:10'),
(97,'ORD-20260912-28DD','PLACED','DINE_IN',263,1,300.00,NULL,'2026-09-12 18:06:00','2026-09-12 04:39:10'),
(98,'ORD-20260912-B1CB','PLACED','TAKEAWAY',263,NULL,1600.00,NULL,'2026-09-12 09:29:00','2026-09-12 04:39:10'),
(99,'ORD-20260912-DEDA','PLACED','TAKEAWAY',263,NULL,3400.00,NULL,'2026-09-12 18:26:00','2026-09-12 04:39:10'),
(100,'ORD-20260913-9144','PAYED','DINE_IN',263,1,4800.00,'2026-09-14 17:20:39','2026-09-13 16:38:00','2026-09-14 17:20:39'),
(101,'ORD-20260913-B274','PAYED','DINE_IN',263,1,2150.00,'2026-09-14 17:20:28','2026-09-13 16:27:00','2026-09-14 17:20:28'),
(102,'ORD-20260913-6DE5','PAYED','DINE_IN',263,1,1200.00,'2026-09-14 17:20:20','2026-09-13 09:27:00','2026-09-14 17:20:20'),
(103,'ORD-20260913-95E9','PAYED','TAKEAWAY',263,NULL,1100.00,'2026-09-14 17:20:22','2026-09-13 09:45:00','2026-09-14 17:20:22'),
(104,'ORD-20260913-4AD4','PAYED','DINE_IN',263,1,800.00,'2026-09-14 17:20:26','2026-09-13 12:45:00','2026-09-14 17:20:26'),
(105,'ORD-20260913-B201','PAYED','DINE_IN',263,1,450.00,'2026-09-14 17:20:35','2026-09-13 16:29:00','2026-09-14 17:20:35'),
(107,'ORD-20260913-4D38','PAYED','DINE_IN',263,3,1950.00,'2026-09-13 06:30:00','2026-09-13 06:30:00','2026-09-12 05:31:19'),
(108,'ORD-20260913-EEFB','PAYED','TAKEAWAY',263,NULL,1150.00,'2026-09-13 06:31:00','2026-09-13 06:31:00','2026-09-12 05:31:19'),
(109,'ORD-20260913-FCC7','PAYED','DINE_IN',263,4,2550.00,'2026-09-13 07:32:00','2026-09-13 07:32:00','2026-09-12 05:31:19'),
(110,'ORD-20260913-3AAF','PAYED','DINE_IN',263,4,100.00,'2026-09-13 07:33:00','2026-09-13 07:33:00','2026-09-12 05:31:19'),
(111,'ORD-20260913-AF66','PAYED','TAKEAWAY',263,NULL,1050.00,'2026-09-13 08:34:00','2026-09-13 08:34:00','2026-09-12 05:31:19'),
(112,'ORD-20260914-091E','SERVED','DINE_IN',263,1,3300.00,NULL,'2026-09-14 17:01:14','2026-09-14 17:01:48'),
(113,'ORD-20260914-2DBC','SERVED','DINE_IN',264,1,200.00,NULL,'2026-09-14 17:02:15','2026-09-14 17:02:19'),
(114,'ORD-20260914-0969','SERVED','DINE_IN',263,1,2200.00,NULL,'2026-09-14 19:21:11','2026-09-14 19:21:52');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_code` varchar(100) DEFAULT NULL,
  `method` enum('CASH','CARD','MOBILE','REFUND') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `order_id` int(11) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `cashier_id` (`cashier_id`),
  KEY `created_at` (`created_at`),
  KEY `method` (`method`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`cashier_id`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(1,NULL,'CARD',1200.00,6,1,'2026-09-06 12:49:00','2026-09-12 04:39:09'),
(2,NULL,'CASH',3600.00,7,1,'2026-09-06 13:47:00','2026-09-12 04:39:09'),
(3,NULL,'CASH',1740.00,8,1,'2026-09-06 12:09:00','2026-09-12 04:39:09'),
(4,NULL,'CASH',1550.00,10,1,'2026-09-06 08:43:00','2026-09-12 04:39:09'),
(5,NULL,'CARD',1050.00,11,1,'2026-09-06 18:37:00','2026-09-12 04:39:09'),
(6,NULL,'MOBILE',2550.00,12,1,'2026-09-06 10:06:00','2026-09-12 04:39:09'),
(7,NULL,'CARD',2350.00,13,1,'2026-09-06 11:34:00','2026-09-12 04:39:09'),
(8,NULL,'MOBILE',2250.00,15,1,'2026-09-06 16:39:00','2026-09-12 04:39:09'),
(9,NULL,'CARD',2400.00,16,1,'2026-09-06 16:10:00','2026-09-12 04:39:09'),
(10,NULL,'CASH',1900.00,17,1,'2026-09-06 09:22:00','2026-09-12 04:39:09'),
(11,NULL,'CASH',1950.00,20,1,'2026-09-07 12:58:00','2026-09-12 04:39:09'),
(12,NULL,'CASH',4400.00,22,1,'2026-09-07 15:16:00','2026-09-12 04:39:09'),
(13,NULL,'CARD',2850.00,25,1,'2026-09-07 17:10:00','2026-09-12 04:39:09'),
(14,NULL,'CASH',1100.00,26,1,'2026-09-07 14:17:00','2026-09-12 04:39:09'),
(15,NULL,'MOBILE',100.00,27,1,'2026-09-07 13:26:00','2026-09-12 04:39:09'),
(16,NULL,'CASH',950.00,28,1,'2026-09-08 12:47:00','2026-09-12 04:39:09'),
(17,NULL,'CASH',1300.00,29,1,'2026-09-08 09:57:00','2026-09-12 04:39:09'),
(18,NULL,'CASH',2200.00,30,1,'2026-09-08 15:42:00','2026-09-12 04:39:09'),
(19,NULL,'MOBILE',2440.00,31,1,'2026-09-08 09:14:00','2026-09-12 04:39:09'),
(20,NULL,'CARD',1150.00,35,1,'2026-09-08 18:21:00','2026-09-12 04:39:09'),
(21,NULL,'MOBILE',1950.00,36,1,'2026-09-08 16:46:00','2026-09-12 04:39:09'),
(22,NULL,'CASH',800.00,38,1,'2026-09-08 17:35:00','2026-09-12 04:39:09'),
(23,NULL,'CARD',1950.00,40,1,'2026-09-08 11:14:00','2026-09-12 04:39:10'),
(24,NULL,'CARD',1200.00,41,1,'2026-09-09 15:45:00','2026-09-12 04:39:10'),
(25,NULL,'CARD',950.00,42,1,'2026-09-09 16:49:00','2026-09-12 04:39:10'),
(26,NULL,'CASH',3450.00,44,1,'2026-09-09 16:48:00','2026-09-12 04:39:10'),
(27,NULL,'CASH',1100.00,45,1,'2026-09-09 10:45:00','2026-09-12 04:39:10'),
(28,NULL,'MOBILE',1600.00,47,1,'2026-09-09 12:10:00','2026-09-12 04:39:10'),
(29,NULL,'CARD',100.00,48,1,'2026-09-09 15:32:00','2026-09-12 04:39:10'),
(30,NULL,'CASH',700.00,51,1,'2026-09-10 16:18:00','2026-09-12 04:39:10'),
(31,NULL,'CASH',2300.00,52,1,'2026-09-10 10:10:00','2026-09-12 04:39:10'),
(32,NULL,'CARD',800.00,54,1,'2026-09-10 11:43:00','2026-09-12 04:39:10'),
(33,NULL,'CASH',1000.00,55,1,'2026-09-10 09:55:00','2026-09-12 04:39:10'),
(34,NULL,'CARD',2000.00,56,1,'2026-09-10 14:42:00','2026-09-12 04:39:10'),
(35,NULL,'CASH',2500.00,59,1,'2026-09-10 16:53:00','2026-09-12 04:39:10'),
(36,NULL,'MOBILE',900.00,60,1,'2026-09-10 17:38:00','2026-09-12 04:39:10'),
(37,NULL,'CASH',750.00,62,1,'2026-09-10 11:07:00','2026-09-12 04:39:10'),
(38,NULL,'CARD',1300.00,63,1,'2026-09-10 16:24:00','2026-09-12 04:39:10'),
(39,NULL,'CASH',450.00,64,1,'2026-09-11 11:44:00','2026-09-12 04:39:10'),
(40,NULL,'CASH',750.00,65,1,'2026-09-11 14:36:00','2026-09-12 04:39:10'),
(41,NULL,'MOBILE',1400.00,66,1,'2026-09-11 18:04:00','2026-09-12 04:39:10'),
(42,NULL,'MOBILE',1400.00,67,1,'2026-09-11 15:53:00','2026-09-12 04:39:10'),
(43,NULL,'MOBILE',1440.00,68,1,'2026-09-11 11:28:00','2026-09-12 04:39:10'),
(44,NULL,'CARD',2650.00,69,1,'2026-09-11 11:07:00','2026-09-12 04:39:10'),
(45,NULL,'CARD',200.00,70,1,'2026-09-11 14:10:00','2026-09-12 04:39:10'),
(46,NULL,'CASH',600.00,71,1,'2026-09-11 08:11:00','2026-09-12 04:39:10'),
(47,NULL,'CASH',2100.00,72,1,'2026-09-11 12:47:00','2026-09-12 04:39:10'),
(48,NULL,'CARD',1600.00,73,1,'2026-09-11 08:24:00','2026-09-12 04:39:10'),
(49,NULL,'CASH',4250.00,76,1,'2026-09-11 12:45:00','2026-09-12 04:39:10'),
(50,NULL,'CASH',1950.00,77,1,'2026-09-11 12:42:00','2026-09-12 04:39:10'),
(51,NULL,'CARD',2850.00,79,1,'2026-09-11 09:54:00','2026-09-12 04:39:10'),
(52,NULL,'CARD',1300.00,81,1,'2026-09-11 09:52:00','2026-09-12 04:39:10'),
(53,NULL,'CARD',1250.00,82,1,'2026-09-11 16:19:00','2026-09-12 04:39:10'),
(54,NULL,'CASH',1440.00,83,1,'2026-09-12 14:41:00','2026-09-12 04:39:10'),
(55,NULL,'CASH',250.00,84,1,'2026-09-12 16:58:00','2026-09-12 04:39:10'),
(56,NULL,'CASH',3150.00,85,1,'2026-09-12 18:24:00','2026-09-12 04:39:10'),
(57,NULL,'MOBILE',1960.00,86,1,'2026-09-12 14:49:00','2026-09-12 04:39:10'),
(58,NULL,'CASH',750.00,87,1,'2026-09-12 08:00:00','2026-09-12 04:39:10'),
(59,NULL,'CASH',100.00,88,1,'2026-09-12 11:16:00','2026-09-12 04:39:10'),
(60,NULL,'CASH',1450.00,89,1,'2026-09-12 10:21:00','2026-09-12 04:39:10'),
(61,NULL,'CARD',1450.00,90,1,'2026-09-12 18:50:00','2026-09-12 04:39:10'),
(62,NULL,'CARD',1950.00,107,269,'2026-09-13 06:30:00','2026-09-12 05:31:19'),
(63,NULL,'CARD',1150.00,108,269,'2026-09-13 06:31:00','2026-09-12 05:31:19'),
(64,NULL,'MOBILE',2550.00,109,269,'2026-09-13 07:32:00','2026-09-12 05:31:19'),
(65,NULL,'CASH',100.00,110,269,'2026-09-13 07:33:00','2026-09-12 05:31:19'),
(66,NULL,'CASH',1050.00,111,269,'2026-09-13 08:34:00','2026-09-12 05:31:19'),
(67,NULL,'CASH',1200.00,102,269,'2026-09-14 17:20:20','2026-09-14 17:20:20'),
(68,NULL,'CASH',1100.00,103,269,'2026-09-14 17:20:22','2026-09-14 17:20:22'),
(69,NULL,'CASH',800.00,104,269,'2026-09-14 17:20:26','2026-09-14 17:20:26'),
(70,NULL,'CASH',2150.00,101,269,'2026-09-14 17:20:28','2026-09-14 17:20:28'),
(71,NULL,'CASH',450.00,105,269,'2026-09-14 17:20:35','2026-09-14 17:20:35'),
(72,NULL,'CASH',4800.00,100,269,'2026-09-14 17:20:39','2026-09-14 17:20:39');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permissions_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES
(1,'dashboard.view','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(2,'kitchen.view','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(3,'bar.view','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(4,'users.view','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(5,'users.create','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(6,'users.update','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(7,'users.deactivate','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(8,'roles.view','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(9,'roles.create','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(10,'roles.update','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(11,'roles.deactivate','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(12,'menu.view','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(13,'menu.create','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(14,'menu.update','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(15,'menu.deactivate','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(16,'categories.view','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(17,'categories.create','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(18,'categories.update','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(19,'categories.deactivate','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(20,'inventory.view','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(21,'inventory.create','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(22,'inventory.update','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(23,'inventory.deactivate','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(24,'inventory.stocktake','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(25,'inventory.variance','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(26,'store.view','2026-08-18 15:00:43','2026-08-18 15:00:43'),
(27,'log.view','2026-08-19 02:57:12','2026-08-19 02:57:12'),
(28,'reports.view','2026-08-19 06:45:18','2026-08-19 06:45:18'),
(57,'cashier.view','2026-09-12 00:54:05','2026-09-12 00:54:05'),
(58,'cashier.settle','2026-09-12 00:54:05','2026-09-12 00:54:05'),
(59,'day.close','2026-09-12 00:54:05','2026-09-12 00:54:05');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurant_details`
--

DROP TABLE IF EXISTS `restaurant_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `restaurant_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL DEFAULT 'RMS Restaurant',
  `address` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(30) NOT NULL DEFAULT '',
  `currency` varchar(10) NOT NULL DEFAULT 'KES',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurant_details`
--

LOCK TABLES `restaurant_details` WRITE;
/*!40000 ALTER TABLE `restaurant_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `restaurant_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES
(1,1),
(1,2),
(3,2),
(1,3),
(4,3),
(1,4),
(1,5),
(1,6),
(1,7),
(1,8),
(1,9),
(1,10),
(1,11),
(1,12),
(1,13),
(1,14),
(1,15),
(1,16),
(1,17),
(1,18),
(1,19),
(1,20),
(12,20),
(1,21),
(12,21),
(1,22),
(12,22),
(1,23),
(12,23),
(1,24),
(12,24),
(1,25),
(12,25),
(1,26),
(12,26),
(1,27),
(1,28),
(1,57),
(11,57),
(1,58),
(11,58),
(1,59);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'MANAGER',1,'2026-08-18 15:00:43','2026-08-18 15:00:43'),
(2,'WAITER',1,'2026-08-18 15:00:43','2026-08-18 15:00:43'),
(3,'HEAD CHEF',1,'2026-08-18 15:00:43','2026-08-18 15:00:43'),
(4,'BARTENDER',1,'2026-08-18 15:00:43','2026-08-18 15:00:43'),
(11,'CASHIER',1,'2026-09-12 00:54:05','2026-09-12 00:54:05'),
(12,'STORE MANAGER',1,'2026-09-12 05:31:06','2026-09-12 05:31:06');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_num` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `national_id` varchar(50) NOT NULL,
  `pin` varchar(4) NOT NULL,
  `pin_hash` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_num` (`employee_num`),
  UNIQUE KEY `national_id` (`national_id`),
  UNIQUE KEY `pin` (`pin`),
  KEY `role_id` (`role_id`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=272 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES
(1,'MR001','Manager',NULL,'Main','30000001','1111',NULL,'$2y$12$XRsfGGLuXJDWOgz9XoV7vOdCLsBxRqk14F7Bh7fd/IXCWu.jHwcOS',NULL,1,1,'2026-08-17 20:05:14','2026-09-12 02:19:27'),
(263,'WT001','Kyle','M','Mutua','40450412','1234',NULL,'$2y$12$acn06/VXRoe43Ndk3H4CKeB23pmrwWBojINWe7udoA/rrM7f7vxs6','0712345678',2,1,'2026-08-19 04:31:58','2026-09-12 02:19:27'),
(264,'BR001','Stephen','','Mirage','12345678','0987',NULL,'$2y$12$PO3pTzZWXTTicWs7hRik0e18ePzAI8RUucNObpQ/tahemar5KMbjK','0712345678',4,1,'2026-09-07 16:10:59','2026-09-12 02:19:27'),
(265,'HC001','Head','','Chef','12345677','2345',NULL,'$2y$12$n2G28GLG1w7ZSNxEjdTOgergkcTwqjOWZbQOzsP6yBSkJm60vI3Yu','0712345678',3,1,'2026-09-07 17:48:29','2026-09-12 02:19:27'),
(266,'WT002','Brian',NULL,'Kiprop','70990001','4444',NULL,'$2y$12$zlVPmnjd6x8vV7niROR/b.qC216YR2vR8vYHXCKrUnpLOZAeUagQ6',NULL,2,1,'2026-09-12 05:31:07','2026-09-12 05:31:07'),
(267,'WT003','Grace',NULL,'Wairimu','70990002','5555',NULL,'$2y$12$v6vUVx0SEFi4ok/VAjsIoOtoF0dUWYiqHx.ZUNu5Y4LDFPnf5ukZe',NULL,2,1,'2026-09-12 05:31:08','2026-09-12 05:31:08'),
(268,'WT004','James',NULL,'Mwangi','70990003','6666',NULL,'$2y$12$Ew5uTzAtkbUYlMUGR3yatudwUqNoNlg30CdWtEfpU8xw4LqJPsaFu',NULL,2,1,'2026-09-12 05:31:08','2026-09-12 05:31:08'),
(269,'CS001','Diana',NULL,'Achieng','70990004','3456',NULL,NULL,NULL,11,1,'2026-09-12 05:31:08','2026-09-12 05:31:08'),
(270,'ST001','Peter',NULL,'Kamau','70990005','7777',NULL,'$2y$12$fTkV6f9b8hQ6E0jYxowiaOAOTTWm43/35mTplu6UvbdTvhrdPTah.',NULL,12,1,'2026-09-12 05:31:09','2026-09-12 05:31:09'),
(271,'WT005','Paul','','Leon','67564322','9090',NULL,'$2y$12$QHvTRKUZD2oIxoCfyb48buVTGlEYh4jHf8jtmM12A/oBArhf6jBFy','0987654321',2,1,'2026-09-14 19:07:12','2026-09-14 19:07:12');
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_take_items`
--

DROP TABLE IF EXISTS `stock_take_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_take_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_take_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `system_qty` decimal(12,3) NOT NULL,
  `counted_qty` decimal(12,3) NOT NULL,
  `variance_qty` decimal(12,3) NOT NULL,
  `unit_cost` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `variance_value` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_take_id` (`stock_take_id`,`inventory_id`),
  KEY `inventory_id` (`inventory_id`),
  CONSTRAINT `stock_take_items_ibfk_1` FOREIGN KEY (`stock_take_id`) REFERENCES `stock_takes` (`id`),
  CONSTRAINT `stock_take_items_ibfk_2` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_take_items`
--

LOCK TABLES `stock_take_items` WRITE;
/*!40000 ALTER TABLE `stock_take_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_take_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_takes`
--

DROP TABLE IF EXISTS `stock_takes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_takes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scope` enum('ALL','BAR') NOT NULL DEFAULT 'ALL',
  `take_date` date NOT NULL,
  `performed_by` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `take_date` (`take_date`),
  KEY `scope` (`scope`),
  KEY `stock_takes_performed_by_fk` (`performed_by`),
  CONSTRAINT `stock_takes_performed_by_fk` FOREIGN KEY (`performed_by`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_takes`
--

LOCK TABLES `stock_takes` WRITE;
/*!40000 ALTER TABLE `stock_takes` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_takes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tables`
--

DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) DEFAULT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('AVAILABLE','OCCUPIED','RESERVED','OUT_OF_SERVICE') DEFAULT 'AVAILABLE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tables`
--

LOCK TABLES `tables` WRITE;
/*!40000 ALTER TABLE `tables` DISABLE KEYS */;
INSERT INTO `tables` VALUES
(1,1,4,'AVAILABLE','2026-08-19 04:56:12','2026-08-19 04:56:12'),
(2,2,4,'AVAILABLE','2026-09-12 05:31:06','2026-09-12 05:31:06'),
(3,3,2,'AVAILABLE','2026-09-12 05:31:06','2026-09-12 05:31:06'),
(4,4,2,'AVAILABLE','2026-09-12 05:31:06','2026-09-12 05:31:06'),
(5,5,6,'AVAILABLE','2026-09-12 05:31:06','2026-09-12 05:31:06'),
(6,6,6,'AVAILABLE','2026-09-12 05:31:06','2026-09-12 05:31:06'),
(7,7,8,'AVAILABLE','2026-09-12 05:31:06','2026-09-12 05:31:06'),
(8,8,8,'AVAILABLE','2026-09-12 05:31:06','2026-09-12 05:31:06'),
(9,9,4,'RESERVED','2026-09-12 05:31:06','2026-09-12 05:31:06'),
(10,10,6,'OCCUPIED','2026-09-12 05:31:06','2026-09-12 05:31:06'),
(11,11,2,'OUT_OF_SERVICE','2026-09-12 05:31:06','2026-09-12 05:31:06'),
(12,12,6,'OCCUPIED','2026-09-12 05:31:06','2026-09-12 05:31:06');
/*!40000 ALTER TABLE `tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_item_sales_by_day`
--

DROP TABLE IF EXISTS `v_item_sales_by_day`;
/*!50001 DROP VIEW IF EXISTS `v_item_sales_by_day`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_item_sales_by_day` AS SELECT
 NULL AS `day`,
 NULL AS `item`,
 NULL AS `category`,
 NULL AS `quantity`,
 NULL AS `revenue` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_payments_by_day`
--

DROP TABLE IF EXISTS `v_payments_by_day`;
/*!50001 DROP VIEW IF EXISTS `v_payments_by_day`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_payments_by_day` AS SELECT
 NULL AS `day`,
 NULL AS `method`,
 NULL AS `count`,
 NULL AS `total` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_sales_by_day`
--

DROP TABLE IF EXISTS `v_sales_by_day`;
/*!50001 DROP VIEW IF EXISTS `v_sales_by_day`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_sales_by_day` AS SELECT
 NULL AS `day`,
 NULL AS `orders`,
 NULL AS `items`,
 NULL AS `revenue`,
 NULL AS `paid`,
 NULL AS `unpaid` */;
SET character_set_client = @saved_cs_client;

--
-- Current Database: `rms`
--

USE `rms`;

--
-- Final view structure for view `v_item_sales_by_day`
--

/*!50001 DROP VIEW IF EXISTS `v_item_sales_by_day`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`user`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_item_sales_by_day` AS select cast(`o`.`created_at` as date) AS `day`,`mi`.`name` AS `item`,coalesce(`mc`.`name`,'Uncategorised') AS `category`,sum(`oi`.`quantity`) AS `quantity`,sum(`oi`.`quantity` * `oi`.`price_at_time`) AS `revenue` from (((`order_items` `oi` join `menu_items` `mi` on(`mi`.`id` = `oi`.`menu_item_id`)) left join `menu_categories` `mc` on(`mc`.`id` = `mi`.`category_id`)) join `orders` `o` on(`o`.`id` = `oi`.`order_id`)) where `o`.`status` <> 'CANCELLED' group by cast(`o`.`created_at` as date),`mi`.`id`,`mi`.`name`,`mc`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_payments_by_day`
--

/*!50001 DROP VIEW IF EXISTS `v_payments_by_day`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`user`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_payments_by_day` AS select cast(`payments`.`created_at` as date) AS `day`,`payments`.`method` AS `method`,count(0) AS `count`,sum(`payments`.`amount`) AS `total` from `payments` group by cast(`payments`.`created_at` as date),`payments`.`method` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_sales_by_day`
--

/*!50001 DROP VIEW IF EXISTS `v_sales_by_day`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`user`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_sales_by_day` AS select cast(`o`.`created_at` as date) AS `day`,count(0) AS `orders`,(select coalesce(sum(`oi`.`quantity`),0) from `order_items` `oi` where `oi`.`order_id` = `o`.`id`) AS `items`,coalesce(sum(`o`.`total_amount`),0) AS `revenue`,coalesce(sum(case when exists(select 1 from `payments` `p` where `p`.`order_id` = `o`.`id` limit 1) then `o`.`total_amount` else 0 end),0) AS `paid`,coalesce(sum(case when !exists(select 1 from `payments` `p` where `p`.`order_id` = `o`.`id` limit 1) then `o`.`total_amount` else 0 end),0) AS `unpaid` from `orders` `o` where `o`.`status` <> 'CANCELLED' group by cast(`o`.`created_at` as date) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-09-23 15:48:16
