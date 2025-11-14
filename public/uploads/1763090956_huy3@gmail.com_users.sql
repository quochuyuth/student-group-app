-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2025 at 03:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_group_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_major` varchar(255) DEFAULT NULL,
  `profile_skills` text DEFAULT NULL,
  `profile_interests` text DEFAULT NULL,
  `profile_strengths` text DEFAULT NULL,
  `profile_weaknesses` text DEFAULT NULL,
  `profile_role_preference` varchar(255) DEFAULT NULL COMMENT 'Vai trò mong muốn',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `profile_major`, `profile_skills`, `profile_interests`, `profile_strengths`, `profile_weaknesses`, `profile_role_preference`, `created_at`) VALUES
(1, 'tuong', 'tuong@gmail.com', '$2y$10$C0cHBwP7ldMKXRQCydZx4.cCfoA3YGx6OOLVV8mIKn0OoxXTZQPY6', '', '', '', '', '', '', '2025-11-06 10:31:32'),
(2, 'tuong1', 'tuong1@gmail.com', '$2y$10$90V2XFKlTVEoAYgqNUwc/eQYyl3j/VTU2Fdhckq8UdymL1S4vJk0m', 'sss', 'xx', 'gh', 'ss', 'ssss', 'ss', '2025-11-06 11:15:42'),
(3, 'tuong3', 'tuong3@gmail.com', '$2y$10$s3IkCADWf28B.gAHr9aM.e7N5pM66V0ZBaQac3uYCs/TO0wbJ8Xbe', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-06 12:44:58'),
(4, 'dat123', 'dat123@gmail.com', '$2y$10$9DA8U.n7IQxGhKutwyA7BuOXbrvPEFs26GyOW.Yu9GnndAt3V1hT.', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 03:18:42'),
(5, 'huy@gmail.com', 'huy1@gmail.com', '$2y$10$pw8elExCF.w.hfrUvLrLquQZRt007Rv0.viiwAlMh7Y4wPF75T.Me', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 16:39:41'),
(6, 'huy2@gmail.com', 'huy2@gmail.com', '$2y$10$AyeJfihZ4OJT5.jlAAo.R./we2I4qIMyjwHJveF0KjxUA1tcEJQFy', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 16:40:49'),
(7, 'huy5@gmail.com', 'huy5@gmail.com', '$2y$10$G4waYi/rV04Rcalc9NQt/eeYcH5c4f43IbXPqpYdWtdHtb3tKsbJq', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-09 09:38:30'),
(8, 'nguyenquochuy12122019@gmail.com', 'huynq5999@ut.edu.vn', '$2y$10$MvMX1j5lK7dEdmsB3HXAFOIjqyLGI28YshpvJbnuqx3gRJRbyVhqG', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 13:02:08'),
(9, 'huy3@gmail.com', 'huy3@gmail.com', '$2y$10$4OE0UJrb0bK8kQTUkMA6PeUCxELCdsFS0vjDEpYMSW3hpZL1MLTiO', 'ok', 'ok', 'ok', 'ok', 'ok', 'ok', '2025-11-14 01:47:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
