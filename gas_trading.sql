-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2025 at 03:23 AM
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
-- Database: `gas_trading`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `account_type` varchar(225) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `email`, `password`, `account_type`, `created_at`) VALUES
(5, 'Employee', 'nicothings@gmail.com', '$2y$10$1tyOyq/r4jLMr6sb7IvRRe0zQUsU.hafi84lM.Dy67yiq1FIRGyZm', 'employee', '2025-03-05 13:34:25'),
(8, 'Admin', 'admin@gmail.com', '$2y$10$AN73xcK6HftoJx4WWpn7kO4M0ldXwzMGV27lIib6dnNU8aVCsqgiW', 'admin', '2025-03-08 05:52:38');

-- --------------------------------------------------------

--
-- Table structure for table `completed_cylinders`
--

CREATE TABLE `completed_cylinders` (
  `id` int(11) NOT NULL,
  `cylinder_number` varchar(225) NOT NULL,
  `gas_type` varchar(50) NOT NULL,
  `capacity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `completed_cylinders`
--

INSERT INTO `completed_cylinders` (`id`, `cylinder_number`, `gas_type`, `capacity`, `price`, `created_at`) VALUES
(1, 'LPG-001', 'LPG', 5.00, 0.00, '2024-11-28 14:25:16'),
(2, 'LPG-002', 'LPG', 5.00, 0.00, '2024-11-28 14:33:03'),
(3, 'LPG-003', 'LPG', 10.00, 0.00, '2024-11-29 07:03:35'),
(4, 'LPG-005', 'LPG', 5.00, 0.00, '2024-11-29 08:20:25'),
(5, 'LPG-006', 'LPG', 5.00, 0.00, '2024-11-29 08:21:29'),
(6, 'LPG-0013', 'LPG', 10.00, 0.00, '2024-11-29 08:44:53'),
(7, 'LPG-007', 'LPG', 5.00, 0.00, '2025-03-05 21:41:44'),
(8, 'ARG-001', 'ARGON', 5.00, 0.00, '2025-03-05 22:33:49'),
(9, 'ARG-002', 'ARGON', 5.00, 0.00, '2025-03-05 22:33:49'),
(10, 'ARG-004', 'ARGON', 10.00, 0.00, '2025-03-05 22:51:06'),
(11, 'ARG-005', 'ARGON', 10.00, 0.00, '2025-03-05 22:51:06'),
(12, 'LPG-008', 'LPG', 5.00, 0.00, '2025-03-05 23:52:12'),
(13, 'ARG-004', 'Argon', 10.00, 0.00, '2025-03-06 02:38:40'),
(14, 'ARG-005', 'Argon', 10.00, 0.00, '2025-03-06 02:38:40'),
(15, 'ARG-006', 'ARGON', 10.00, 0.00, '2025-03-06 02:39:48'),
(16, 'ARG-007', 'ARGON', 10.00, 0.00, '2025-03-06 02:39:48'),
(17, 'ARG-006', 'Argon', 10.00, 0.00, '2025-03-06 02:42:13'),
(18, 'ARG-007', 'Argon', 10.00, 0.00, '2025-03-06 02:42:13'),
(19, 'Oxy-001', 'OXYGEN', 5.00, 0.00, '2025-03-06 18:24:37'),
(20, 'OXY-005', 'OXYGEN', 5.00, 0.00, '2025-03-06 18:24:37'),
(21, 'Oxy-001', 'OXYGEN', 5.00, 0.00, '2025-03-06 18:44:47'),
(22, 'ARG-003', 'ARGON', 5.00, 0.00, '2025-03-06 18:47:42'),
(23, 'OXY-005', 'OXYGEN', 5.00, 0.00, '2025-03-07 01:52:54'),
(24, 'ARG-003', 'ARGON', 5.00, 0.00, '2025-03-07 11:12:32'),
(25, 'LPG-008', 'LPG', 5.00, 0.00, '2025-03-07 11:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `delivery_date` date NOT NULL,
  `delivery_truck` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `location`, `delivery_date`, `delivery_truck`, `created_at`) VALUES
(1, 'Jollibee', 'Polomolok South Cotabato', '2024-11-29', 'LAM1254', '2024-11-28 22:25:16'),
(2, 'Dave', 'General Santos City', '2024-11-28', 'LAM1254', '2024-11-28 22:33:03'),
(3, 'Mang Inasal', 'General Santos City', '2024-11-30', 'LAM7894', '2024-11-29 15:03:35'),
(4, 'Custarda', 'Polomolok South Cotabato', '2024-12-01', 'LAM1254', '2024-11-29 16:20:25'),
(5, 'Jollibee', 'Polomolok South Cotabato', '2024-12-02', 'LAM1254', '2024-11-29 16:21:29'),
(6, 'zynn', 'Polomolok South Cotabato', '2024-11-30', 'LAM7894', '2024-11-29 16:44:53'),
(7, 'Chooks to Go', 'Polomolok South Cotabato', '2025-03-07', 'LAM7894', '2025-03-06 05:41:44'),
(8, 'Dave', 'General Santos City', '2025-03-10', 'LAM1254', '2025-03-06 06:26:28'),
(9, 'Dave', 'General Santos City', '2025-03-10', 'LAM1254', '2025-03-06 06:30:08'),
(10, 'Mang Omengs shop', 'Polomolok South Cotabato', '2025-03-08', 'LAM7894', '2025-03-06 06:33:49'),
(11, 'Sample', 'General Santos City', '2025-03-11', 'LAM7894', '2025-03-06 06:49:20'),
(12, 'Sample', 'Polomolok South Cotabato', '2025-03-07', 'LAM1254', '2025-03-06 06:50:35'),
(13, 'Sample Order', 'Polomolok South Cotabato', '2025-03-07', 'LAM1254', '2025-03-06 06:51:06'),
(17, 'Orders', 'Polomolok South Cotabato', '2025-03-06', 'LAM1254', '2025-03-06 07:45:28'),
(18, 'Chooks to Go', 'Polomolok South Cotabato', '2025-03-07', 'LAM1254', '2025-03-06 07:52:12'),
(19, 'qqq', 'Polomolok South Cotabato', '2025-03-06', 'LAM1254', '2025-03-06 09:48:52'),
(20, 'dd', 'Polomolok South Cotabato', '2025-03-06', 'LAM1254', '2025-03-06 10:37:46'),
(21, 'Arwin works ', 'Polomolok South Cotabato', '2025-03-07', 'LAM7894', '2025-03-06 10:38:40'),
(22, 'qqq', 'Polomolok South Cotabato', '2025-03-14', 'LAM1254', '2025-03-06 10:39:48'),
(23, 'www', 'Polomolok South Cotabato', '2025-03-11', 'LAM1254', '2025-03-06 10:42:13'),
(24, 'www', 'Polomolok South Cotabato', '2025-03-08', 'LAM1254', '2025-03-07 02:24:37'),
(25, 'rrr', 'General Santos City', '2025-03-07', 'LAM7894', '2025-03-07 02:44:47'),
(26, 'xx', 'Polomolok South Cotabato', '2025-03-14', 'LAM7894', '2025-03-07 02:47:42'),
(27, 'vs', 'Polomolok South Cotabato', '2025-03-12', 'LAM1254', '2025-03-07 02:53:13'),
(28, 's', 'General Santos City', '2025-03-11', 'LAM1254', '2025-03-07 02:59:28'),
(29, 'b', 'Polomolok South Cotabato', '2025-03-14', 'LAM1254', '2025-03-07 03:43:25'),
(30, 'ee', 'General Santos City', '2025-03-20', 'LAM7894', '2025-03-07 09:52:54'),
(31, 'bbb', 'General Santos City', '2025-03-27', 'LAM1254', '2025-03-07 19:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `cylinders`
--

CREATE TABLE `cylinders` (
  `id` int(11) NOT NULL,
  `cylinder_number` varchar(50) NOT NULL,
  `gas_type` varchar(50) NOT NULL,
  `capacity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(10) DEFAULT 'Available',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cylinders`
--

INSERT INTO `cylinders` (`id`, `cylinder_number`, `gas_type`, `capacity`, `price`, `status`, `created_at`, `updated_at`) VALUES
(10, 'LPG-009', 'LPG', 5.00, 190.00, 'Available', '2024-11-29 15:11:27', '2025-03-09 07:51:28'),
(11, 'LPG-010', 'LPG', 10.00, 455.00, 'Available', '2024-11-29 15:11:27', '2025-03-09 07:48:51'),
(17, 'ARG-006', 'Argon', 10.00, 250.00, 'Available', '2024-11-29 15:12:52', '2024-11-29 15:12:52'),
(18, 'ARG-007', 'Argon', 10.00, 250.00, 'Available', '2024-11-29 15:12:52', '2024-11-29 15:12:52'),
(25, 'LPG-001', 'LPG', 10.00, 455.00, 'Available', '2025-03-07 03:45:24', '2025-03-07 03:45:24'),
(26, 'LPG-002', 'LPG', 5.00, 230.00, 'Available', '2025-03-07 16:30:17', '2025-03-07 16:30:17'),
(27, 'LPG-003', 'LPG', 5.00, 230.00, 'Available', '2025-03-07 16:30:17', '2025-03-07 16:30:17'),
(28, 'N001', 'Nitrogen', 10.00, 500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(29, 'N002', 'Nitrogen', 10.00, 500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(30, 'N003', 'Nitrogen', 10.00, 500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(31, 'N004', 'Nitrogen', 10.00, 500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(32, 'N005', 'Nitrogen', 10.00, 500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(33, 'N006', 'Nitrogen', 20.00, 900.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(34, 'N007', 'Nitrogen', 20.00, 900.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(35, 'N008', 'Nitrogen', 20.00, 900.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(36, 'N009', 'Nitrogen', 20.00, 900.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(37, 'N010', 'Nitrogen', 20.00, 900.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(38, 'N011', 'Nitrogen', 40.00, 1500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(39, 'N012', 'Nitrogen', 40.00, 1500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(40, 'N013', 'Nitrogen', 40.00, 1500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(41, 'N014', 'Nitrogen', 40.00, 1500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(42, 'N015', 'Nitrogen', 40.00, 1500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(43, 'N016', 'Nitrogen', 50.00, 1800.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(44, 'N017', 'Nitrogen', 50.00, 1800.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(45, 'N018', 'Nitrogen', 50.00, 1800.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(46, 'N019', 'Nitrogen', 50.00, 1800.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(47, 'N020', 'Nitrogen', 50.00, 1800.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(48, 'N021', 'Nitrogen', 80.00, 2500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(49, 'N022', 'Nitrogen', 80.00, 2500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(50, 'N023', 'Nitrogen', 80.00, 2500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(51, 'N024', 'Nitrogen', 80.00, 2500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(52, 'N025', 'Nitrogen', 80.00, 2500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(53, 'N026', 'Nitrogen', 125.00, 3500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(54, 'N027', 'Nitrogen', 125.00, 3500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(55, 'N028', 'Nitrogen', 125.00, 3500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(56, 'N029', 'Nitrogen', 125.00, 3500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(57, 'N030', 'Nitrogen', 125.00, 3500.00, 'Available', '2025-03-08 15:34:35', '2025-03-08 15:34:35'),
(58, 'A001', 'Acetylene', 10.00, 700.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(59, 'A002', 'Acetylene', 10.00, 700.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(60, 'A003', 'Acetylene', 10.00, 700.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(61, 'A004', 'Acetylene', 10.00, 700.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(62, 'A005', 'Acetylene', 10.00, 700.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(63, 'A006', 'Acetylene', 20.00, 1200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(64, 'A007', 'Acetylene', 20.00, 1200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(65, 'A008', 'Acetylene', 20.00, 1200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(66, 'A009', 'Acetylene', 20.00, 1200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(67, 'A010', 'Acetylene', 20.00, 1200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(68, 'A011', 'Acetylene', 40.00, 2000.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(69, 'A012', 'Acetylene', 40.00, 2000.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(70, 'A013', 'Acetylene', 40.00, 2000.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(71, 'A014', 'Acetylene', 40.00, 2000.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(72, 'A015', 'Acetylene', 40.00, 2000.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(73, 'A016', 'Acetylene', 50.00, 2500.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(74, 'A017', 'Acetylene', 50.00, 2500.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(75, 'A018', 'Acetylene', 50.00, 2500.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(76, 'A019', 'Acetylene', 50.00, 2500.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(77, 'A020', 'Acetylene', 50.00, 2500.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(78, 'A021', 'Acetylene', 80.00, 3200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(79, 'A022', 'Acetylene', 80.00, 3200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(80, 'A023', 'Acetylene', 80.00, 3200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(81, 'A024', 'Acetylene', 80.00, 3200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(82, 'A025', 'Acetylene', 80.00, 3200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(83, 'A026', 'Acetylene', 125.00, 4200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(84, 'A027', 'Acetylene', 125.00, 4200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(85, 'A028', 'Acetylene', 125.00, 4200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(86, 'A029', 'Acetylene', 125.00, 4200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(87, 'A030', 'Acetylene', 125.00, 4200.00, 'Available', '2025-03-08 15:38:07', '2025-03-08 15:38:07'),
(88, 'H001', 'Hydrogen', 10.00, 800.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(89, 'H002', 'Hydrogen', 10.00, 800.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(90, 'H003', 'Hydrogen', 10.00, 800.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(91, 'H004', 'Hydrogen', 10.00, 800.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(92, 'H005', 'Hydrogen', 10.00, 800.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(93, 'H006', 'Hydrogen', 20.00, 1500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(94, 'H007', 'Hydrogen', 20.00, 1500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(95, 'H008', 'Hydrogen', 20.00, 1500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(96, 'H009', 'Hydrogen', 20.00, 1500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(97, 'H010', 'Hydrogen', 20.00, 1500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(98, 'H011', 'Hydrogen', 40.00, 2500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(99, 'H012', 'Hydrogen', 40.00, 2500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(100, 'H013', 'Hydrogen', 40.00, 2500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(101, 'H014', 'Hydrogen', 40.00, 2500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(102, 'H015', 'Hydrogen', 40.00, 2500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(103, 'H016', 'Hydrogen', 50.00, 3200.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(104, 'H017', 'Hydrogen', 50.00, 3200.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(105, 'H018', 'Hydrogen', 50.00, 3200.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(106, 'H019', 'Hydrogen', 50.00, 3200.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(107, 'H020', 'Hydrogen', 50.00, 3200.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(108, 'H021', 'Hydrogen', 80.00, 4000.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(109, 'H022', 'Hydrogen', 80.00, 4000.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(110, 'H023', 'Hydrogen', 80.00, 4000.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(111, 'H024', 'Hydrogen', 80.00, 4000.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(112, 'H025', 'Hydrogen', 80.00, 4000.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(113, 'H026', 'Hydrogen', 125.00, 5500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(114, 'H027', 'Hydrogen', 125.00, 5500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(115, 'H028', 'Hydrogen', 125.00, 5500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(116, 'H029', 'Hydrogen', 125.00, 5500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(117, 'H030', 'Hydrogen', 125.00, 5500.00, 'Available', '2025-03-08 15:38:35', '2025-03-08 15:38:35'),
(118, 'C001', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-08 15:47:57', '2025-03-08 15:47:57'),
(119, 'C005', 'CARBON DIOXIDE', 20.00, 1400.00, 'Available', '2025-03-08 15:47:57', '2025-03-08 15:47:57'),
(120, 'C002', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-08 15:48:31', '2025-03-08 15:48:31'),
(121, 'OXY-010', 'OXYGEN', 5.00, 230.00, 'Available', '2025-03-09 05:44:37', '2025-03-09 05:44:37'),
(122, 'OXY001', 'Oxygen', 20.00, 6600.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(123, 'OXY002', 'Oxygen', 20.00, 6600.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(124, 'OXY003', 'Oxygen', 20.00, 6600.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(125, 'OXY004', 'Oxygen', 20.00, 6600.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(126, 'OXY005', 'Oxygen', 20.00, 6600.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(127, 'OXY006', 'Oxygen', 20.00, 6600.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(128, 'OXY007', 'Oxygen', 20.00, 6600.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(129, 'OXY008', 'Oxygen', 20.00, 6600.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(130, 'OXY009', 'Oxygen', 20.00, 6600.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(131, 'OXY010', 'Oxygen', 20.00, 6600.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(132, 'OXY011', 'Oxygen', 50.00, 9000.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(133, 'OXY012', 'Oxygen', 50.00, 9000.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(134, 'OXY013', 'Oxygen', 50.00, 9000.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(135, 'OXY014', 'Oxygen', 50.00, 9000.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(136, 'OXY015', 'Oxygen', 50.00, 9000.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(137, 'OXY016', 'Oxygen', 50.00, 9000.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(138, 'OXY017', 'Oxygen', 50.00, 9000.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(139, 'OXY018', 'Oxygen', 50.00, 9000.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(140, 'OXY019', 'Oxygen', 50.00, 9000.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(141, 'OXY020', 'Oxygen', 50.00, 9000.00, 'Available', '2025-03-09 06:07:02', '2025-03-09 06:07:02'),
(142, 'C001', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(143, 'C002', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(144, 'C003', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(145, 'C004', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(146, 'C005', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(147, 'C006', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(148, 'C007', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(149, 'C008', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(150, 'C009', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(151, 'C010', 'CARBON DIOXIDE', 10.00, 750.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(152, 'C011', 'CARBON DIOXIDE', 20.00, 1400.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(153, 'C012', 'CARBON DIOXIDE', 20.00, 1400.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(154, 'C013', 'CARBON DIOXIDE', 20.00, 1400.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(155, 'C014', 'CARBON DIOXIDE', 20.00, 1400.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(156, 'C015', 'CARBON DIOXIDE', 20.00, 1400.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(157, 'C016', 'CARBON DIOXIDE', 20.00, 1400.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(158, 'C017', 'CARBON DIOXIDE', 20.00, 1400.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(159, 'C018', 'CARBON DIOXIDE', 20.00, 1400.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(160, 'C019', 'CARBON DIOXIDE', 20.00, 1400.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(161, 'C020', 'CARBON DIOXIDE', 20.00, 1400.00, 'Available', '2025-03-09 06:44:22', '2025-03-09 06:44:22'),
(162, 'LPG-015', 'LPG', 10.00, 455.00, 'Available', '2025-03-09 06:46:48', '2025-03-09 07:11:00'),
(163, 'LPG-011', 'LPG', 10.00, 455.00, 'Available', '2025-03-09 06:46:48', '2025-03-09 07:13:05'),
(164, 'LPG-012', 'LPG', 10.00, 455.00, 'Available', '2025-03-09 06:46:48', '2025-03-09 07:22:02'),
(165, 'LPG-013', 'LPG', 10.00, 455.00, 'Available', '2025-03-09 06:48:40', '2025-03-09 07:24:01'),
(166, 'LPG-014', 'LPG', 10.00, 455.00, 'Available', '2025-03-09 06:48:40', '2025-03-09 06:48:40');

-- --------------------------------------------------------

--
-- Table structure for table `deleted_cylinders`
--

CREATE TABLE `deleted_cylinders` (
  `id` int(11) NOT NULL,
  `cylinder_number` varchar(50) NOT NULL,
  `gas_type` varchar(50) DEFAULT NULL,
  `capacity` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `deleted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deleted_cylinders`
--

INSERT INTO `deleted_cylinders` (`id`, `cylinder_number`, `gas_type`, `capacity`, `price`, `deleted_at`) VALUES
(1, 'LPG-004', 'LPG', 10.00, 290.00, '2024-11-28 22:24:31');

-- --------------------------------------------------------

--
-- Table structure for table `deleted_expenses`
--

CREATE TABLE `deleted_expenses` (
  `id` int(11) NOT NULL,
  `expense_type` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `webcam_path` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deleted_expenses`
--

INSERT INTO `deleted_expenses` (`id`, `expense_type`, `amount`, `date`, `notes`, `file_path`, `webcam_path`, `deleted_at`) VALUES
(1, 'Delivery Fuel', 1500.00, '2024-11-29', 'Full Tank', '', 'uploads/webcam_67491bf61bd993.05404859.png', '2024-11-29 02:09:20'),
(2, 'Delivery Fuel', 45.00, '2024-11-30', 'fghj./', '', 'uploads/ webcam_67491f54906a05.47392806.png', '2024-11-29 02:09:53'),
(3, 'Delivery Fuel', 7.00, '2024-11-30', '2234', '', 'uploads/webcam_67491f80bc8125.44820696.png', '2024-11-29 02:09:56'),
(4, 'Meal Allowance', 200.00, '2024-11-29', 'driver', '', '../admin/uploads/webcam_67491d04ad1704.09652773.png', '2024-11-29 02:09:58'),
(5, 'Delivery Fuel', 555555.00, '2024-11-30', 'gggggg', 'uploads/file_674922aacb4585.05612584.jpg', '', '2024-11-29 02:26:05'),
(6, 'Delivery Fuel', 99999999.99, '2024-11-30', '', '', 'uploads/webcam_67492571c2ecb4.97940166.png', '2024-11-29 02:26:37'),
(7, 'Meal Allowance', 4444.00, '2024-11-30', '23', '', 'uploads/webcam_6749225e634c18.25468610.png', '2024-11-29 02:26:40'),
(8, 'Miscellaneous Supplies', 123.00, '2024-11-30', '1', '', 'uploads/webcam_67491f9f470ea9.01758080.png', '2024-11-29 02:26:44'),
(9, 'Vehicle Maintenance', 99999999.99, '2024-11-30', '11', '', '../uploadswebcam_6749264fdda383.77181087.png', '2024-11-29 02:26:46'),
(10, 'Delivery Fuel', 9.00, '2024-11-30', '7yhn', '', '../uploads/webcam_674926bc8a2a06.93143573.png', '2024-11-29 02:30:18'),
(11, 'Vehicle Maintenance', 1.00, '2024-11-30', '1', '', '../uploadswebcam_674926758a4816.34643223.png', '2024-11-29 02:30:21'),
(12, 'Vehicle Maintenance', 1500.00, '2025-03-10', 'NtingS', '', '../uploads/webcam_67cb0c3928f780.56316608.png', '2025-03-07 15:16:32');

-- --------------------------------------------------------

--
-- Table structure for table `deleted_orders`
--

CREATE TABLE `deleted_orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_truck` varchar(100) DEFAULT NULL,
  `cylinder_number` varchar(50) DEFAULT NULL,
  `gas_type` varchar(100) DEFAULT NULL,
  `capacity` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deleted_orders`
--

INSERT INTO `deleted_orders` (`id`, `customer_id`, `name`, `location`, `delivery_date`, `delivery_truck`, `cylinder_number`, `gas_type`, `capacity`, `quantity`, `price`, `status`, `deleted_at`) VALUES
(1, 29, NULL, NULL, NULL, NULL, 'Oxy-001', 'OXYGEN', '5.00', 1, 230.00, 'Paid', '2025-03-07 02:27:51'),
(2, 27, NULL, NULL, NULL, NULL, 'Oxy-001', 'OXYGEN', '5.00', 1, 230.00, 'Paid', '2025-03-07 02:42:21'),
(3, 4, NULL, NULL, NULL, NULL, 'LPG-005', 'LPG', '5.00', 1, 190.00, 'Paid', '2025-03-07 14:26:24');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `expense_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL,
  `webcam_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `expense_type`, `amount`, `date`, `notes`, `created_at`, `file_path`, `webcam_path`) VALUES
(12, 'Delivery Fuel', 1.00, '2024-11-30', '1', '2025-03-07 11:38:04', '', '../uploads/webcam_67cada9c317203.37455047.png'),
(14, 'Personal Protective Equipment (PPE)', 4000.00, '2025-03-07', 'keep', '2025-03-07 14:54:47', '../uploads/file_67cb08b7461088.18673533.png', ''),
(15, 'Delivery Fuel', 2000.00, '2025-03-07', 'gass', '2025-03-07 15:14:15', '../uploads/file_67cb0d47ad0a66.58686085.jpg', '../uploads/webcam_67cb0d47ad75b7.73438242.png'),
(16, 'Meal Allowance', 250.00, '2025-03-05', '', '2025-03-09 01:15:03', '../uploads/file_67cceb97d20dc2.25660024.jpg', ''),
(17, 'Vehicle Maintenance', 250.00, '2025-03-09', 'Tire Repair/Vulcanize tire', '2025-03-09 01:16:18', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_limits`
--

CREATE TABLE `inventory_limits` (
  `capacity` int(11) NOT NULL,
  `max_count` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_limits`
--

INSERT INTO `inventory_limits` (`capacity`, `max_count`) VALUES
(5, 10),
(10, 10),
(15, 10),
(20, 10),
(25, 10),
(30, 10),
(35, 10),
(45, 10),
(55, 10),
(60, 10),
(70, 10),
(90, 10),
(100, 10),
(110, 10),
(120, 10),
(130, 10),
(140, 10),
(150, 10);

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `account_type` varchar(255) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `username`, `ip_address`, `status`, `account_type`, `login_time`) VALUES
(1, 'Dave', '::1', 'success', 'admin', '2024-11-29 05:51:10'),
(2, 'Dave', '::1', 'success', 'admin', '2024-11-29 05:58:44'),
(57, 'employee@gmail.com', '::1', 'failed', 'employee', '2024-11-29 07:02:48'),
(58, 'Employee', '::1', 'success', 'employee', '2024-11-29 07:02:58'),
(59, 'admin@gmail.com', '::1', 'failed', 'admin', '2024-11-29 07:05:49'),
(60, 'Dave', '::1', 'success', 'admin', '2024-11-29 07:05:57'),
(61, 'employee@gmail.com', '::1', 'failed', 'employee', '2024-11-29 08:39:14'),
(62, 'employee@gmail.com', '::1', 'failed', 'employee', '2024-11-29 08:39:33'),
(63, 'employee@gmail.com', '::1', 'failed', 'employee', '2024-11-29 08:40:02'),
(64, 'Employee', '::1', 'success', 'employee', '2024-11-29 08:40:26'),
(65, 'Dave', '::1', 'success', 'admin', '2024-11-29 08:41:39'),
(66, 'Employee', '::1', 'success', 'employee', '2024-12-02 02:04:57'),
(67, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-02-11 06:19:31'),
(68, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-02-11 06:29:47'),
(69, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-02-11 06:29:55'),
(70, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:27:12'),
(71, 'dave@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:27:25'),
(72, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:27:32'),
(73, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:27:39'),
(74, 'employee@gmail.com', '::1', 'failed', 'employee', '2025-03-05 13:27:50'),
(75, 'employee@gmail.com', '::1', 'failed', 'employee', '2025-03-05 13:27:59'),
(76, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:28:08'),
(77, 'dave@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:28:51'),
(78, 'dave@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:28:58'),
(79, 'dave@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:29:08'),
(80, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:30:42'),
(81, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:30:48'),
(82, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:30:59'),
(83, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:31:07'),
(84, 'nicothing@gmail.com', '::1', 'failed', 'unknown', '2025-03-05 13:36:30'),
(85, 'NICO', '::1', 'success', 'employee', '2025-03-05 13:36:48'),
(86, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:42:15'),
(87, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:42:21'),
(88, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:42:27'),
(89, 'admin', '::1', 'failed', 'admin', '2025-03-05 13:42:43'),
(90, 'admin', '::1', 'failed', 'admin', '2025-03-05 13:42:50'),
(91, 'admin', '::1', 'failed', 'admin', '2025-03-05 13:42:57'),
(92, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:43:18'),
(93, 'admin', '::1', 'failed', 'admin', '2025-03-05 13:43:27'),
(94, 'admin', '::1', 'failed', 'admin', '2025-03-05 13:43:28'),
(95, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-05 13:43:38'),
(96, 'admin', '::1', 'success', 'admin', '2025-03-05 13:44:31'),
(97, 'admin', '::1', 'success', 'admin', '2025-03-05 14:01:18'),
(98, 'admin@gmail.com', '::1', 'failed', 'admin', '2025-03-08 04:19:00'),
(99, 'nicothing@gmail.com', '::1', 'failed', 'unknown', '2025-03-08 04:27:04'),
(100, 'NICO', '::1', 'success', 'employee', '2025-03-08 04:27:13'),
(101, 'NICO', '::1', 'success', 'employee', '2025-03-08 04:51:34'),
(102, 'employee', '::1', 'success', 'employee', '2025-03-08 04:53:13'),
(103, 'Employee', '::1', 'success', 'employee', '2025-03-08 04:54:30'),
(104, 'Employee', '::1', 'success', 'employee', '2025-03-08 04:59:53'),
(105, 'Employee', '::1', 'failed', 'employee', '2025-03-08 05:00:22'),
(106, 'Employee', '::1', 'success', 'employee', '2025-03-08 05:00:30'),
(107, 'admin', '::1', 'failed', 'admin', '2025-03-08 05:50:51'),
(108, 'admin', '::1', 'failed', 'admin', '2025-03-08 05:51:19'),
(109, 'admin', '::1', 'failed', 'admin', '2025-03-08 05:51:28'),
(110, 'admin', '::1', 'failed', 'admin', '2025-03-08 05:51:39'),
(111, 'Admin', '::1', 'success', 'admin', '2025-03-08 05:52:47'),
(112, 'Admin', '::1', 'success', 'admin', '2025-03-08 06:18:40'),
(113, 'Admin', '::1', 'success', 'admin', '2025-03-08 06:19:31'),
(114, 'Admin', '::1', 'success', 'admin', '2025-03-08 06:19:47'),
(115, 'Admin', '::1', 'success', 'admin', '2025-03-08 06:20:09'),
(116, 'Admin', '::1', 'success', 'admin', '2025-03-08 06:21:41'),
(117, 'Admin', '::1', 'success', 'admin', '2025-03-08 06:26:13'),
(118, 'Admin', '::1', 'success', 'admin', '2025-03-08 06:28:50'),
(119, 'Employee', '::1', 'failed', 'employee', '2025-03-08 06:53:59'),
(120, 'Employee', '::1', 'success', 'employee', '2025-03-08 06:54:10'),
(121, 'employee@gmail.com', '::1', 'failed', 'unknown', '2025-03-08 07:14:22'),
(122, 'Employee', '::1', 'failed', 'employee', '2025-03-08 07:14:33'),
(123, 'Employee', '::1', 'success', 'employee', '2025-03-08 07:14:45'),
(124, 'Admin', '::1', 'success', 'admin', '2025-03-08 07:14:55'),
(125, 'NICO', '::1', 'failed', 'unknown', '2025-03-09 01:08:18'),
(126, 'Employee', '::1', 'success', 'employee', '2025-03-09 01:11:10'),
(127, 'Admin', '::1', 'success', 'admin', '2025-03-09 01:11:20');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `cylinder_number` varchar(50) DEFAULT NULL,
  `gas_type` varchar(50) NOT NULL,
  `capacity` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Unpaid','Partial','Paid') DEFAULT 'Unpaid',
  `ordered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_status` enum('Done','Undone') DEFAULT 'Undone',
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `cylinder_number`, `gas_type`, `capacity`, `quantity`, `price`, `status`, `ordered_at`, `delivery_status`, `completed_at`) VALUES
(7, 7, 'LPG-007', 'LPG', 5.00, 1, 190.00, 'Unpaid', '2025-03-05 21:41:44', 'Done', '2025-03-08 15:01:28'),
(8, 10, 'ARG-001', 'ARGON', 5.00, 1, 150.00, 'Paid', '2025-03-05 22:33:49', 'Done', '2025-03-08 14:43:29'),
(9, 10, 'ARG-002', 'ARGON', 5.00, 1, 150.00, 'Paid', '2025-03-05 22:33:49', 'Done', '2025-03-08 14:43:29'),
(10, 13, 'ARG-004', 'ARGON', 10.00, 1, 250.00, 'Paid', '2025-03-05 22:51:06', 'Undone', NULL),
(12, 18, 'LPG-008', 'LPG', 5.00, 1, 190.00, 'Paid', '2025-03-05 23:52:12', 'Undone', NULL),
(13, 21, 'ARG-004', 'Argon', 10.00, 2, 250.00, 'Paid', '2025-03-06 02:38:40', 'Undone', NULL),
(21, 25, 'Oxy-001', 'OXYGEN', 5.00, 2, 230.00, 'Paid', '2025-03-06 18:44:47', 'Undone', NULL),
(27, 31, 'ARG-003', 'ARGON', 5.00, 1, 150.00, 'Paid', '2025-03-07 11:12:32', 'Undone', NULL),
(28, 31, 'LPG-008', 'LPG', 5.00, 1, 190.00, 'Paid', '2025-03-07 11:12:32', 'Undone', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `change_given` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `customer_id`, `amount_paid`, `total_amount`, `change_given`, `payment_date`) VALUES
(1, 1, 200.00, 190.00, 10.00, '2024-11-28 22:32:04'),
(2, 3, 300.00, 290.00, 10.00, '2024-11-29 15:03:43'),
(3, 4, 190.00, 190.00, 0.00, '2024-11-29 16:20:43'),
(4, 5, 190.00, 190.00, 0.00, '2024-11-29 16:22:17'),
(5, 6, 3000.00, 2000.00, 1000.00, '2024-11-29 16:45:34'),
(6, 7, 190.00, 190.00, 0.00, '2025-03-06 06:15:49'),
(7, 10, 300.00, 300.00, 0.00, '2025-03-06 08:01:23'),
(8, 18, 190.00, 190.00, 0.00, '2025-03-06 08:06:13'),
(10, 13, 500.00, 500.00, 0.00, '2025-03-06 08:10:46'),
(11, 21, 500.00, 500.00, 0.00, '2025-03-07 02:39:47'),
(12, 25, 460.00, 460.00, 0.00, '2025-03-07 02:46:01'),
(13, 26, 150.00, 150.00, 0.00, '2025-03-07 02:47:51'),
(18, 27, 230.00, 230.00, 0.00, '2025-03-07 03:40:51'),
(19, 29, 230.00, 230.00, 0.00, '2025-03-07 03:43:36'),
(20, 31, 340.00, 340.00, 0.00, '2025-03-07 19:36:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `completed_cylinders`
--
ALTER TABLE `completed_cylinders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cylinders`
--
ALTER TABLE `cylinders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deleted_cylinders`
--
ALTER TABLE `deleted_cylinders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deleted_expenses`
--
ALTER TABLE `deleted_expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deleted_orders`
--
ALTER TABLE `deleted_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_limits`
--
ALTER TABLE `inventory_limits`
  ADD PRIMARY KEY (`capacity`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `completed_cylinders`
--
ALTER TABLE `completed_cylinders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `cylinders`
--
ALTER TABLE `cylinders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `deleted_cylinders`
--
ALTER TABLE `deleted_cylinders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `deleted_expenses`
--
ALTER TABLE `deleted_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `deleted_orders`
--
ALTER TABLE `deleted_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
