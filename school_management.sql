-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 02:56 PM
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
-- Database: `school_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `ip_address`, `created_at`) VALUES
(1, 4, 'User logged out', '::1', '2025-05-09 12:17:04'),
(2, 1, 'User logged in', '::1', '2025-05-09 12:17:15'),
(3, 4, 'User logged in', '::1', '2025-05-09 12:18:53'),
(4, 1, 'User logged in', '::1', '2025-05-12 12:00:04'),
(5, 1, 'User logged out', '::1', '2025-05-12 12:01:24'),
(6, 4, 'User logged in', '::1', '2025-05-12 12:01:32'),
(7, 4, 'User logged out', '::1', '2025-05-12 12:02:57'),
(8, 3, 'User logged in', '::1', '2025-05-12 12:03:02'),
(9, 1, 'User logged in', '::1', '2025-05-12 12:05:04'),
(10, 4, 'User logged in', '::1', '2025-05-12 12:15:03'),
(11, 4, 'User logged out', '::1', '2025-05-12 13:35:38'),
(12, 1, 'User logged in', '::1', '2025-05-12 13:36:08'),
(13, 1, 'User logged out', '::1', '2025-05-12 13:36:29'),
(14, 4, 'User logged in', '::1', '2025-05-12 13:37:44'),
(15, 4, 'User logged out', '::1', '2025-05-12 13:38:36'),
(16, 3, 'User logged in', '::1', '2025-05-12 13:39:04'),
(17, 1, 'User logged in', '::1', '2025-05-14 11:50:03'),
(18, 1, 'User logged out', '::1', '2025-05-14 11:50:23'),
(19, 4, 'User logged in', '::1', '2025-05-14 11:50:28'),
(20, 4, 'User logged out', '::1', '2025-05-14 11:52:01'),
(21, 1, 'User logged in', '::1', '2025-05-14 11:52:11'),
(22, 1, 'User logged out', '::1', '2025-05-14 11:52:19'),
(23, 4, 'User logged in', '::1', '2025-05-14 11:52:28'),
(24, 4, 'User logged out', '::1', '2025-05-14 11:57:20'),
(25, 4, 'User logged in', '::1', '2025-05-14 11:57:34'),
(26, 4, 'User logged out', '::1', '2025-05-14 12:03:37'),
(27, 3, 'User logged in', '::1', '2025-05-14 12:03:44'),
(28, 1, 'User logged in', '::1', '2025-05-14 12:11:02'),
(29, 1, 'User logged out', '::1', '2025-05-14 12:11:11'),
(30, 4, 'User logged in', '::1', '2025-05-14 12:11:16'),
(31, 4, 'User logged out', '::1', '2025-05-14 12:13:47'),
(32, 1, 'User logged in', '::1', '2025-05-14 12:13:55'),
(33, 1, 'User logged out', '::1', '2025-05-14 12:14:19'),
(34, 3, 'User logged in', '::1', '2025-05-14 12:14:25'),
(35, 3, 'User logged out', '::1', '2025-05-14 12:22:52'),
(36, 4, 'User logged in', '::1', '2025-05-14 12:23:00'),
(37, 4, 'User logged out', '::1', '2025-05-14 12:33:14'),
(38, 3, 'User logged in', '::1', '2025-05-14 12:33:21'),
(39, 3, 'User logged out', '::1', '2025-05-14 12:39:29'),
(40, 1, 'User logged in', '::1', '2025-05-14 12:39:35'),
(41, 1, 'User logged out', '::1', '2025-05-14 12:40:40'),
(42, 3, 'User logged in', '::1', '2025-05-14 12:40:46'),
(43, 3, 'User logged out', '::1', '2025-05-14 12:45:50'),
(44, 1, 'User logged in', '::1', '2025-05-14 12:45:56'),
(45, 1, 'User logged out', '::1', '2025-05-14 12:46:23'),
(46, 3, 'User logged in', '::1', '2025-05-14 12:46:29'),
(47, 3, 'User logged out', '::1', '2025-05-14 13:05:12'),
(48, 1, 'User logged in', '::1', '2025-05-14 13:05:19'),
(49, 1, 'User logged out', '::1', '2025-05-14 13:05:40'),
(50, 3, 'User logged in', '::1', '2025-05-14 13:05:45'),
(51, 3, 'User logged out', '::1', '2025-05-14 13:06:18'),
(52, 1, 'User logged in', '::1', '2025-05-14 13:06:23'),
(53, 1, 'User logged out', '::1', '2025-05-14 13:06:30'),
(54, 3, 'User logged in', '::1', '2025-05-14 13:06:36'),
(55, 3, 'User logged out', '::1', '2025-05-14 13:15:03'),
(56, 4, 'User logged in', '::1', '2025-05-14 13:15:10'),
(57, 4, 'User logged out', '::1', '2025-05-14 13:15:12'),
(58, 1, 'User logged in', '::1', '2025-05-14 13:15:24'),
(59, 1, 'User logged out', '::1', '2025-05-14 13:15:28'),
(60, 1, 'User logged in', '::1', '2025-05-14 13:15:40'),
(61, 1, 'User logged out', '::1', '2025-05-14 13:15:47'),
(62, 3, 'User logged in', '::1', '2025-05-14 13:15:58'),
(63, 3, 'User logged out', '::1', '2025-05-14 13:18:18'),
(64, 1, 'User logged in', '::1', '2025-05-14 13:18:23'),
(65, 1, 'User logged out', '::1', '2025-05-14 13:19:02'),
(66, 3, 'User logged in', '::1', '2025-05-14 13:19:14'),
(67, 3, 'User logged out', '::1', '2025-05-14 13:29:23'),
(68, 3, 'User logged in', '::1', '2025-05-14 13:29:34'),
(69, 3, 'User logged out', '::1', '2025-05-14 13:33:59'),
(70, 1, 'User logged in', '::1', '2025-05-14 13:34:05'),
(71, 1, 'User logged out', '::1', '2025-05-14 13:34:13'),
(72, 4, 'User logged in', '::1', '2025-05-14 13:34:22'),
(73, 4, 'User logged out', '::1', '2025-05-14 13:38:31'),
(74, 1, 'User logged in', '::1', '2025-05-14 13:38:41'),
(75, 1, 'User logged out', '::1', '2025-05-14 13:38:54'),
(76, 3, 'User logged in', '::1', '2025-05-14 13:39:04'),
(77, 3, 'User logged out', '::1', '2025-05-14 13:42:52'),
(78, 1, 'User logged in', '::1', '2025-05-14 13:43:03'),
(79, 1, 'User logged out', '::1', '2025-05-14 13:43:20'),
(80, 3, 'User logged in', '::1', '2025-05-14 13:43:31'),
(81, 3, 'User logged out', '::1', '2025-05-14 13:44:26'),
(82, 1, 'User logged in', '::1', '2025-05-14 13:44:33'),
(83, 3, 'User logged in', '::1', '2025-05-15 02:26:43'),
(84, 3, 'User logged out', '::1', '2025-05-15 02:29:08'),
(85, 4, 'User logged in', '::1', '2025-05-15 02:37:12'),
(86, 4, 'User logged out', '::1', '2025-05-15 02:39:44'),
(87, 3, 'User logged in', '::1', '2025-05-15 02:39:51'),
(88, 3, 'User logged out', '::1', '2025-05-15 02:43:11'),
(89, 3, 'User logged in', '::1', '2025-05-15 02:44:49'),
(90, 3, 'User logged out', '::1', '2025-05-15 02:45:04'),
(93, 3, 'User logged in', '::1', '2025-05-15 02:49:01'),
(94, 3, 'User logged out', '::1', '2025-05-15 02:49:10'),
(95, 4, 'User logged in', '::1', '2025-05-15 02:49:25'),
(96, 4, 'User logged out', '::1', '2025-05-15 02:51:57'),
(97, 3, 'User logged in', '::1', '2025-05-15 02:53:21'),
(98, 1, 'User logged in', '::1', '2025-05-15 02:54:16'),
(99, 1, 'User logged out', '::1', '2025-05-15 02:55:53'),
(100, 4, 'User logged in', '::1', '2025-05-15 02:56:02'),
(101, 4, 'User logged out', '::1', '2025-05-15 02:58:59'),
(102, 1, 'User logged in', '::1', '2025-05-15 03:36:14'),
(103, 1, 'User logged out', '::1', '2025-05-15 03:59:43'),
(104, 1, 'User logged in', '::1', '2025-05-15 04:32:01'),
(105, 1, 'User logged out', '::1', '2025-05-15 04:35:36'),
(106, 1, 'User logged in', '::1', '2025-05-15 04:37:19'),
(107, 1, 'User logged out', '::1', '2025-05-15 04:49:21'),
(108, 4, 'User logged in', '::1', '2025-05-15 04:49:28'),
(109, 4, 'User logged out', '::1', '2025-05-15 04:50:12'),
(110, 3, 'User logged in', '::1', '2025-05-15 04:50:19'),
(111, 3, 'User logged out', '::1', '2025-05-15 05:07:03'),
(112, 4, 'User logged in', '::1', '2025-05-15 05:07:09'),
(113, 4, 'User logged out', '::1', '2025-05-15 05:08:00'),
(114, 4, 'User logged in', '::1', '2025-05-15 05:10:03'),
(115, 4, 'User logged out', '::1', '2025-05-15 07:26:34'),
(116, 1, 'User logged in', '::1', '2025-05-15 07:26:45'),
(117, 1, 'User logged out', '::1', '2025-05-15 07:26:56'),
(118, 3, 'User logged in', '::1', '2025-05-15 07:27:18'),
(119, 3, 'User logged out', '::1', '2025-05-15 07:34:22'),
(120, 3, 'User logged in', '::1', '2025-05-15 07:34:32'),
(121, 3, 'User logged out', '::1', '2025-05-15 07:47:27'),
(122, 4, 'User logged in', '::1', '2025-05-15 07:47:34'),
(123, 4, 'User logged out', '::1', '2025-05-15 07:47:44'),
(124, 1, 'User logged in', '::1', '2025-05-15 07:47:56'),
(125, 1, 'User logged out', '::1', '2025-05-15 07:48:00'),
(126, 1, 'User logged in', '::1', '2025-05-15 07:48:07'),
(127, 1, 'User logged out', '::1', '2025-05-15 07:48:20'),
(128, 3, 'User logged in', '::1', '2025-05-15 07:48:32'),
(129, 3, 'User logged out', '::1', '2025-05-15 07:52:16'),
(130, 4, 'User logged in', '::1', '2025-05-15 07:52:25'),
(131, 4, 'User logged out', '::1', '2025-05-15 07:52:45'),
(132, 3, 'User logged in', '::1', '2025-05-15 07:55:06'),
(133, 3, 'User logged out', '::1', '2025-05-15 07:55:18'),
(134, 1, 'User logged in', '::1', '2025-05-15 07:55:27'),
(135, 1, 'User logged out', '::1', '2025-05-15 07:55:33'),
(136, 4, 'User logged in', '::1', '2025-05-15 07:55:39'),
(137, 4, 'User logged out', '::1', '2025-05-15 07:56:37'),
(138, 1, 'User logged in', '::1', '2025-05-15 07:56:42'),
(139, 1, 'User logged out', '::1', '2025-05-15 07:56:50'),
(140, 3, 'User logged in', '::1', '2025-05-15 07:57:00'),
(141, 3, 'User logged out', '::1', '2025-05-15 07:57:11'),
(142, 4, 'User logged in', '::1', '2025-05-15 07:57:21'),
(143, 4, 'User logged out', '::1', '2025-05-15 07:58:09'),
(144, 4, 'User logged in', '::1', '2025-05-15 07:58:58'),
(145, 4, 'User logged out', '::1', '2025-05-15 08:01:23'),
(146, 1, 'User logged in', '::1', '2025-05-15 08:01:29'),
(147, 1, 'User logged out', '::1', '2025-05-15 08:01:37'),
(148, 3, 'User logged in', '::1', '2025-05-15 08:01:46'),
(149, 3, 'User logged out', '::1', '2025-05-15 08:03:19'),
(150, 1, 'User logged in', '::1', '2025-05-15 08:03:26'),
(151, 1, 'User logged out', '::1', '2025-05-15 08:03:38'),
(152, 3, 'User logged in', '::1', '2025-05-15 08:04:11'),
(153, 3, 'User logged out', '::1', '2025-05-15 08:04:45'),
(154, 1, 'User logged in', '::1', '2025-05-15 08:04:51'),
(155, 1, 'User logged out', '::1', '2025-05-15 08:05:03'),
(156, 4, 'User logged in', '::1', '2025-05-15 08:05:11'),
(157, 4, 'User logged out', '::1', '2025-05-15 08:05:37');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `user_id`, `teacher_id`, `name`, `phone`, `email`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Carl Lauro Castillo', '09938782896', 'carllauro06@gmail.com', '2025-05-07 10:29:03', '2025-05-07 10:29:03'),
(2, 1, NULL, 'Carl Cordero', '09938743567', 'carllauro07@gmail.com', '2025-05-07 10:29:32', '2025-05-07 10:29:32'),
(3, 1, 3, 'Ann', '0945556666', 'ann_ann@gmail.com', '2025-05-07 10:48:41', '2025-05-07 10:48:41'),
(4, 3, 3, 'Carl Cordero', '0912223333', 'carllauro06@gmail.com', '2025-05-07 10:57:45', '2025-05-07 10:57:45'),
(5, 1, 3, 'Lauro Castillo', '0945556666', 'carllauro08@gmail.com', '2025-05-07 10:58:10', '2025-05-07 10:58:10'),
(6, 4, NULL, 'Benelyn Andaya', '0912223333', 'benelynandaya02@gmail.com', '2025-05-14 12:32:18', '2025-05-14 12:32:18'),
(7, 1, NULL, 'Benelyn Andaya', '0912223333', 'benelynandaya02@gmail.com', '2025-05-14 12:39:54', '2025-05-14 12:39:54'),
(8, 3, 3, 'Mazariah Dela Pena', '0945556666', 'mazatot@gmail.com', '2025-05-14 12:48:54', '2025-05-14 12:48:54'),
(9, 1, NULL, 'Blessie De Guzman', '0935782564', 'blessyou@gmail.com', '2025-05-14 13:18:51', '2025-05-14 13:18:51'),
(10, 1, 3, 'Lorena Castillo', '09986864521', 'kastilyo.el23@gmail.com', '2025-05-14 13:42:39', '2025-05-14 13:42:39'),
(11, 1, 3, 'Jomarie Mina', '09938743567', 'joms@gmail.com', '2025-05-14 13:44:10', '2025-05-14 13:44:10'),
(12, 3, 3, 'Lor Castillo', '0982765231', 'kastilo.el23@gmail.com', '2025-05-15 08:03:07', '2025-05-15 08:04:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','teacher','user') NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_code` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `password_hash`, `role`, `is_active`, `email_verified`, `verification_code`, `created_at`, `updated_at`) VALUES
(1, 'carl', 'Carl Lauro Castillo', 'carllauro06@gmail.com', '$2y$10$V1TBsFzm0nwPC32Sz0A65OAK2KZGjQLWmesX8V4TovpyzWZTtDMCe', 'user', 1, 1, NULL, '2025-05-07 10:04:54', '2025-05-15 02:54:01'),
(3, 'lyro', 'Cairo Cordero', 'carllauro08@gmail.com', '$2y$10$x3sJAAVIohpNEYtYitz.7OPzIdTZ1V/83nKJjeKcq/c1fiPA5xXHi', 'teacher', 1, 1, NULL, '2025-05-07 10:10:00', '2025-05-15 02:53:56'),
(4, 'admin', 'Admin', 'carllauro07@gmail.com', '$2y$10$ciQeZRJdgsLJMatMC3gdgeDd4r7wYgulzeqo7wbNWoQkPdK.tpkrC', 'admin', 1, 1, NULL, '2025-05-07 10:17:24', '2025-05-15 02:38:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contacts_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
