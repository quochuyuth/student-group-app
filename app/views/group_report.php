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
    <title>Báo cáo - <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="public/css/report.css">
</head>
<body>
    <div class="background"></div>

    <header class="dashboard-header">
        <h1 class="logo">Báo cáo nhóm <span><?php echo htmlspecialchars($group['group_name']); ?></span></h1>
        <nav>
            <a href="index.php?page=dashboard">Trang Chủ</a>
            <a href="index.php?page=profile">Hồ Sơ</a>
            <a href="index.php?page=groups">Quản Lý Nhóm</a>
            <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>">Quản lí nhóm</a>
            <a href="index.php?action=logout" class="btn-logout">Đăng Xuất</a>
        </nav>
    </header>

    <main class="report-container">
        <section class="report-header">
            <h2>📊 Báo cáo tổng quan nhóm</h2>
            <p>Phân tích tiến độ và đóng góp của từng thành viên</p>
        </section>

        <div class="report-grid">

            <aside class="chart-container">
                <h3>Tiến độ nhóm</h3>
                <img src="<?php echo $chartUrl; ?>" alt="Biểu đồ tiến độ công việc">
            </aside>

            <section class="score-table-container">
                <h3>Điểm đóng góp thành viên</h3>
                <table class="score-table">
                    <thead>
                        <tr>
                            <th>👤 Thành viên</th>
                            <th>📈 Điểm Task</th>
                            <th>⭐ Điểm Đánh giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contributionData as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['username']); ?></td>
                                <td><?php echo number_format($member['total_task_points'] ?? 0, 1); ?> điểm</td>
                                <td>
                                    <?php 
                                    if ($member['avg_rubric_score']) {
                                        echo number_format($member['avg_rubric_score'], 2) . " / 4.0";
                                    } else {
                                        echo "Chưa đánh giá";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="note">
                    <p>💡 <strong>Điểm Task</strong>: Tổng điểm các công việc được giao (Done: 100%, Review: 60%, In Progress: 30%, Backlog: 0%).</p>
                    <p>⭐ <strong>Điểm Đánh giá</strong>: Trung bình điểm từ các lần được đánh giá (Rubric).</p>
                </div>
            </section>

        </div>
    </main>
</body>
</html>
