<?php
// app/views/group_rubric_member.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
// Các biến $group, $my_stats, $my_feedback được truyền từ RubricController
// (Chúng ta không cần $criteria ở đây nữa)
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả đánh giá - <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="public/css/group_rubric.css"> 
</head>
<body>
    <div class="background"></div>

    <header class="dashboard-header">
        <div class="logo">Student<span>Group</span>App</div>
        <nav>
            <a href="index.php?page=dashboard">Trang Chủ</a>
            <a href="index.php?page=profile">Hồ sơ</a>
            <a href="index.php?page=groups">Danh Sách Nhóm</a>
            <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>">Quản lí nhóm</a>
            <a href="index.php?action=logout" class="btn-logout">Đăng Xuất</a>
        </nav>
    </header>

    <main class="container">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="flash-message"><?= $_SESSION['flash_message']; ?></div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <div class="form-container">
            <h2>📊 Kết quả đánh giá của bạn</h2>
            <p>Đây là điểm trung bình từ các lần đánh giá của trưởng nhóm.</p>

            <?php if (empty($my_stats['final_average'])): ?>
                <p><i>Bạn chưa có điểm đánh giá nào.</i></p>
            <?php elseif (empty($my_stats['criteria_scores'])): ?>
                 <p><i>Chưa có dữ liệu điểm chi tiết.</i></p>
                 <p style="font-size: 1.2em; font-weight: 700;">Điểm tổng kết trung bình: <?php echo number_format($my_stats['final_average'], 2); ?> / 4.0</p>
            <?php else: ?>
                <table class="rubric-table">
                    <thead>
                        <tr>
                            <th>Tiêu chí</th>
                            <th>Trọng số</th>
                            <th style="text-align: center;">Điểm trung bình (1-4)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Dùng $my_stats['criteria_scores'] (đã JOIN)
                        foreach ($my_stats['criteria_scores'] as $score_data): 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($score_data['criteria_name']); ?></td>
                                <td><?php echo ($score_data['criteria_weight'] * 100); ?>%</td>
                                <td style="text-align: center; font-weight: 600;">
                                    <?php echo number_format($score_data['average_score'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <tr style="background: #f0f4ff;">
                            <td colspan="2" style="font-weight: 700; text-align: right;">ĐIỂM TỔNG KẾT TRUNG BÌNH</td>
                            <td style="font-size: 1.2em; font-weight: 700; text-align: center;">
                                <?php echo number_format($my_stats['final_average'], 2); ?> / 4.0
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="form-container">
            <h2>📩 Gửi phản hồi về kết quả</h2>
            <p>Nếu có thắc mắc về điểm số, bạn có thể gửi phản hồi cho trưởng nhóm.</p>
            <form action="index.php?action=submit_feedback" method="POST">
                <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                <div class="form-group">
                    <label for="feedback_content">Nội dung phản hồi:</label>
                    <textarea id="feedback_content" name="feedback_content" rows="5" required><?php echo htmlspecialchars($my_feedback); ?></textarea>
                </div>
                <button type="submit" class="btn">Gửi Phản Hồi</button>
            </form>
        </div>
    </main>
</body>
</html>