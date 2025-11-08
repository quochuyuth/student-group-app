-- --------------------------------------------------------
-- Database: `student_collab_db`
-- --------------------------------------------------------

SET NAMES utf8mb4;
SET time_zone = '+07:00';

--
-- Cấu trúc bảng cho `users` (Chức năng 1)
--
CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `profile_major` VARCHAR(255) NULL,
  `profile_skills` TEXT NULL,
  `profile_interests` TEXT NULL,
  `profile_strengths` TEXT NULL,
  `profile_weaknesses` TEXT NULL,
  `profile_role_preference` VARCHAR(255) NULL COMMENT 'Vai trò mong muốn',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `groups` (Chức năng 2)
--
CREATE TABLE `groups` (
  `group_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_name` VARCHAR(255) NOT NULL,
  `group_description` TEXT NULL,
  `created_by_user_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `group_members` (Chức năng 2: Ai ở nhóm nào)
--
CREATE TABLE `group_members` (
  `member_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `role` ENUM('admin', 'member') NOT NULL DEFAULT 'member' COMMENT 'Trưởng nhóm / Thành viên',
  `join_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `user_group_unique` (`group_id`, `user_id`),
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `group_invitations` (Chức năng 2: Mời và chấp nhận)
--
CREATE TABLE `group_invitations` (
  `invitation_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `invited_by_user_id` INT NOT NULL,
  `invited_email` VARCHAR(255) NULL,
  `invited_user_id` INT NULL COMMENT 'Dùng nếu mời bằng Tên người dùng',
  `status` ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`invited_by_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`invited_user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `tasks` (Chức năng 3: Kanban)
--
CREATE TABLE `tasks` (
  `task_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `task_title` VARCHAR(255) NOT NULL,
  `task_description` TEXT NULL,
  `status` ENUM('backlog', 'in_progress', 'review', 'done') NOT NULL DEFAULT 'backlog',
  `priority` ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
  `created_by_user_id` INT NOT NULL,
  `assigned_to_user_id` INT NULL,
  `points` INT DEFAULT 0 COMMENT 'Điểm đóng góp khi task hoàn thành',
  `due_date` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `completed_at` DATETIME NULL COMMENT 'Thời gian task được chuyển sang Done',
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `evaluations` (Chức năng 4: Lưu lịch sử đánh giá)
--
CREATE TABLE `evaluations` (
  `evaluation_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `evaluator_user_id` INT NOT NULL COMMENT 'Người thực hiện đánh giá',
  `evaluated_user_id` INT NOT NULL COMMENT 'Người bị đánh giá',
  `evaluation_period_name` VARCHAR(255) NULL COMMENT 'Ví dụ: "Đánh giá giữa kỳ"',
  `total_score` DECIMAL(5, 2) NOT NULL COMMENT 'Điểm tổng cuối cùng theo trọng số',
  `comments` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`evaluator_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`evaluated_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `evaluation_scores` (Chức năng 4: Chi tiết điểm Rubric)
--
CREATE TABLE `evaluation_scores` (
  `score_id` INT AUTO_INCREMENT PRIMARY KEY,
  `evaluation_id` INT NOT NULL,
  `criteria` ENUM('completion', 'deadline', 'quality', 'communication', 'initiative') NOT NULL COMMENT 'Hoàn thành, Deadline, Chất lượng, Giao tiếp, Chủ động',
  `score` INT NOT NULL COMMENT 'Thang điểm 1-4',
  `weight` DECIMAL(3, 2) NOT NULL COMMENT 'Trọng số của tiêu chí (ví dụ: 0.2 cho 20%)',
  UNIQUE KEY `eval_criteria_unique` (`evaluation_id`, `criteria`),
  FOREIGN KEY (`evaluation_id`) REFERENCES `evaluations`(`evaluation_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `meetings` (Chức năng 5)
--
CREATE TABLE `meetings` (
  `meeting_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `meeting_title` VARCHAR(255) NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NULL,
  `agenda` TEXT NULL COMMENT 'Nội dung chuẩn bị họp',
  `minutes` TEXT NULL COMMENT 'Biên bản họp',
  `action_items` TEXT NULL COMMENT 'Các việc cần làm sau họp',
  `ai_summary` TEXT NULL COMMENT 'AI tóm tắt (mô phỏng)',
  `created_by_user_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `meeting_attendance` (Chức năng 5: Đánh giá hài lòng)
--
CREATE TABLE `meeting_attendance` (
  `attendance_id` INT AUTO_INCREMENT PRIMARY KEY,
  `meeting_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `satisfaction_rating` INT NULL COMMENT 'Đánh giá 1-5',
  UNIQUE KEY `meeting_user_unique` (`meeting_id`, `user_id`),
  FOREIGN KEY (`meeting_id`) REFERENCES `meetings`(`meeting_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `files` (Chức năng 7)
--
CREATE TABLE `files` (
  `file_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `uploaded_by_user_id` INT NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_size` INT NOT NULL COMMENT 'Kích thước file (bytes)',
  `file_type` VARCHAR(100) NOT NULL,
  `task_id` INT NULL COMMENT 'Gắn file vào task',
  `meeting_id` INT NULL COMMENT 'Gắn file vào họp',
  `version_of_file_id` INT NULL COMMENT 'Quản lý phiên bản (trỏ đến file_id cũ)',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`task_id`) REFERENCES `tasks`(`task_id`) ON DELETE SET NULL,
  FOREIGN KEY (`meeting_id`) REFERENCES `meetings`(`meeting_id`) ON DELETE SET NULL,
  FOREIGN KEY (`version_of_file_id`) REFERENCES `files`(`file_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `polls` (Chức năng 6: Bình chọn)
--
CREATE TABLE `polls` (
  `poll_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `created_by_user_id` INT NOT NULL,
  `poll_question` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `poll_options` (Chức năng 6)
--
CREATE TABLE `poll_options` (
  `option_id` INT AUTO_INCREMENT PRIMARY KEY,
  `poll_id` INT NOT NULL,
  `option_text` VARCHAR(255) NOT NULL,
  FOREIGN KEY (`poll_id`) REFERENCES `polls`(`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `poll_votes` (Chức năng 6)
--
CREATE TABLE `poll_votes` (
  `vote_id` INT AUTO_INCREMENT PRIMARY KEY,
  `poll_id` INT NOT NULL,
  `option_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  UNIQUE KEY `poll_user_unique` (`poll_id`, `user_id`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls`(`poll_id`) ON DELETE CASCADE,
  FOREIGN KEY (`option_id`) REFERENCES `poll_options`(`option_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `messages` (Chức năng 6: Chat)
--
CREATE TABLE `messages` (
  `message_id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `sender_user_id` INT NOT NULL,
  `group_id` INT NULL COMMENT 'Nếu là chat nhóm (NULL nếu là DM)',
  `receiver_user_id` INT NULL COMMENT 'Nếu là chat cá nhân (NULL nếu là chat nhóm)',
  `message_content` TEXT NULL,
  `file_id` INT NULL COMMENT 'Gửi file đính kèm',
  `poll_id` INT NULL COMMENT 'Gửi bình chọn trong chat',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sender_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`receiver_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`file_id`) REFERENCES `files`(`file_id`) ON DELETE SET NULL,
  FOREIGN KEY (`poll_id`) REFERENCES `polls`(`poll_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `group_moods` (Chức năng 8: Biểu đồ cảm xúc)
--
CREATE TABLE `group_moods` (
  `mood_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `mood_rating` INT NOT NULL COMMENT 'Ví dụ: 1 (rất tệ) - 5 (rất tốt)',
  `log_date` DATE NOT NULL,
  UNIQUE KEY `group_user_date_unique` (`group_id`, `user_id`, `log_date`),
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Cấu trúc bảng cho `task_comments` (Chức năng bổ sung, rất cần thiết)
--
CREATE TABLE `task_comments` (
  `comment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `task_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `comment_text` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`task_id`) REFERENCES `tasks`(`task_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;