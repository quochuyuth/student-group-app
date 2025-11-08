-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2025 at 04:36 PM
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
-- Database: `student_group_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `evaluation_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `evaluator_user_id` int(11) NOT NULL COMMENT 'Người thực hiện đánh giá',
  `evaluated_user_id` int(11) NOT NULL COMMENT 'Người bị đánh giá',
  `evaluation_period_name` varchar(255) DEFAULT NULL COMMENT 'Ví dụ: "Đánh giá giữa kỳ"',
  `total_score` decimal(5,2) NOT NULL COMMENT 'Điểm tổng cuối cùng theo trọng số',
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`evaluation_id`, `group_id`, `evaluator_user_id`, `evaluated_user_id`, `evaluation_period_name`, `total_score`, `comments`, `created_at`) VALUES
(1, 6, 3, 1, NULL, 2.50, NULL, '2025-11-06 13:10:15'),
(2, 6, 3, 1, NULL, 2.15, NULL, '2025-11-06 13:10:41'),
(3, 1, 1, 2, NULL, 1.00, NULL, '2025-11-07 04:40:06'),
(4, 3, 2, 1, NULL, 4.00, NULL, '2025-11-07 15:31:43'),
(5, 4, 2, 1, NULL, 3.40, NULL, '2025-11-07 19:33:34'),
(6, 8, 4, 1, NULL, 3.10, NULL, '2025-11-08 03:31:15'),
(7, 8, 1, 4, NULL, 2.90, NULL, '2025-11-08 03:36:01'),
(8, 8, 1, 4, NULL, 2.30, NULL, '2025-11-08 03:36:49'),
(9, 4, 2, 1, NULL, 2.20, NULL, '2025-11-08 04:11:58'),
(10, 3, 1, 2, NULL, 3.30, NULL, '2025-11-08 14:22:11'),
(11, 3, 1, 2, NULL, 4.00, NULL, '2025-11-08 14:57:57'),
(12, 3, 1, 2, NULL, 3.76, NULL, '2025-11-08 15:03:09'),
(13, 4, 2, 1, NULL, 4.00, NULL, '2025-11-08 15:05:07'),
(14, 4, 2, 1, NULL, 3.00, NULL, '2025-11-08 15:11:54'),
(15, 4, 2, 1, NULL, 1.00, NULL, '2025-11-08 15:12:02'),
(16, 4, 2, 1, NULL, 3.10, NULL, '2025-11-08 15:13:44'),
(17, 3, 1, 2, NULL, 4.00, NULL, '2025-11-08 15:15:08'),
(18, 9, 2, 1, NULL, 3.80, NULL, '2025-11-08 15:17:10');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_scores`
--

CREATE TABLE `evaluation_scores` (
  `score_id` int(11) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `criteria_id` int(11) NOT NULL COMMENT 'Đã đổi sang criteria_id',
  `score` int(11) NOT NULL COMMENT 'Thang điểm 1-4'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evaluation_scores`
--

