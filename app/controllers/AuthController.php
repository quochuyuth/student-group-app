<?php
// app/controllers/AuthController.php

require_once 'app/models/User.php';

class AuthController {
    private $db;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($this->db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $username = strip_tags($_POST['username']);
            $email = strip_tags($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if (empty($username) || empty($email) || empty($password)) {
                $this->redirectWithFlash('register', 'Vui lòng điền tất cả các trường.');
            }
            if ($password !== $confirm_password) {
                $this->redirectWithFlash('register', 'Mật khẩu xác nhận không khớp.');
            }
            if ($this->userModel->findByEmail($email)) {
                $this->redirectWithFlash('register', 'Email này đã được sử dụng.');
            }
            if ($this->userModel->findByUsername($username)) {
                $this->redirectWithFlash('register', 'Tên người dùng này đã tồn tại.');
            }

            if ($this->userModel->create($username, $email, $password)) {
                $this->redirectWithFlash('login', 'Đăng ký thành công! Vui lòng đăng nhập.');
            } else {
                $this->redirectWithFlash('register', 'Đã xảy ra lỗi. Vui lòng thử lại.');
            }
        } else {
            header('Location: index.php?page=register');
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = strip_tags($_POST['email']);
            $password = $_POST['password'];
            $user = $this->userModel->findByEmailAndPassword($email, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php?page=dashboard");
                exit;
            } else {
                $this->redirectWithFlash('login', 'Sai email hoặc mật khẩu. Vui lòng thử lại.');
            }
        } else {
            header('Location: index.php?page=login');
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    }

    private function redirectWithFlash($page, $message) {
        $_SESSION['flash_message'] = $message;
        header("Location: index.php?page=$page");
        exit;
    }
}
?>