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
     * Hiển thị trang hồ sơ
     */
    public function showProfile() {
        // 1. Bảo vệ trang: Đảm bảo người dùng đã đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        // 2. Lấy user_id từ session
        $user_id = $_SESSION['user_id'];

        // 3. Gọi Model để lấy thông tin người dùng
        $user = $this->userModel->findById($user_id);

        // 4. Tải file View và truyền dữ liệu $user vào
        require 'app/views/profile.php';
    }

    /**
     * Xử lý cập nhật hồ sơ
     */
    public function updateProfile() {
        // 1. Bảo vệ: Đảm bảo đã đăng nhập và đây là yêu cầu POST
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        // 2. Lấy user_id từ session
        $user_id = $_SESSION['user_id'];

        // 3. Lấy dữ liệu từ form (gói gọn trong mảng $_POST)
        $data = $_POST;

        // 4. Gọi Model để cập nhật
        if ($this->userModel->updateProfile($user_id, $data)) {
            // Cập nhật thành công, đặt thông báo và tải lại
            $_SESSION['flash_message'] = "Cập nhật hồ sơ thành công!";
        } else {
            // Cập nhật thất bại
            $_SESSION['flash_message'] = "Cập nhật hồ sơ thất bại. Vui lòng thử lại.";
        }

        // 5. Quay lại trang hồ sơ
        header('Location: index.php?page=profile');
        exit;
    }
}
?>