INSERT INTO `evaluation_scores` (`score_id`, `evaluation_id`, `criteria_id`, `score`) VALUES
(3, 12, 4, 4),
(4, 12, 5, 1),
(5, 12, 6, 4),
(9, 16, 8, 4),
(10, 16, 9, 1),
(11, 17, 4, 4),
(12, 17, 5, 4),
(13, 17, 6, 4),
(14, 18, 10, 3),
(15, 18, 11, 4);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `uploaded_by_user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL COMMENT 'Kích thước file (bytes)',
  `file_type` varchar(100) NOT NULL,
  `task_id` int(11) DEFAULT NULL COMMENT 'Gắn file vào task',
  `meeting_id` int(11) DEFAULT NULL COMMENT 'Gắn file vào họp',
  `version_of_file_id` int(11) DEFAULT NULL COMMENT 'Quản lý phiên bản (trỏ đến file_id cũ)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`file_id`, `group_id`, `uploaded_by_user_id`, `file_name`, `file_path`, `file_size`, `file_type`, `task_id`, `meeting_id`, `version_of_file_id`, `created_at`) VALUES
(1, 4, 2, 'Tài Liệu Tổng Hợp Chức Năng Hệ Thống Quản Lý Nhóm Sinh Viên.docx', 'public/uploads/1762504020_tuong1_Tài Liệu Tổng Hợp Chức Năng Hệ Thống Quản Lý Nhóm Sinh Viên.docx', 15742, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', NULL, NULL, NULL, '2025-11-07 08:27:00'),
(2, 7, 1, 'Tài Liệu Tổng Hợp Chức Năng Hệ Thống Quản Lý Nhóm Sinh Viên.docx', 'public/uploads/1762513068_tuong_Tài Liệu Tổng Hợp Chức Năng Hệ Thống Quản Lý Nhóm Sinh Viên.docx', 15742, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', NULL, NULL, NULL, '2025-11-07 10:57:48'),
(3, 6, 1, 'Tài Liệu Tổng Hợp Chức Năng Hệ Thống Quản Lý Nhóm Sinh Viên.docx', 'public/uploads/1762513477_tuong_Tài Liệu Tổng Hợp Chức Năng Hệ Thống Quản Lý Nhóm Sinh Viên.docx', 15742, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', NULL, NULL, NULL, '2025-11-07 11:04:37'),
(4, 3, 2, '054205009974_Nguyễn Mạnh Tường _6.pdf', 'public/uploads/1762574748_tuong1_054205009974_Nguyễn Mạnh Tường _6.pdf', 228301, 'application/pdf', NULL, NULL, NULL, '2025-11-08 04:05:48'),
(5, 3, 2, 'Zalo 2025-10-25 23-47-01.mp4', 'public/uploads/1762574793_tuong1_Zalo 2025-10-25 23-47-01.mp4', 9757470, 'video/mp4', NULL, NULL, NULL, '2025-11-08 04:06:33'),
(6, 4, 2, 'database5.sql', 'public/uploads/1762574954_tuong1_database5.sql', 11803, 'application/octet-stream', NULL, NULL, NULL, '2025-11-08 04:09:14');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `group_description` text DEFAULT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`group_id`, `group_name`, `group_description`, `created_by_user_id`, `created_at`) VALUES
(1, 'tường', 'hahaa', 1, '2025-11-06 10:47:19'),
(2, 'tuong', '', 1, '2025-11-06 11:02:38'),
(3, 'tuong', 'sss', 1, '2025-11-06 11:02:48'),
(4, 'haha', 'nhuwcc', 2, '2025-11-06 11:16:10'),
(5, 'tường', 'tuong2', 2, '2025-11-06 12:40:59'),
(6, 'toán', 'học chăm nah', 3, '2025-11-06 12:45:35'),
(7, 'hhh', 'hhhh', 1, '2025-11-07 10:51:56'),
(8, 'dat', 'dat', 4, '2025-11-08 03:19:23'),
(9, 'xem điểm ', 'học tốt', 2, '2025-11-08 15:15:46');

-- --------------------------------------------------------

--
-- Table structure for table `group_invitations`
--

CREATE TABLE `group_invitations` (
  `invitation_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `invited_by_user_id` int(11) NOT NULL,
  `invited_email` varchar(255) DEFAULT NULL,
  `invited_user_id` int(11) DEFAULT NULL COMMENT 'Dùng nếu mời bằng Tên người dùng',
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `member_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('admin','member') NOT NULL DEFAULT 'member' COMMENT 'Trưởng nhóm / Thành viên',
  `join_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`member_id`, `group_id`, `user_id`, `role`, `join_date`) VALUES
(1, 1, 1, 'admin', '2025-11-06 10:47:19'),
(2, 2, 1, 'admin', '2025-11-06 11:02:38'),
(3, 3, 1, 'admin', '2025-11-06 11:02:48'),
(4, 4, 2, 'admin', '2025-11-06 11:16:10'),
(5, 4, 1, 'member', '2025-11-06 11:20:42'),
(6, 3, 2, 'member', '2025-11-06 12:17:37'),
(7, 1, 2, 'member', '2025-11-06 12:17:39'),
(8, 5, 2, 'admin', '2025-11-06 12:40:59'),
(9, 6, 3, 'admin', '2025-11-06 12:45:35'),
(10, 6, 1, 'member', '2025-11-06 12:46:18'),
(11, 7, 1, 'admin', '2025-11-07 10:51:56'),
(12, 8, 4, 'admin', '2025-11-08 03:19:23'),
(13, 8, 1, 'member', '2025-11-08 03:20:39'),
(14, 9, 2, 'admin', '2025-11-08 15:15:46'),
(15, 9, 1, 'member', '2025-11-08 15:16:12');

-- --------------------------------------------------------

--
-- Table structure for table `group_moods`
--

