<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// ✅ Xử lý khi người dùng tải ảnh lên
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $uploadDir = __DIR__ . '/../../public/uploads/'; // đường dẫn thư mục thật
    $uploadUrl = 'public/uploads/'; // đường dẫn dùng hiển thị trên web

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $file = $_FILES['avatar'];
    $fileName = basename($file['name']);
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($fileExt, $allowed)) {
        $newName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExt;
        $filePath = $uploadDir . $newName;
        $fileUrl = $uploadUrl . $newName;

        if (move_uploaded_file($fileTmp, $filePath)) {
            $_SESSION['flash_message'] = "Ảnh đại diện đã được tải lên!";
            $_SESSION['user_avatar'] = $fileUrl;
        } else {
            $_SESSION['flash_message'] = "Lỗi khi tải ảnh lên!";
        }
    } else {
        $_SESSION['flash_message'] = "Vui lòng chọn file ảnh hợp lệ (jpg, png, gif, webp)!";
    }
}

// ✅ Lấy thông tin người dùng từ session (giả định bạn đã lưu $user ở đâu đó)
$user = [
    'username' => $_SESSION['username'] ?? 'Người dùng',
    'email' => $_SESSION['email'] ?? 'user@example.com',
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hồ sơ cá nhân - <?php echo htmlspecialchars($user['username']); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/css/profile.css">
</head>

<body>
  <div class="background"></div>

  <header class="dashboard-header">
    <div class="logo">Student<span>Group</span>App</div>
    <nav>
      <a href="index.php?page=profile" class="active">Hồ sơ</a>
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
             src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? 'https://i.pravatar.cc/200?u=' . $user['email']); ?>"
             alt="Avatar"
             class="avatar">

        <!-- ✅ Form upload ảnh -->
        <form id="avatarForm" method="POST" enctype="multipart/form-data">
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
          <input type="text" id="major" name="profile_major" value="">
        </div>

        <div class="form-group">
          <label for="skills">Các kỹ năng:</label>
          <textarea id="skills" name="profile_skills"></textarea>
        </div>

        <div class="form-group">
          <label for="interests">Sở thích:</label>
          <textarea id="interests" name="profile_interests"></textarea>
        </div>

        <div class="form-group">
          <label for="strengths">Điểm mạnh:</label>
          <textarea id="strengths" name="profile_strengths"></textarea>
        </div>

        <div class="form-group">
          <label for="weaknesses">Điểm yếu:</label>
          <textarea id="weaknesses" name="profile_weaknesses"></textarea>
        </div>

        <div class="form-group">
          <label for="role">Vai trò mong muốn trong nhóm:</label>
          <input type="text" id="role" name="profile_role_preference" value="">
        </div>

        <button type="submit" class="btn-primary">💾 Lưu thay đổi</button>
      </form>
    </div>
  </main>

  <script>
  const avatarInput = document.getElementById('avatarInput');
  const avatarPreview = document.getElementById('avatarPreview');
  const avatarForm = document.getElementById('avatarForm');

  avatarInput.addEventListener('change', () => {
    if (avatarInput.files && avatarInput.files[0]) {
      const reader = new FileReader();
      reader.onload = e => avatarPreview.src = e.target.result;
      reader.readAsDataURL(avatarInput.files[0]);
      setTimeout(() => avatarForm.submit(), 400);
    }
  });
  </script>
</body>
</html>
