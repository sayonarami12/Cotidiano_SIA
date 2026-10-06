-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2026 at 01:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `database_cotidiano`
--
CREATE DATABASE IF NOT EXISTS `database_cotidiano` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `database_cotidiano`;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customerID` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phoneNumber` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customerID`, `firstName`, `lastName`, `email`, `phoneNumber`, `password`) VALUES
(1, 'Levan', 'Reganion', 'reganionLevan@gmail.com', '555-0101', ''),
(2, 'Kian', 'Inovejas', 'kianInovehaj@gmail.com', '555-0102', ''),
(3, 'Sheena', 'Muego', 'sheenMuego@gmail.com', '555-0103', ''),
(4, 'Emily', 'Brown', 'emily.brown@email.com', '555-0104', ''),
(5, 'David', 'Wilson', 'david.w@email.com', '555-0105', ''),
(6, 'Sarah', 'Martinez', 'sarah.m@email.com', '555-0106', ''),
(7, 'James', 'Garcia', 'james.g@email.com', '555-0107', ''),
(8, 'Jennifer', 'Rodriguez', 'jennifer.r@email.com', '555-0108', ''),
(9, 'Robert', 'Lee', 'robert.lee@email.com', '555-0109', ''),
(10, 'Lisa', 'Taylor', 'lisa.t@email.com', '555-0110', ''),
(11, 'William', 'Anderson', 'william.a@email.com', '555-0111', ''),
(12, 'Maria', 'Thomas', 'maria.t@email.com', '555-0112', ''),
(13, 'Richard', 'Jackson', 'richard.j@email.com', '555-0113', ''),
(14, 'Patricia', 'White', 'patricia.w@email.com', '555-0114', ''),
(15, 'Christopher', 'Harris', 'chris.h@email.com', '555-0115', ''),
(16, 'Ayela', 'Reganion', 'ayela.dango@email.com', '345-6789', ''),
(17, 'Alfonzo', 'Anuat', 'Alfonzo.anuat@email.com', '345-6799', ''),
(18, 'sample', 'user4', 'sampleuser4@gmail.com', '', 'sample4123'),
(19, 'sample', 'user5', 'sampleuser5@gmail.com', '', 'sample4123'),
(20, 'tito', 'badang', 'titobandang@gmail.com', '', 'titobading'),
(21, 'sample', 'user5', 'sample5@gmail.com', '', 'sampleuser5'),
(22, 'matt', 'omalin', 'mattomalin@gmail.com', '', 'mattomalin123');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `orderID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `orderDate` datetime NOT NULL DEFAULT current_timestamp(),
  `orderStatus` varchar(50) NOT NULL DEFAULT 'Pending',
  `addressID` int(11) NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `isArchived` tinyint(1) NOT NULL DEFAULT 0,
  `archivedAt` datetime DEFAULT NULL
) ;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`orderID`, `customerID`, `orderDate`, `orderStatus`, `addressID`, `totalAmount`, `isArchived`, `archivedAt`) VALUES
(3, 3, '2026-01-18 09:15:00', 'Delivered', 3, 636.00, 0, NULL),
(4, 4, '2026-01-20 16:45:00', 'Delivered', 4, 3446.00, 0, NULL),
(5, 5, '2026-01-22 11:00:00', 'Delivered', 5, 1636.00, 0, NULL),
(6, 1, '2026-01-25 13:30:00', 'Delivered', 11, 570.00, 0, NULL),
(7, 6, '2026-01-27 10:00:00', 'Delivered', 6, 2772.00, 0, NULL),
(8, 7, '2026-01-28 15:20:00', 'Delivered', 7, 1186.00, 0, NULL),
(9, 8, '2026-02-01 09:45:00', 'Delivered', 8, 1691.00, 0, NULL),
(10, 2, '2026-02-02 14:10:00', 'Delivered', 12, 1736.00, 0, NULL),
(11, 9, '2026-02-03 11:30:00', 'Delivered', 9, 1750.00, 0, NULL),
(12, 10, '2026-02-04 16:00:00', 'Delivered', 10, 2420.00, 0, NULL),
(13, 1, '2026-09-23 11:35:35', 'Delivered', 16, 1272.00, 0, NULL),
(14, 18, '2026-09-23 11:44:41', 'Delivered', 17, 1272.00, 0, NULL),
(15, 20, '2026-09-23 12:02:54', 'Delivered', 18, 1272.00, 0, NULL),
(16, 18, '2026-09-23 12:41:01', 'Delivered', 19, 636.00, 0, NULL),
(17, 18, '2026-09-23 12:41:21', 'Delivered', 20, 636.00, 0, NULL),
(18, 18, '2026-09-23 12:41:35', 'Delivered', 21, 636.00, 0, NULL),
(19, 21, '2026-09-23 12:43:08', 'Delivered', 22, 3214.00, 0, NULL),
(20, 21, '2026-09-23 12:43:31', 'Delivered', 23, 1908.00, 0, NULL),
(21, 21, '2026-09-23 12:43:52', 'Delivered', 24, 670.00, 0, NULL),
(22, 18, '2026-09-23 12:48:18', 'Delivered', 25, 636.00, 0, NULL),
(23, 22, '2026-09-23 16:30:20', 'Delivered', 26, 1272.00, 0, NULL),
(27, 21, '2026-09-29 22:45:52', 'Delivered', 32, 636.00, 0, NULL),
(30, 18, '2026-09-29 23:08:23', 'Delivered', 35, 636.00, 0, NULL),
(33, 18, '2026-09-29 23:44:28', 'Delivered', 38, 500.00, 0, NULL),
(34, 18, '2026-09-29 23:55:10', 'Cancelled', 39, 636.00, 0, NULL),
(35, 18, '2026-10-06 18:58:32', 'Pending', 40, 636.00, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `orderItemID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unitPrice` decimal(10,2) NOT NULL
) ;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`orderItemID`, `orderID`, `productID`, `quantity`, `unitPrice`) VALUES
(6, 3, 4, 1, 636.00),
(7, 4, 9, 2, 580.00),
(8, 4, 11, 1, 636.00),
(10, 4, 6, 1, 1000.00),
(11, 5, 8, 1, 636.00),
(12, 5, 3, 1, 1000.00),
(13, 6, 7, 1, 570.00),
(14, 7, 1, 2, 636.00),
(15, 7, 10, 1, 500.00),
(20, 9, 4, 1, 636.00),
(21, 9, 6, 1, 555.00),
(22, 10, 5, 1, 636.00),
(23, 10, 11, 1, 1100.00),
(24, 11, 9, 1, 580.00),
(26, 11, 7, 1, 520.00),
(27, 12, 8, 2, 460.00),
(28, 12, 10, 1, 780.00),
(29, 12, 3, 1, 720.00),
(30, 13, 1, 2, 636.00),
(31, 14, 1, 2, 636.00),
(32, 15, 1, 1, 636.00),
(33, 15, 3, 1, 636.00),
(34, 16, 1, 1, 636.00),
(35, 17, 1, 1, 636.00),
(36, 18, 4, 1, 636.00),
(37, 19, 3, 1, 636.00),
(38, 19, 4, 1, 636.00),
(39, 19, 16, 1, 670.00),
(40, 19, 11, 1, 636.00),
(41, 19, 10, 1, 636.00),
(42, 20, 4, 1, 636.00),
(43, 20, 5, 1, 636.00),
(44, 20, 8, 1, 636.00),
(45, 21, 16, 1, 670.00),
(46, 22, 1, 1, 636.00),
(47, 23, 3, 2, 636.00),
(51, 27, 3, 1, 636.00),
(54, 30, 5, 1, 636.00),
(57, 33, 1, 1, 500.00),
(58, 34, 3, 1, 636.00),
(59, 35, 3, 1, 636.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `paymentID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `paymentMethod` varchar(50) NOT NULL,
  `paymentDate` datetime NOT NULL DEFAULT current_timestamp(),
  `paymentAmount` decimal(10,2) NOT NULL,
  `paymentStatus` varchar(50) NOT NULL DEFAULT 'Pending'
) ;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`paymentID`, `orderID`, `paymentMethod`, `paymentDate`, `paymentAmount`, `paymentStatus`) VALUES
(3, 3, 'Credit Card', '2026-01-18 09:20:00', 636.00, 'Completed'),
(5, 5, 'Credit Card', '2026-01-22 11:05:00', 1636.00, 'Completed'),
(6, 6, 'Credit Card', '2026-01-25 13:35:00', 570.00, 'Completed'),
(7, 7, 'Credit Card', '2026-01-27 10:05:00', 2772.00, 'Completed'),
(10, 10, 'Credit Card', '2026-02-02 14:15:00', 1736.00, 'Completed'),
(14, 27, 'Credit Card', '2026-09-29 22:45:52', 636.00, 'Completed'),
(17, 30, 'GCash', '2026-09-29 23:08:23', 636.00, 'Completed'),
(20, 33, 'Credit Card', '2026-09-29 23:44:28', 500.00, 'Completed'),
(21, 34, 'Credit Card', '2026-09-29 23:55:10', 636.00, 'Completed'),
(22, 35, 'GCash', '2026-10-06 18:58:32', 636.00, 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `productID` int(11) NOT NULL,
  `productName` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `stockQuantity` int(11) NOT NULL DEFAULT 0,
  `size_ML` int(11) DEFAULT 30,
  `Image_url` varchar(255) NOT NULL,
  `family` varchar(100) DEFAULT 'Cotidiano Fragrance',
  `category` enum('male','female','unisex') DEFAULT 'unisex',
  `badge` varchar(50) DEFAULT NULL,
  `notes_top` varchar(255) DEFAULT '–',
  `notes_heart` varchar(255) DEFAULT '–',
  `notes_base` varchar(255) DEFAULT '–'
) ;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`productID`, `productName`, `description`, `price`, `discount_price`, `stockQuantity`, `size_ML`, `Image_url`, `family`, `category`, `badge`, `notes_top`, `notes_heart`, `notes_base`) VALUES
(1, 'Anima', 'Romantic floral scent perfect for special occasions', 636.00, 500.00, 9, 50, 'assets\\images\\products\\anima.png', 'Cotidiano Fragrance', 'unisex', NULL, 'Rose', 'Peony', 'Vanilla'),
(3, 'Midnight Bloom', 'Bold and confident masculine scent', 636.00, NULL, 8, 50, 'assets\\images\\products\\midnight_bloom.png', 'Cotidiano Fragrance', 'male', NULL, 'Lemon', 'Cedarwood', 'Amber'),
(4, 'Lina', 'Citrusy floral blend ideal for daily wear', 636.00, NULL, 10, 50, 'assets\\images\\products\\lina.png', 'Cotidiano Fragrance', 'female', NULL, 'Grapefruit', 'Lilac', 'Sandalwood'),
(5, 'D\'Iconic', 'Clean and elegant modern fragrance', 636.00, NULL, 11, 50, 'assets\\images\\products\\d_iconic.png', 'Cotidiano Fragrance', 'female', NULL, 'Lavender', 'Iris', 'Vetiver'),
(6, 'libra', 'Classic masculine scent with refined notes', 636.00, NULL, 12, 50, 'assets\\images\\products\\libra.png', 'Cotidiano Fragrance', 'male', NULL, 'Anise', 'Almond', 'Cinnamon'),
(7, 'Born in Roma', 'Intense and luxurious evening fragrance', 636.00, NULL, 12, 50, 'assets\\images\\products\\biri.png', 'Cotidiano Fragrance', 'female', NULL, 'Neroli', 'Rose', 'Oud'),
(8, 'Hibiscus Mahajad', 'Tropical floral with vanilla and musk notes, perfect for summer', 636.00, NULL, 12, 50, 'assets\\images\\products\\hibiscus.png', 'Cotidiano Fragrance', 'female', NULL, 'Hibiscus', 'Plumeria', 'Vanilla'),
(9, 'Mango Venom', 'Premium Filipino-inspired fragrance', 636.00, NULL, 12, 50, 'assets\\images\\products\\mango_venom.png', 'Cotidiano Fragrance', 'unisex', NULL, 'Citrus', 'mango', 'Coconut'),
(10, 'Talisman', 'Fresh citrus and musk blend, ideal for the soft aesthetic', 636.00, NULL, 12, 50, 'assets\\images\\products\\talisman.png', 'Cotidiano Fragrance', 'unisex', NULL, 'Mandarin', 'Peach', 'Musk'),
(11, 'Curiosity V2', 'Elegant powdery scent with sophisticated appeal', 636.00, NULL, 12, 50, 'assets\\images\\products\\curiosity_v2.png', 'Cotidiano Fragrance', 'unisex', NULL, 'Peach', 'Tuberose', 'Powder'),
(16, 'Seduction Supreme', NULL, 670.00, NULL, 1, 30, 'assets/images/products/seduction_supreme.png', 'Cotidiano Fragrance', 'unisex', NULL, 'vanilla', 'Peach', 'Musk');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `reviewID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `reviewText` text DEFAULT NULL,
  `reviewDate` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`reviewID`, `customerID`, `productID`, `rating`, `reviewText`, `reviewDate`) VALUES
