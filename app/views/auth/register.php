<!-- app/views/auth/register.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Student Collab</title>
    <!-- Link CSS của bạn -->
    <link rel="stylesheet" href="/<?php echo $project_folder; ?>/public/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Đăng ký tài khoản mới</h2>
        <form action="xuly_dangky.php" method="POST"> <!-- Tạm thời, sau này sẽ do Controller xử lý -->
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
            <button type="submit">Đăng ký</button>
        </form>
        <p>Đã có tài khoản? <a href="login">Đăng nhập</a></p>
    </div>
</body>
</html>
