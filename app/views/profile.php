<?php 
// app/views/profile.php (Trang CHỈNH SỬA)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Biến $user được truyền từ hàm showEditProfile() trong UserController
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chỉnh sửa Hồ sơ - <?php echo htmlspecialchars($user['username']); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/css/profile.css">
</head>

<body>
  <div class="background"></div>

  <header class="dashboard-header">
    <div class="logo">Student<span>Group</span>App</div>
    <nav>
      <a href="index.php?page=edit_profile" class="active">Hồ sơ</a> 
      <a href="index.php?page=groups">Quản Lí Nhóm</a>
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
             src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? $user['avatar_url'] ?? 'https://i.pravatar.cc/200?u=' . $user['email']); ?>"
             alt="Avatar"
             class="avatar">

        <form id="avatarForm" action="index.php?action=upload_avatar" method="POST" enctype="multipart/form-data">
          <input type="file" id="avatarInput" name="avatar" accept="image/*" hidden>
          <button type="button" class="change-avatar-btn" onclick="document.getElementById('avatarInput').click();">
            Thay đổi ảnh
          </button>
        </form>
      </div>

      <form action="index.php?action=update_profile" method="POST" class="fadeInDelay">
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
          <input type="text" id="major" name="profile_major" value="<?php echo htmlspecialchars($user['profile_major'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label for="skills">Các kỹ năng:</label>
          <textarea id="skills" name="profile_skills"><?php echo htmlspecialchars($user['profile_skills'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
          <label for="interests">Sở thích:</label>
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
          <input type="text" id="role" name="profile_role_preference" value="<?php echo htmlspecialchars($user['profile_role_preference'] ?? ''); ?>">
        </div>

        <button type="submit" class="btn-primary">💾 Lưu thay đổi</button>
      </form>
    </div>
  </main>

  <script>
  // Code JS này không đổi, nó vẫn hoạt động đúng
  const avatarInput = document.getElementById('avatarInput');
  const avatarPreview = document.getElementById('avatarPreview');
  const avatarForm = document.getElementById('avatarForm');

  avatarInput.addEventListener('change', () => {
    if (avatarInput.files && avatarInput.files[0]) {
      const reader = new FileReader();
      reader.onload = e => avatarPreview.src = e.target.result;
      reader.readAsDataURL(avatarInput.files[0]);
      setTimeout(() => avatarForm.submit(), 400); // Submit form
    }
  });
  </script>
</body>
</html>