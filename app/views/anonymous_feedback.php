<?php
// app/views/anonymous_feedback.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
$group_id = $_GET['group_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phản hồi ẩn danh</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/group_details.css">
</head>
<body>
    <div class="bg-overlay"></div>

    <header class="topbar">
        <div class="topbar-left">
            <h1 class="group-title">Phản hồi ẩn danh</h1>
            <p class="group-sub">Chức năng đang phát triển</p>
        </div>
        <nav class="topnav">
            <a href="index.php?page=dashboard">🏠 Trang chủ</a>
            <a href="index.php?page=profile">👤 Hồ sơ</a>
            <?php if ($group_id): ?>
                <a href="index.php?page=group_details&id=<?php echo $group_id; ?>">⬅️ Quay lại nhóm</a>
            <?php endif; ?>
            <a href="index.php?action=logout" class="logout">🚪 Đăng xuất</a>
        </nav>
    </header>

    <main class="container" style="text-align: center; padding-top: 50px;">
        <div class="card" style="max-width: 600px; margin: auto; padding: 40px;">
            <h2>🚧 Chức năng này chúng tôi đang phát triển 🚧</h2>
            <p style="font-size: 1.1rem; margin-top: 15px;">
                Hẹn bạn vào bản cập nhật tiếp theo!
            </p>
            <br>
            <?php if ($group_id): ?>
                <a href="index.php?page=group_details&id=<?php echo $group_id; ?>" class="btn">Quay lại nhóm</a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>