(3, 2, 3, 5, 'Love this ocean breeze smell!', '2026-01-22 10:30:00'),
(4, 2, 5, 4, 'Refreshing citrus scent, good for summer.', '2026-01-22 10:35:00'),
(5, 3, 4, 5, 'Perfect vanilla perfume!', '2026-01-25 14:00:00'),
(6, 5, 8, 4, 'Beautiful cherry blossom fragrance.', '2026-01-28 09:15:00'),
(7, 5, 3, 5, 'Fresh and clean scent!', '2026-01-28 09:20:00'),
(8, 1, 7, 5, 'Rich sandalwood, very luxurious.', '2026-02-01 11:00:00'),
(9, 6, 1, 4, 'Lovely lavender, calming.', '2026-02-02 16:30:00'),
(11, 2, 11, 5, 'Tropical paradise indeed! Love it!', '2026-02-04 10:00:00'),
(13, 9, 4, 5, 'Warm vanilla, perfect for winter.', '2026-02-05 09:00:00'),
(14, 10, 5, 4, 'Energizing citrus burst!', '2026-02-05 15:45:00'),
(15, 4, 9, 5, 'Deep amber woods, absolutely gorgeous!', '2026-02-05 18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sales_transaction`
--

CREATE TABLE `sales_transaction` (
  `saleID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `saleAmount` decimal(10,2) NOT NULL,
  `recordedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_transaction`
--

INSERT INTO `sales_transaction` (`saleID`, `orderID`, `saleAmount`, `recordedAt`) VALUES
(1, 3, 636.00, '2026-09-23 12:18:23'),
(2, 5, 1636.00, '2026-09-23 12:18:23'),
(3, 6, 570.00, '2026-09-23 12:18:23'),
(4, 10, 1736.00, '2026-09-23 12:18:23'),
(5, 14, 1272.00, '2026-09-23 12:18:23'),
(80, 21, 670.00, '2026-09-23 12:44:32'),
(82, 20, 1908.00, '2026-09-23 12:44:35'),
(84, 19, 3214.00, '2026-09-23 12:44:57'),
(86, 18, 636.00, '2026-09-23 12:44:57'),
(88, 17, 636.00, '2026-09-23 12:44:57'),
(91, 16, 636.00, '2026-09-23 12:45:04'),
(93, 15, 1272.00, '2026-09-23 12:45:08'),
(95, 13, 1272.00, '2026-09-23 12:45:16'),
(97, 12, 2420.00, '2026-09-23 12:45:16'),
(99, 9, 1691.00, '2026-09-23 12:45:21'),
(102, 11, 1750.00, '2026-09-23 12:45:25'),
(104, 8, 1186.00, '2026-09-23 12:45:28'),
(106, 7, 2772.00, '2026-09-23 12:45:32'),
(108, 4, 3446.00, '2026-09-23 12:45:35'),
(143, 22, 636.00, '2026-09-23 12:48:37'),
(227, 23, 1272.00, '2026-09-23 16:31:09'),
(640, 27, 636.00, '2026-09-29 22:48:37'),
(801, 33, 500.00, '2026-09-29 23:48:43'),
(820, 30, 636.00, '2026-09-29 23:50:10');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_address`
--

CREATE TABLE `shipping_address` (
  `addressID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `addressLabel` varchar(20) DEFAULT NULL,
  `phoneNumber` varchar(15) DEFAULT NULL,
  `streetAddress` varchar(200) NOT NULL,
  `city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `postalCode` varchar(10) NOT NULL,
  `isDefault` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_address`
--

INSERT INTO `shipping_address` (`addressID`, `customerID`, `addressLabel`, `phoneNumber`, `streetAddress`, `city`, `province`, `postalCode`, `isDefault`) VALUES
(1, 1, 'Home', NULL, '123 Main St', 'Manila', 'Metro Manila', '1000', 1),
(2, 2, 'Home', NULL, '456 Oak Ave', 'Quezon City', 'Metro Manila', '1100', 1),
(3, 3, 'Home', NULL, '789 Pine Rd', 'Makati', 'Metro Manila', '1200', 1),
(4, 4, 'Office', NULL, '321 Elm St', 'Pasig', 'Metro Manila', '1600', 1),
(5, 5, 'Home', NULL, '654 Maple Dr', 'Taguig', 'Metro Manila', '1630', 1),
(6, 6, 'Home', NULL, '987 Cedar Ln', 'Mandaluyong', 'Metro Manila', '1550', 1),
(7, 7, 'Home', NULL, '147 Birch Way', 'San Juan', 'Metro Manila', '1500', 1),
(8, 8, 'Home', NULL, '258 Spruce Ct', 'Pasay', 'Metro Manila', '1300', 1),
(9, 9, 'Office', NULL, '369 Willow St', 'Parañaque', 'Metro Manila', '1700', 1),
(10, 10, 'Home', NULL, '741 Ash Blvd', 'Las Piñas', 'Metro Manila', '1740', 1),
(11, 1, 'Office', NULL, '852 Cherry Ave', 'Manila', 'Metro Manila', '1001', 0),
(12, 2, 'Parents', NULL, '963 Poplar Rd', 'Antipolo', 'Rizal', '1870', 0),
(13, 11, 'Home', NULL, '159 Walnut Dr', 'Caloocan', 'Metro Manila', '1400', 1),
(14, 12, 'Home', NULL, '357 Hickory Ln', 'Malabon', 'Metro Manila', '1470', 1),
(15, 13, 'Home', NULL, '486 Pecan Way', 'Navotas', 'Metro Manila', '1485', 1),
(16, 1, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(17, 18, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(18, 20, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(19, 18, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(20, 18, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(21, 18, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(22, 21, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(23, 21, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(24, 21, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(25, 18, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(26, 22, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(27, 18, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'Sandio nisio', '1700', 0),
(28, 18, 'home', '09626338905', '7707 N.t Garcia San dionisio', 'Paranaque City', 'manila', '1700', 1),
(29, 21, 'home', '0978235647', '7701 new york', 'Paranaque City', 'bisaya', '1700', 1),
(30, 21, NULL, NULL, '7701 new york', 'Paranaque City', 'bisaya', '1700', 0),
(31, 21, NULL, NULL, '7701 new york', 'Paranaque City', 'bisaya', '1700', 0),
(32, 21, NULL, NULL, '7701 new york', 'Paranaque City', 'bisaya', '1700', 0),
(33, 18, NULL, NULL, '7707 N.t Garcia San dionisio', 'Paranaque City', 'manila', '1700', 0),
(34, 18, NULL, NULL, '7707 N.t Garcia San dionisio', 'Paranaque City', 'manila', '1700', 0),
(35, 18, NULL, NULL, '7707 N.t Garcia San dionisio', 'Paranaque City', 'manila', '1700', 0),
(36, 18, NULL, NULL, '7707 N.t Garcia San dionisio', 'Paranaque City', 'manila', '1700', 0),
(37, 18, NULL, NULL, '7707 N.t Garcia San dionisio', 'Paranaque City', 'manila', '1700', 0),
(38, 18, NULL, NULL, '7707 N.t Garcia San dio', 'Paranaque City', 'manila', '1700', 0),
(39, 18, NULL, NULL, '7707 N.t Garcia San dionisio', 'Paranaque City', 'manila', '1700', 0),
(40, 18, NULL, NULL, '7707 N.t Garcia San dionisio', 'Paranaque City', 'manila', '1700', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customerID`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_name` (`lastName`,`firstName`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`orderID`),
  ADD KEY `addressID` (`addressID`),
  ADD KEY `idx_customer_order` (`customerID`),
  ADD KEY `idx_order_date` (`orderDate`),
  ADD KEY `idx_order_status` (`orderStatus`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`orderItemID`),
  ADD KEY `idx_order` (`orderID`),
  ADD KEY `idx_product` (`productID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`paymentID`),
  ADD UNIQUE KEY `orderID` (`orderID`),
  ADD KEY `idx_payment_status` (`paymentStatus`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productID`),
  ADD KEY `idx_product_name` (`productName`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`reviewID`),
  ADD KEY `customerID` (`customerID`),
  ADD KEY `idx_product_review` (`productID`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `sales_transaction`
--
ALTER TABLE `sales_transaction`
  ADD PRIMARY KEY (`saleID`),
  ADD UNIQUE KEY `orderID` (`orderID`),
  ADD KEY `idx_recorded_at` (`recordedAt`);

--
-- Indexes for table `shipping_address`
--
ALTER TABLE `shipping_address`
  ADD PRIMARY KEY (`addressID`),
  ADD KEY `idx_customer` (`customerID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `orderItemID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `paymentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `productID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `reviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_transaction`
--
ALTER TABLE `sales_transaction`
  MODIFY `saleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1077;

--
-- AUTO_INCREMENT for table `shipping_address`
--
ALTER TABLE `shipping_address`
  MODIFY `addressID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customer` (`customerID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`addressID`) REFERENCES `shipping_address` (`addressID`) ON UPDATE CASCADE;

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `order` (`orderID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `product` (`productID`) ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `order` (`orderID`) ON UPDATE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customer` (`customerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `product` (`productID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales_transaction`
--
ALTER TABLE `sales_transaction`
  ADD CONSTRAINT `sales_transaction_order_fk` FOREIGN KEY (`orderID`) REFERENCES `order` (`orderID`) ON UPDATE CASCADE;

--
-- Constraints for table `shipping_address`
--
ALTER TABLE `shipping_address`
  ADD CONSTRAINT `shipping_address_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customer` (`customerID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
