<?php
// app/controllers/UserController.php

require_once 'app/models/User.php';

class UserController {
    private $db;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($this->db);
    }

    /**
     * (MỚI) Hiển thị trang XEM HỒ SƠ (Read-only)
     */
    public function viewProfile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        // 1. Lấy ID của người cần xem
        // Nếu có ?id=123 trên URL, xem người đó. Nếu không, xem chính mình.
        $user_id_to_view = $_SESSION['user_id'];
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $user_id_to_view = (int)$_GET['id'];
        }

        // 2. Gọi Model để lấy thông tin
        $user_profile = $this->userModel->findById($user_id_to_view);

        if (!$user_profile) {
            $_SESSION['flash_message'] = "Không tìm thấy người dùng này.";
            header('Location: index.php?page=dashboard');
            exit;
        }

        // 3. Tải file View mới
        require 'app/views/view_profile.php';
    }


    /**
     * (ĐỔI TÊN) Hiển thị trang CHỈNH SỬA HỒ SƠ
     */
    public function showEditProfile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        $user_id = $_SESSION['user_id'];
        $user = $this->userModel->findById($user_id);
        
        // Tải file View (trang chỉnh sửa)
        require 'app/views/profile.php';
    }

    /**
     * Xử lý cập nhật thông tin (Giữ nguyên)
     */
    public function updateProfile() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $data = $_POST; // Dữ liệu từ form

        if ($this->userModel->updateProfile($user_id, $data)) {
            $_SESSION['flash_message'] = "Cập nhật hồ sơ thành công!";
        } else {
            $_SESSION['flash_message'] = "Cập nhật hồ sơ thất bại. Vui lòng thử lại.";
        }

        // Quay lại trang CHỈNH SỬA hồ sơ
        header('Location: index.php?page=edit_profile');
        exit;
    }

    /**
     * (MỚI) Xử lý Upload Avatar
     */
    public function uploadAvatar() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_FILES['avatar'])) {
            header('Location: index.php?page=edit_profile');
            exit;
        }
        
        $uploadDir = __DIR__ . '/../../public/uploads/'; // đường dẫn thư mục thật
        $uploadUrl = 'public/uploads/'; // đường dẫn dùng hiển thị trên web

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $file = $_FILES['avatar'];
        $fileName = basename($file['name']);
        $fileTmp = $file['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($fileExt, $allowed)) {
            $newName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExt;
            $filePath = $uploadDir . $newName;
            $fileUrl = $uploadUrl . $newName;

            // (Cần hàm update avatar trong Model)
            // $this->userModel->updateAvatar($fileUrl, $_SESSION['user_id']);
            
            // Giả định là lưu vào session (theo code cũ của bạn)
            if (move_uploaded_file($fileTmp, $filePath)) {
                $_SESSION['flash_message'] = "Ảnh đại diện đã được tải lên!";
                $_SESSION['user_avatar'] = $fileUrl; // Cập nhật avatar mới vào session
            } else {
                $_SESSION['flash_message'] = "Lỗi khi tải ảnh lên!";
            }
        } else {
            $_SESSION['flash_message'] = "Vui lòng chọn file ảnh hợp lệ (jpg, png, gif, webp)!";
        }

        // Quay lại trang chỉnh sửa
        header('Location: index.php?page=edit_profile');
        exit;
    }
}
?>