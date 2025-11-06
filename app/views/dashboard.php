<?php
// app/views/dashboard.php

// KHÔNG CẦN session_start(); ở đây nữa, vì index.php đã gọi rồi.

// BẢO VỆ TRANG: Kiểm tra xem người dùng đã đăng nhập chưa
// Nếu chưa, đá về trang đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Bảng Tin</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <h1>Chào mừng, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <nav>
            <a href="index.php?page=profile">Quản lý Hồ sơ</a>
            <a href="index.php?page=groups">Quản lý Nhóm</a>
            
            <a href="index.php?action=logout">Đăng Xuất</a>
        </nav>
    </header>

    <main>
        <h2>Đây là trang Bảng tin (Dashboard)</h2>
        <p>Nơi đây sẽ hiển thị các nhóm, dự án, và công việc của bạn.</p>
    </main>
</body>
</html>