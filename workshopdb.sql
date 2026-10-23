-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 23, 2026 at 03:50 PM
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
-- Database: `workshopdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `first_name`, `last_name`, `email`, `message`, `submitted_at`) VALUES
(1, 'John', 'Cena', 'john@gmail.com', 'I like to play', '2026-08-19 08:56:00'),
(2, 'John', 'Doe', 'john@gmail.com', 'I like to play', '2026-08-19 09:55:25');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `order_id` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `cart_data` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`order_id`, `user_id`, `customer_name`, `customer_phone`, `address`, `payment_method`, `cart_data`, `total_price`, `created_at`) VALUES
('ORD-05691BD1', 5, 'David Martinez', '012-3456789', 'Night City', 'Credit/Debit Card', 'Car Player:1, Exhaust Pipe:2', 3248.30, '2026-10-23 13:15:46'),
('ORD-19E2326C', 1, 'Ali Papa', '0122662743', 'India', 'E-Wallet', 'Exhaust Pipe:1', 679.20, '2026-08-18 08:09:56'),
('ORD-24516610', 3, 'Kuma Tekken', '21573256326237', 'JP', 'Online Banking', 'Car Player:1', 1889.90, '2026-08-18 16:40:17'),
('ORD-72AD2EDA', 1, 'Ali Papa', '0122662743', 'India', 'Credit/Debit Card', 'Car Player:3, LED Headlights:2', 5862.90, '2026-08-18 08:09:01'),
('ORD-8F2A3146', 1, 'Ali Papa', '0122662743', 'KL', 'E-Wallet', 'Exhaust Pipe:1', 679.20, '2026-08-18 16:32:26'),
('ORD-AD407AD3', 4, 'John Doe', '8325732325385', 'KL', 'E-Wallet', 'Car Player:1, LED Headlights:6', 2469.50, '2026-08-18 17:54:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(4) NOT NULL,
  `username` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `updated_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Ali', 'ali@email.com', '12345678', '2026-08-17 04:52:25.833807', '2026-08-18 16:22:12.238873'),
(2, 'alex', 'alex@gmail.com', '1234567890-', '2026-08-17 05:49:11.141207', '2026-08-17 05:49:11.141207'),
(3, 'kuma', 'kuma@gmail.com', 'cisco123', '2026-08-18 05:49:59.873893', '2026-08-18 05:49:59.873893'),
(4, 'John', 'john@gmail.com', '1234567890', '2026-08-18 17:52:30.157462', '2026-08-18 17:57:53.950927'),
(5, 'David', 'david@gmail.com', '123456', '2026-10-23 13:13:43.876821', '2026-10-23 13:13:43.876821');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
