<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - StudentGroupApp</title>
    <link rel="stylesheet" href="public/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <form action="index.php?action=register" method="POST">
            <h2>✨ Tạo tài khoản mới</h2>

            <?php
            if (isset($_SESSION['flash_message'])) {
                echo '<div class="flash-message">' . $_SESSION['flash_message'] . '</div>';
                unset($_SESSION['flash_message']);
            }
            ?>

            <div class="form-group">
                <label for="username">Tên người dùng:</label>
                <input type="text" id="username" name="username" placeholder="Nhập tên người dùng" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Nhập email" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" placeholder="Tạo mật khẩu" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
            </div>

            <button type="submit" class="btn">Đăng Ký</button>

            <p>Đã có tài khoản? <a href="index.php?page=login">Đăng nhập ngay</a></p>
        </form>
    </div>
</body>
</html>
