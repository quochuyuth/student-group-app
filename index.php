<?php
// index.php
session_start(); // Bắt đầu session để quản lý đăng nhập

// Lấy đường dẫn yêu cầu từ URL
// Ví dụ: /StudentGroupApp/login sẽ cho $request = 'login'
$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$project_folder = 'StudentGroupApp'; // Tên thư mục dự án của bạn
$request = str_replace($project_folder . '/', '', $request_uri);

// Điều hướng đơn giản
switch ($request) {
    case '':
    case 'dashboard':
        // Yêu cầu controller xử lý
        // include 'app/controllers/DashboardController.php';
        // (Tạm thời) Hiển thị view
        include 'app/views/dashboard.php';
        break;

    case 'login':
        // Yêu cầu controller xử lý
        // include 'app/controllers/AuthController.php';
        // (Tạm thời) Hiển thị view
        include 'app/views/auth/login.php';
        break;

    case 'register':
        // Yêu cầu controller xử lý
        // include 'app/controllers/AuthController.php';
        // (Tạm thời) Hiển thị view
        include 'app/views/auth/register.php';
        break;
    
    // Thêm các case khác cho: logout, create-group, tasks...

    default:
        http_response_code(404);
        echo "404 - Trang không tồn tại";
        break;
}
?>