CREATE TABLE `group_moods` (
  `mood_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mood_rating` int(11) NOT NULL COMMENT 'Ví dụ: 1 (rất tệ) - 5 (rất tốt)',
  `log_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_rubric_criteria`
--

CREATE TABLE `group_rubric_criteria` (
  `criteria_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `criteria_name` varchar(255) NOT NULL COMMENT 'Tên tiêu chí (Admin tự nhập)',
  `criteria_weight` decimal(3,2) NOT NULL COMMENT 'Trọng số (ví dụ: 0.30 cho 30%)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `group_rubric_criteria`
--

INSERT INTO `group_rubric_criteria` (`criteria_id`, `group_id`, `criteria_name`, `criteria_weight`) VALUES
(4, 3, 'hsahjss', 0.90),
(5, 3, 'jhshsj', 0.08),
(6, 3, 'kjksd', 0.02),
(8, 4, 'h', 0.70),
(9, 4, 'k', 0.30),
(10, 9, 'hpas', 0.20),
(11, 9, 'dd', 0.80);

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `meeting_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `meeting_title` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `agenda` text DEFAULT NULL COMMENT 'Nội dung chuẩn bị họp',
  `minutes` text DEFAULT NULL COMMENT 'Biên bản họp',
  `action_items` text DEFAULT NULL COMMENT 'Các việc cần làm sau họp',
  `ai_summary` text DEFAULT NULL COMMENT 'AI tóm tắt (mô phỏng)',
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meetings`
--

INSERT INTO `meetings` (`meeting_id`, `group_id`, `meeting_title`, `start_time`, `end_time`, `agenda`, `minutes`, `action_items`, `ai_summary`, `created_by_user_id`, `created_at`) VALUES
(1, 4, '10h', '2025-11-11 18:55:00', NULL, 'họp môn toán', NULL, NULL, NULL, 1, '2025-11-06 11:55:44'),
(2, 4, 'jjj', '2025-11-12 19:29:00', NULL, 'jhbsdjaosjdasdhajsdjsdjjjjjjj', NULL, NULL, NULL, 2, '2025-11-06 12:29:05'),
(3, 7, 'ee', '2025-11-13 17:55:00', NULL, 'gggg', NULL, NULL, NULL, 1, '2025-11-07 10:55:37'),
(4, 1, 'đ', '2025-11-17 23:29:00', NULL, 'dddddddd', NULL, NULL, NULL, 2, '2025-11-07 16:29:52'),
(5, 5, '10h', '2025-11-21 01:08:00', NULL, 'JHHJ', NULL, NULL, NULL, 2, '2025-11-07 18:08:40');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_attendance`
--

CREATE TABLE `meeting_attendance` (
  `attendance_id` int(11) NOT NULL,
  `meeting_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `satisfaction_rating` int(11) DEFAULT NULL COMMENT 'Đánh giá 1-5'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meeting_attendance`
--

INSERT INTO `meeting_attendance` (`attendance_id`, `meeting_id`, `user_id`, `satisfaction_rating`) VALUES
(1, 2, 2, 4),
(6, 1, 2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` bigint(20) NOT NULL,
  `sender_user_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL COMMENT 'Nếu là chat nhóm (NULL nếu là DM)',
  `receiver_user_id` int(11) DEFAULT NULL COMMENT 'Nếu là chat cá nhân (NULL nếu là chat nhóm)',
  `message_content` text DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL COMMENT 'Gửi file đính kèm',
  `poll_id` int(11) DEFAULT NULL COMMENT 'Gửi bình chọn trong chat',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_user_id`, `group_id`, `receiver_user_id`, `message_content`, `file_id`, `poll_id`, `created_at`) VALUES
(1, 2, 4, NULL, 'hi', NULL, NULL, '2025-11-07 08:26:55'),
(2, 2, 4, NULL, NULL, 1, NULL, '2025-11-07 08:27:00'),
(3, 1, 7, NULL, 'shhssh', NULL, NULL, '2025-11-07 10:52:36'),
(4, 1, 7, NULL, NULL, 2, NULL, '2025-11-07 10:57:48'),
(5, 1, 6, NULL, NULL, 3, NULL, '2025-11-07 11:04:37'),
(6, 4, 8, NULL, 'sdfghj', NULL, NULL, '2025-11-08 03:27:10'),
(7, 2, 3, NULL, NULL, 4, NULL, '2025-11-08 04:05:48'),
(8, 2, 3, NULL, NULL, 5, NULL, '2025-11-08 04:06:33'),
(9, 2, 4, NULL, NULL, 6, NULL, '2025-11-08 04:09:14');

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `poll_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `poll_question` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`poll_id`, `group_id`, `created_by_user_id`, `poll_question`, `created_at`) VALUES
(1, 6, 1, 'hsggs', '2025-11-07 11:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `poll_options`
--

CREATE TABLE `poll_options` (
  `option_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `poll_options`
--

INSERT INTO `poll_options` (`option_id`, `poll_id`, `option_text`) VALUES
(1, 1, '1'),
(2, 1, '2'),
(3, 1, '7');

-- --------------------------------------------------------

--
-- Table structure for table `poll_votes`
--

CREATE TABLE `poll_votes` (
  `vote_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `poll_votes`
--

INSERT INTO `poll_votes` (`vote_id`, `poll_id`, `option_id`, `user_id`) VALUES
(1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `rubric_feedback`
--

CREATE TABLE `rubric_feedback` (
  `feedback_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'User gửi phản hồi',
  `feedback_content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rubric_feedback`
--

INSERT INTO `rubric_feedback` (`feedback_id`, `group_id`, `user_id`, `feedback_content`, `created_at`) VALUES
(1, 1, 2, 'jdjkdjjjjjjjjjjj\r\n', '2025-11-08 14:20:41'),
(3, 3, 2, 'hjssssssssssssssss', '2025-11-08 14:22:56'),
(4, 4, 1, 'hjsshsshshkjsadjasdasndasdhas', '2025-11-08 15:10:50');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `task_title` varchar(255) NOT NULL,
  `task_description` text DEFAULT NULL,
  `status` enum('backlog','in_progress','review','done') NOT NULL DEFAULT 'backlog',
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `created_by_user_id` int(11) NOT NULL,
  `assigned_to_user_id` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT 0 COMMENT 'Điểm đóng góp khi task hoàn thành',
  `due_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL COMMENT 'Thời gian task được chuyển sang Done'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `group_id`, `task_title`, `task_description`, `status`, `priority`, `created_by_user_id`, `assigned_to_user_id`, `points`, `due_date`, `created_at`, `completed_at`) VALUES
(1, 4, 'toán', 'khó', 'done', 'high', 1, 1, 10, '2025-11-21 00:00:00', '2025-11-06 11:27:17', NULL),
(2, 4, 'văn', 'tường', 'done', 'low', 1, 2, 10, '2025-11-21 00:00:00', '2025-11-06 11:31:46', NULL),
(3, 4, 'hóa', 'hhdhd', 'review', 'critical', 1, NULL, 0, NULL, '2025-11-06 12:03:36', NULL),
(4, 6, 'toán', 'hhhhhhhhhh', 'done', 'high', 1, 3, 10, '2025-11-09 00:00:00', '2025-11-06 12:47:26', NULL),
(5, 1, 'toán', 'sss', 'backlog', 'low', 1, 2, 4, '2025-11-27 00:00:00', '2025-11-07 07:54:57', NULL),
(6, 7, 'hjhhhd', 'sss', 'done', 'high', 1, 1, 10, '2025-11-18 00:00:00', '2025-11-07 10:52:29', NULL),
(7, 5, 'môn', 'kkkkk', 'done', 'critical', 2, 2, 10, '2025-11-16 00:00:00', '2025-11-07 15:18:07', NULL),
(8, 5, 'toán', 'haha', 'done', 'critical', 2, 2, 10, '2025-11-17 00:00:00', '2025-11-07 15:22:27', NULL),
(9, 3, 'jfjjfdkf', 'sàljas', 'done', 'critical', 2, 2, 10, '2025-11-19 00:00:00', '2025-11-07 15:31:27', NULL),
(10, 3, 'jhakasd', 'djasdkaksd', 'done', 'critical', 2, 1, 9, '2025-11-18 00:00:00', '2025-11-07 15:59:08', NULL),
(11, 3, 'kmlkas', 'ưqlmkeqw', 'review', 'low', 2, 1, 8, '2025-11-19 00:00:00', '2025-11-07 16:00:27', NULL),
(12, 4, 'N', 'HH', 'review', 'medium', 2, 1, 7, '2025-11-26 00:00:00', '2025-11-07 19:05:10', NULL),
(13, 8, 'văn', '', 'in_progress', 'medium', 4, 1, 10, '2025-11-10 00:00:00', '2025-11-08 03:22:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `task_comments`
--

CREATE TABLE `task_comments` (
  `comment_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_comments`
--

INSERT INTO `task_comments` (`comment_id`, `task_id`, `user_id`, `comment_text`, `created_at`) VALUES
(1, 4, 3, 'hhhhs', '2025-11-06 12:53:36');

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
(4, 'dat123', 'dat123@gmail.com', '$2y$10$9DA8U.n7IQxGhKutwyA7BuOXbrvPEFs26GyOW.Yu9GnndAt3V1hT.', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 03:18:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`evaluation_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `evaluator_user_id` (`evaluator_user_id`),
  ADD KEY `evaluated_user_id` (`evaluated_user_id`);

--
-- Indexes for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  ADD PRIMARY KEY (`score_id`),
  ADD UNIQUE KEY `eval_criteria_unique` (`evaluation_id`,`criteria_id`),
  ADD KEY `idx_evaluation_id` (`evaluation_id`),
  ADD KEY `criteria_id` (`criteria_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `uploaded_by_user_id` (`uploaded_by_user_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `meeting_id` (`meeting_id`),
  ADD KEY `version_of_file_id` (`version_of_file_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`);

--
-- Indexes for table `group_invitations`
--
ALTER TABLE `group_invitations`
  ADD PRIMARY KEY (`invitation_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `invited_by_user_id` (`invited_by_user_id`),
  ADD KEY `invited_user_id` (`invited_user_id`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `user_group_unique` (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_moods`
--
ALTER TABLE `group_moods`
  ADD PRIMARY KEY (`mood_id`),
  ADD UNIQUE KEY `group_user_date_unique` (`group_id`,`user_id`,`log_date`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_rubric_criteria`
--
ALTER TABLE `group_rubric_criteria`
  ADD PRIMARY KEY (`criteria_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`meeting_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`);

--
-- Indexes for table `meeting_attendance`
--
ALTER TABLE `meeting_attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `meeting_user_unique` (`meeting_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_user_id` (`sender_user_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `receiver_user_id` (`receiver_user_id`),
  ADD KEY `file_id` (`file_id`),
  ADD KEY `poll_id` (`poll_id`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`poll_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`);

--
-- Indexes for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `poll_id` (`poll_id`);

--
-- Indexes for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD PRIMARY KEY (`vote_id`),
  ADD UNIQUE KEY `poll_user_unique` (`poll_id`,`user_id`),
  ADD KEY `option_id` (`option_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rubric_feedback`
--
ALTER TABLE `rubric_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD UNIQUE KEY `group_user_unique` (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`),
  ADD KEY `assigned_to_user_id` (`assigned_to_user_id`);

--
-- Indexes for table `task_comments`
--
ALTER TABLE `task_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `evaluation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `group_invitations`
--
ALTER TABLE `group_invitations`
  MODIFY `invitation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `group_moods`
--
ALTER TABLE `group_moods`
  MODIFY `mood_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_rubric_criteria`
--
ALTER TABLE `group_rubric_criteria`
  MODIFY `criteria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `meeting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `meeting_attendance`
--
ALTER TABLE `meeting_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `poll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `poll_options`
--
ALTER TABLE `poll_options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `poll_votes`
--
ALTER TABLE `poll_votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rubric_feedback`
--
ALTER TABLE `rubric_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `task_comments`
--
ALTER TABLE `task_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`evaluator_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_ibfk_3` FOREIGN KEY (`evaluated_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  ADD CONSTRAINT `evaluation_scores_ibfk_1` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluations` (`evaluation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_scores_ibfk_2` FOREIGN KEY (`criteria_id`) REFERENCES `group_rubric_criteria` (`criteria_id`) ON DELETE CASCADE;

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`uploaded_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `files_ibfk_4` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`meeting_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `files_ibfk_5` FOREIGN KEY (`version_of_file_id`) REFERENCES `files` (`file_id`) ON DELETE SET NULL;

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `group_invitations`
--
ALTER TABLE `group_invitations`
  ADD CONSTRAINT `group_invitations_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_invitations_ibfk_2` FOREIGN KEY (`invited_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_invitations_ibfk_3` FOREIGN KEY (`invited_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `group_moods`
--
ALTER TABLE `group_moods`
  ADD CONSTRAINT `group_moods_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_moods_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `group_rubric_criteria`
--
ALTER TABLE `group_rubric_criteria`
  ADD CONSTRAINT `group_rubric_criteria_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE;

--
-- Constraints for table `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `meetings_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meetings_ibfk_2` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_attendance`
--
ALTER TABLE `meeting_attendance`
  ADD CONSTRAINT `meeting_attendance_ibfk_1` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`meeting_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_attendance_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`receiver_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_4` FOREIGN KEY (`file_id`) REFERENCES `files` (`file_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `messages_ibfk_5` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE SET NULL;

--
-- Constraints for table `polls`
--
ALTER TABLE `polls`
  ADD CONSTRAINT `polls_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `polls_ibfk_2` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD CONSTRAINT `poll_options_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE;

--
-- Constraints for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD CONSTRAINT `poll_votes_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poll_votes_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `poll_options` (`option_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poll_votes_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `rubric_feedback`
--
ALTER TABLE `rubric_feedback`
  ADD CONSTRAINT `rubric_feedback_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rubric_feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `task_comments`
--
ALTER TABLE `task_comments`
  ADD CONSTRAINT `task_comments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
