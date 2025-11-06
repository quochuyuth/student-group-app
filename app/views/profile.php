<?php
// app/views/profile.php

// Bảo vệ trang (dù controller đã làm, nhưng cẩn thận vẫn hơn)
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Biến $user được truyền từ UserController
// (Chúng ta thêm thẻ <pre> để xem dữ liệu, bạn có thể xóa sau)
// echo "<pre>";
// print_r($user);
// echo "</pre>";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ của bạn - <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <h1>Hồ sơ cá nhân</h1>
        <nav>
            <a href="index.php?page=dashboard">Quay lại Bảng tin</a>
            <a href="index.php?action=logout">Đăng Xuất</a>
        </nav>
    </header>

    <main class="profile-container">
        
        <?php
        // Hiển thị thông báo (flash message)
        if (isset($_SESSION['flash_message'])) {
            echo '<div class="flash-message">' . $_SESSION['flash_message'] . '</div>';
            unset($_SESSION['flash_message']);
        }
        ?>

        <form action="index.php?action=update_profile" method="POST">
            <h3>Thông tin cơ bản</h3>
            <div class="form-group">
                <label>Tên người dùng:</label>
                <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>

            <hr>

            <h3>Thông tin hồ sơ</h3>
            <p>Hãy chia sẻ về bạn để đồng đội dễ dàng tìm thấy!</p>

            <div class="form-group">
                <label for="major">Ngành học:</label>
                <input type="text" id="major" name="profile_major" 
                       value="<?php echo htmlspecialchars($user['profile_major'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="skills">Các kỹ năng (cách nhau bởi dấu phẩy):</label>
                <textarea id="skills" name="profile_skills"><?php echo htmlspecialchars($user['profile_skills'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="interests">Sở thích (cách nhau bởi dấu phẩy):</label>
                <textarea id="interests" name="profile_interests"><?php echo htmlspecialchars($user['profile_interests'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="strengths">Điểm mạnh:</label>
                <textarea id="strengths" name="profile_strengths"><?php echo htmlspecialchars($user['profile_strengths'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="weaknesses">Điểm yếu:</label>
                <textarea id="weaknesses" name="profile_weaknesses"><?php echo htmlspecialchars($user['profile_weaknesses'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="role">Vai trò mong muốn trong nhóm:</label>
                <input type="text" id="role" name="profile_role_preference" 
                       value="<?php echo htmlspecialchars($user['profile_role_preference'] ?? ''); ?>">
            </div>

            <button type="submit" class="btn">Lưu thay đổi</button>
        </form>
    </main>
</body>
</html>