<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - StudentGroupApp</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Đăng nhập hệ thống</h2>

        <?php
        // 1. Kiểm tra xem có thông báo (flash message) không
        if (isset($_SESSION['flash_message'])) {
            // 2. Hiển thị thông báo (ví dụ: "Đăng ký thành công!", "Sai mật khẩu")
            echo '<div class="flash-message">' . $_SESSION['flash_message'] . '</div>';
            
            // 3. Xóa thông báo sau khi đã hiển thị
            unset($_SESSION['flash_message']);
        }
        ?>

        <form action="index.php?action=login" method="POST">
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">Đăng Nhập</button>
        </form>
        
        <p>Chưa có tài khoản? <a href="index.php?page=register">Đăng ký ngay</a></p>
    </div>
</body>
</html>