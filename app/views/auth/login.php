<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng Nhập - StudentGroupApp</title>
  <link rel="stylesheet" href="public/css/auth.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="auth-container">
    <form action="index.php?action=login" method="POST">
      <h2>✨ Đăng nhập hệ thống</h2>

      <?php
      if (isset($_SESSION['flash_message'])) {
          echo '<div class="flash-message">' . $_SESSION['flash_message'] . '</div>';
          unset($_SESSION['flash_message']);
      }
      ?>

      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Nhập email của bạn" required>
      </div>

      <div class="form-group">
        <label for="password">Mật khẩu:</label>
        <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
      </div>

      <button type="submit" class="btn">Đăng Nhập</button>

      <p>Chưa có tài khoản? <a href="index.php?page=register">Đăng ký ngay</a></p>
    </form>
  </div>
</body>
</html>
