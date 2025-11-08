<?php
// app/views/dashboard.php
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
    <title>Bảng điều khiển - StudentGroupApp</title>
    <link rel="stylesheet" href="public/css/dashboard.css">
</head>
<body>
    <div class="background"></div>
    <header class="dashboard-header">
        <h1 class="logo">Student<span>Group</span>App</h1>
        <nav>
            <a href="index.php?page=profile">Hồ sơ</a>
            <a href="index.php?page=groups">Quản lí nhóm</a>
            <a href="index.php?action=logout" class="btn-logout">Đăng xuất</a>
        </nav>
    </header>

    <main class="dashboard-container">
        <section class="hero">
            <div class="hero-text">
                <h2>Xin chào, <span><?php echo htmlspecialchars($_SESSION['username']); ?></span> 👋</h2>
                <p>Khám phá không gian làm việc nhóm thông minh và năng động, giúp bạn quản lý dễ dàng và hợp tác hiệu quả hơn.</p>
                <a href="index.php?page=groups" class="btn-primary">Bắt đầu ngay</a>
            </div>
            <div class="hero-img">

            </div>
        </section>

        <section class="features">
            <div class="feature-card">
                <h3>💡 Hợp tác dễ dàng</h3>
                <p>Kết nối và chia sẻ ý tưởng với các thành viên nhóm một cách linh hoạt và nhanh chóng.</p>
            </div>
            <div class="feature-card">
                <h3>📊 Quản lý thông minh</h3>
                <p>Theo dõi tiến độ, phân chia nhiệm vụ và tối ưu hiệu suất làm việc của từng dự án.</p>
            </div>
            <div class="feature-card">
                <h3>🎨 Trải nghiệm tinh tế</h3>
                <p>Thiết kế đẹp mắt, dễ sử dụng và thân thiện với mọi người — làm việc cũng có thể rất vui.</p>
            </div>
        </section>
    </main>

    <footer>
        <p>© 2025 StudentGroupApp — Nền tảng quản lý nhóm toàn diện.</p>
    </footer>
</body>
</html>
