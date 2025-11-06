<?php
// app/views/group_report.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
// $group, $chartUrl, $contributionData được truyền từ ReportController
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo - <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .report-grid {
            display: flex;
            gap: 20px;
        }
        .chart-container {
            flex: 1;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .chart-container img {
            max-width: 100%;
            height: auto;
        }
        .score-table-container {
            flex: 2;
        }
        .score-table {
            width: 100%;
            border-collapse: collapse;
        }
        .score-table th, .score-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .score-table th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <header>
        <h1>Báo cáo: <?php echo htmlspecialchars($group['group_name']); ?></h1>
        <nav>
            <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>">Quay lại Chi tiết nhóm</a>
            <a href="index.php?action=logout">Đăng Xuất</a>
        </nav>
    </header>

    <main class="container">

        <div class="report-grid">
            
            <aside class="chart-container">
                <h2>Tiến độ Nhóm</h2>
                <img src="<?php echo $chartUrl; ?>" alt="Biểu đồ tiến độ công việc">
            </aside>

            <section class="score-table-container">
                <h2>Điểm Đóng Góp Thành Viên</h2>
                <table class="score-table">
                    <thead>
                        <tr>
                            <th>Thành viên</th>
                            <th>Điểm Task (từ Kanban)</th>
                            <th>Điểm Đánh giá (từ Rubric)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contributionData as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['username']); ?></td>
                                
                                <td><?php echo (int)$member['total_task_points']; ?> điểm</td>
                                
                                <td>
                                    <?php 
                                    if ($member['avg_rubric_score']) {
                                        echo number_format($member['avg_rubric_score'], 2) . " / 4.0";
                                    } else {
                                        echo "Chưa được đánh giá";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <small>* Điểm Task = Tổng điểm từ các task ở cột "Done".</small><br>
                <small>* Điểm Đánh giá = Điểm trung bình từ các lần đánh giá (Rubric).</small>
            </section>

        </div>
    </main>
</body>
</html>