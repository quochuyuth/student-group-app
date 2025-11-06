<!-- app/views/auth/login.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Student Collab</title>
    <link rel="stylesheet" href="/<?php echo $project_folder; ?>/public/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Đăng nhập</h2>
        <form action="xuly_dangnhap.php" method="POST"> <!-- Tạm thời -->
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Đăng nhập</button>
        </form>
        <p>Chưa có tài khoản? <a href="register">Đăng ký</a></p>
    </div>
</body>
</html>
