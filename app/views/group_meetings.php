<?php
// app/views/group_meetings.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
// $group, $meetings được truyền từ MeetingController
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Họp - <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .meeting-list-item {
            background-color: #f9f9f9; border: 1px solid #ddd;
            padding: 15px; margin-bottom: 10px; border-radius: 5px;
        }
        /* THÊM CSS NÀY ĐỂ BIẾN TIÊU ĐỀ THÀNH LINK */
        .meeting-list-item h3 a { text-decoration: none; color: #333; }
        .meeting-list-item h3 a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <h1>Quản lý Họp: <?php echo htmlspecialchars($group['group_name']); ?></h1>
        <nav>
            <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>">Quay lại Chi tiết nhóm</a>
            <a href="index.php?action=logout">Đăng Xuất</a>
        </nav>
    </header>

    <main class="container">

        <?php
        if (isset($_SESSION['flash_message'])) {
            echo '<div class="flash-message">' . $_SESSION['flash_message'] . '</div>';
            unset($_SESSION['flash_message']);
        }
        ?>

        <section class="form-container">
            <h2>Đặt lịch họp mới</h2>
            <form action="index.php?action=create_meeting" method="POST">
                <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                <div class="form-group">
                    <label for="meeting_title">Tiêu đề cuộc họp:</label>
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
                <button type="submit" class="btn">Đặt Lịch</button>
            </form>
        </section>

        <hr>

        <section class="list-container">
            <h2>Các cuộc họp đã đặt</h2>
            
            <?php if (empty($meetings)): ?>
                <p>Chưa có cuộc họp nào được đặt.</p>
            <?php else: ?>
                <div class="meeting-list">
                    <?php foreach ($meetings as $meeting): ?>
                        <div class="meeting-list-item">
                            
                            <h3>
                                <a href="index.php?page=meeting_details&id=<?php echo $meeting['meeting_id']; ?>">
                                    <?php echo htmlspecialchars($meeting['meeting_title']); ?>
                                </a>
                            </h3>
                            <p><strong>Thời gian:</strong> <?php echo date('d/m/Y H:i', strtotime($meeting['start_time'])); ?></p>
                            <p><strong>Người đặt:</strong> <?php echo htmlspecialchars($meeting['creator_name']); ?></p>
                            <div><strong>Agenda:</strong><pre><?php echo htmlspecialchars($meeting['agenda']); ?></pre></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>