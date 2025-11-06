<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - StudentGroupApp</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Tạo tài khoản mới</h2>

        <?php
        // 1. Kiểm tra xem có thông báo (flash message) không
        if (isset($_SESSION['flash_message'])) {
            // 2. Hiển thị thông báo
            echo '<div class="flash-message">' . $_SESSION['flash_message'] . '</div>';
            
            // 3. Xóa thông báo sau khi đã hiển thị
            unset($_SESSION['flash_message']);
        }
        ?>

        <form action="index.php?action=register" method="POST">
            
            <div class="form-group">
                <label for="username">Tên người dùng:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn">Đăng Ký</button>
        </form>
        
        <p>Đã có tài khoản? <a href="index.php?page=login">Đăng nhập ngay</a></p>
    </div>
</body>
</html>