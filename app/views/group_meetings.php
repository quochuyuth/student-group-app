<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý Họp - <?php echo htmlspecialchars($group['group_name']); ?></title>
  <link rel="stylesheet" href="public/css/group_meetings.css">
</head>
<body>
  <div class="background-overlay"></div>

  <header class="dashboard-header">
    <h1>📅 Họp nhóm: <span><?php echo htmlspecialchars($group['group_name']); ?></span></h1>
    <nav>
      <a href="index.php?page=dashboard">Trang chủ</a>
      <a href="index.php?page=profile">Hồ sơ</a>
      <a href="index.php?page=groups">Nhóm</a>
      <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>">🔙 Chi tiết nhóm</a>
      <a href="index.php?action=logout" class="logout-btn">🚪 Đăng xuất</a>
    </nav>
  </header>

  <main class="container">
    <?php if (isset($_SESSION['flash_message'])): ?>
      <div class="flash-message">
        <?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
      </div>
    <?php endif; ?>

    <section class="form-section">
      <h2>🗓️ Tạo cuộc họp mới</h2>
      <form action="index.php?action=create_meeting" method="POST">
        <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">

        <div class="form-group">
          <label for="meeting_title">Tiêu đề:</label>
          <input type="text" id="meeting_title" name="meeting_title" required>
        </div>

        <div class="form-group">
          <label for="start_time">Thời gian bắt đầu:</label>
          <input type="datetime-local" id="start_time" name="start_time" required>
        </div>

        <div class="form-group">
          <label for="agenda">Nội dung (Agenda):</label>
          <textarea id="agenda" name="agenda" rows="4"></textarea>
        </div>

        <button type="submit" class="btn-primary">Tạo Lịch</button>
      </form>
    </section>

    <section class="list-section">
      <h2>📋 Danh sách các cuộc họp</h2>

      <?php if (empty($meetings)): ?>
        <p class="empty">Chưa có cuộc họp nào được đặt.</p>
      <?php else: ?>
        <div class="meeting-list">
          <?php foreach ($meetings as $meeting): ?>
            <div class="meeting-card">
              <h3>
                <a href="index.php?page=meeting_details&id=<?php echo $meeting['meeting_id']; ?>">
                  <?php echo htmlspecialchars($meeting['meeting_title']); ?>
                </a>
              </h3>
              <p><strong>🕒 Thời gian:</strong> <?php echo date('d/m/Y H:i', strtotime($meeting['start_time'])); ?></p>
              <p><strong>👤 Người đặt:</strong> <?php echo htmlspecialchars($meeting['creator_name']); ?></p>
              <div class="agenda"><strong>📝 Agenda:</strong>
                <pre><?php echo htmlspecialchars($meeting['agenda']); ?></pre>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
