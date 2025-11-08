<?php
// app/views/meeting_details.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
// $meeting và $user_rating được truyền từ MeetingController
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết họp: <?php echo htmlspecialchars($meeting['meeting_title']); ?></title>
    <link rel="stylesheet" href="public/css/meeting_details.css">
</head>
<body>
    <div class="background"></div>

    <header class="dashboard-header">
        <div class="logo">Student<span>Group</span>App</div>
        <nav>
            <a href="index.php?page=dashboard">Trang Chủ</a>
            <a href="index.php?page=profile">Hồ sơ</a>
            <a href="index.php?page=groups">Quản Lí Nhóm</a>
            <a href="index.php?page=group_meetings&group_id=<?php echo $meeting['group_id']; ?>">Danh sách họp</a>
            <a href="index.php?action=logout" class="btn-logout">Đăng Xuất</a>
        </nav>
    </header>

    <main class="container">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="flash-message"><?= $_SESSION['flash_message']; ?></div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <section class="meeting-info form-container">
             <h2><?php echo htmlspecialchars($meeting['meeting_title']); ?></h2>

    <div class="info-boxes">
        <div class="info-box">
            <strong>Thời gian:</strong> <?php echo date('d/m/Y H:i', strtotime($meeting['start_time'])); ?>
        </div>
        <div class="info-box">
            <strong>Người tạo:</strong> <?php echo htmlspecialchars($meeting['creator_name']); ?>
        </div>
    </div>

    <div class="agenda-card">
        <h3>Nội dung (Agenda)</h3>
        <pre><?php echo htmlspecialchars($meeting['agenda']); ?></pre>
    </div>
        </section>

        <section class="form-container">
            <h2>Biên bản họp (Minutes)</h2>
            <form action="index.php?action=save_minutes" method="POST">
                <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
                <div class="form-group">
                    <label for="minutes">Nội dung đã diễn ra:</label>
                    <textarea id="minutes" name="minutes" rows="8"><?php echo htmlspecialchars($meeting['minutes'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="action_items">Việc cần làm sau họp:</label>
                    <textarea id="action_items" name="action_items" rows="5"><?php echo htmlspecialchars($meeting['action_items'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn">💾 Lưu Biên Bản</button>
            </form>
        </section>

        <section class="form-container">
            <h2>Đánh giá cuộc họp</h2>
            <p>Bạn cảm thấy cuộc họp này hiệu quả ở mức nào?</p>
            <form action="index.php?action=submit_meeting_rating" method="POST">
                <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
                <div class="rating-stars">
                    <?php for ($i=5; $i>=1; $i--): ?>
                        <input type="radio" id="star<?php echo $i; ?>" name="satisfaction_rating" value="<?php echo $i; ?>" <?php echo ($user_rating == $i) ? 'checked' : ''; ?> required>
                        <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> sao">&#9733;</label>
                    <?php endfor; ?>
                </div>
                <button type="submit" class="btn">Gửi Đánh Giá</button>
            </form>
        </section>
    </main>
</body>
</html>
