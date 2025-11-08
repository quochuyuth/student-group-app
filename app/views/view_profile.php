<?php 
// app/views/view_profile.php (Trang XEM)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Biến $user_profile được truyền từ hàm viewProfile() trong UserController
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hồ sơ của: <?php echo htmlspecialchars($user_profile['username']); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/css/profile.css"> 
  <link rel="stylesheet" href="public/css/view_profile.css"> 
</head>

<body>
  <div class="background"></div>

  <header class="dashboard-header">
    <div class="logo">Student<span>Group</span>App</div>
    <nav>
      <a href="index.php?page=edit_profile">Hồ sơ của tôi</a> 
      <a href="index.php?page=groups">Danh Sách Nhóm</a>
      <a href="index.php?page=dashboard">Trang Chủ</a>
      <a href="index.php?action=logout" class="btn-logout">Đăng Xuất</a>
    </nav>
  </header>

  <main class="profile-container">
    <?php if (isset($_SESSION['flash_message'])): ?>
      <div class="flash-message"><?= htmlspecialchars($_SESSION['flash_message']); ?></div>
      <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <div class="profile-card fadeIn">
      <div class="avatar-section">
        <img id="avatarPreview"
             src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? $user_profile['avatar_url'] ?? 'https://i.pravatar.cc/200?u=' . $user_profile['email']); ?>"
             alt="Avatar"
             class="avatar">
        </div>

      <div class="view-info-container fadeInDelay">
        <h3>Thông tin cơ bản</h3>
        <div class="info-group">
          <label>Tên người dùng:</label>
          <p><?php echo htmlspecialchars($user_profile['username']); ?></p>
        </div>
        <div class="info-group">
          <label>Email:</label>
          <p><?php echo htmlspecialchars($user_profile['email']); ?></p>
        </div>

        <hr>

        <h3>Thông tin hồ sơ</h3>
        
        <div class="info-group">
          <label>Ngành học:</label>
          <p><?php echo htmlspecialchars($user_profile['profile_major'] ?? 'Chưa cập nhật'); ?></p>
        </div>

        <div class="info-group">
          <label>Các kỹ năng:</label>
          <p><?php echo nl2br(htmlspecialchars($user_profile['profile_skills'] ?? 'Chưa cập nhật')); ?></p>
        </div>

        <div class="info-group">
          <label>Sở thích:</label>
          <p><?php echo nl2br(htmlspecialchars($user_profile['profile_interests'] ?? 'Chưa cập nhật')); ?></p>
        </div>

        <div class="info-group">
          <label>Điểm mạnh:</label>
          <p><?php echo nl2br(htmlspecialchars($user_profile['profile_strengths'] ?? 'Chưa cập nhật')); ?></p>
        </div>

        <div class="info-group">
          <label>Điểm yếu:</label>
          <p><?php echo nl2br(htmlspecialchars($user_profile['profile_weaknesses'] ?? 'Chưa cập nhật')); ?></p>
        </div>

        <div class="info-group">
          <label>Vai trò mong muốn trong nhóm:</label>
          <p><?php echo htmlspecialchars($user_profile['profile_role_preference'] ?? 'Chưa cập nhật'); ?></p>
        </div>

        <a href="javascript:history.back()" class="btn-secondary">⬅️ Quay lại</a>
      </div>
    </div>
  </main>
  </body>
</html>