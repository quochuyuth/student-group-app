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
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .rating-stars { display: flex; flex-direction: row-reverse; justify-content: flex-end; }
        .rating-stars input[type="radio"] { display: none; }
        .rating-stars label {
            font-size: 2em; color: #ddd;
            cursor: pointer; padding: 0 2px;
        }
        .rating-stars input[type="radio"]:checked ~ label,
        .rating-stars label:hover,
        .rating-stars label:hover ~ label {
            color: #f5c518;
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($meeting['meeting_title']); ?></h1>
        <nav>
            <a href="index.php?page=group_meetings&group_id=<?php echo $meeting['group_id']; ?>">Quay lại DS Họp</a>
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

        <section class="meeting-info">
            <p><strong>Thời gian:</strong> <?php echo date('d/m/Y H:i', strtotime($meeting['start_time'])); ?></p>
            <p><strong>Người tạo:</strong> <?php echo htmlspecialchars($meeting['creator_name']); ?></p>
            <div><h3>Nội dung (Agenda)</h3><pre><?php echo htmlspecialchars($meeting['agenda']); ?></pre></div>
        </section>

        <hr>

        <section class="form-container">
            <h2>Biên bản họp (Minutes)</h2>
            <form action="index.php?action=save_minutes" method="POST">
                <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
                <div class="form-group">
                    <label for="minutes">Nội dung đã diễn ra:</label>
                    <textarea id="minutes" name="minutes" rows="10"><?php echo htmlspecialchars($meeting['minutes'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="action_items">Việc cần làm sau họp:</label>
                    <textarea id="action_items" name="action_items" rows="5"><?php echo htmlspecialchars($meeting['action_items'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn">Lưu Biên Bản</button>
            </form>
        </section>

        <hr>

        <section class="form-container">
            <h2>Đánh giá cuộc họp</h2>
            <p>Bạn cảm thấy cuộc họp này hiệu quả ở mức nào?</p>
            <form action="index.php?action=submit_meeting_rating" method="POST">
                <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
                
                <div class="rating-stars">
                    <input type="radio" id="star5" name="satisfaction_rating" value="5" <?php echo ($user_rating == 5) ? 'checked' : ''; ?> required>
                    <label for="star5" title="5 sao">&#9733;</label>
                    <input type="radio" id="star4" name="satisfaction_rating" value="4" <?php echo ($user_rating == 4) ? 'checked' : ''; ?>>
                    <label for="star4" title="4 sao">&#9733;</label>
                    <input type="radio" id="star3" name="satisfaction_rating" value="3" <?php echo ($user_rating == 3) ? 'checked' : ''; ?>>
                    <label for="star3" title="3 sao">&#9733;</label>
                    <input type="radio" id="star2" name="satisfaction_rating" value="2" <?php echo ($user_rating == 2) ? 'checked' : ''; ?>>
                    <label for="star2" title="2 sao">&#9733;</label>
                    <input type="radio" id="star1" name="satisfaction_rating" value="1" <?php echo ($user_rating == 1) ? 'checked' : ''; ?>>
                    <label for="star1" title="1 sao">&#9733;</label>
                </div>
                
                <br>
                <button type="submit" class="btn">Gửi Đánh Giá</button>
            </form>
        </section>

    </main>
</body>
</html>