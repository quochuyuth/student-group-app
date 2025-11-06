<?php
// config/database.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // User mặc định của XAMPP
define('DB_PASS', 'password123');     // Password mặc định của XAMPP là rỗng
define('DB_NAME', 'student_group_management'); // Tên DB bạn đã tạo ở Bước 1

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Đặt chế độ báo lỗi để dễ dàng debug
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Kết nối thành công!"; // Bỏ comment để kiểm tra
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

?>