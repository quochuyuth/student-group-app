<?php
// app/views/group_rubric.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
// $group, $members, $tasks, $criteria (array) được truyền từ RubricController

// Ánh xạ tên ENUM sang Tiếng Việt để hiển thị
$criteria_names_vn = [
    'completion' => 'Hoàn thành nhiệm vụ',
    'deadline' => 'Deadline',
    'quality' => 'Chất lượng',
    'communication' => 'Giao tiếp',
    'initiative' => 'Chủ động'
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá Rubric - <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        /* (CSS giữ nguyên như cũ) */
        .rubric-table { width: 100%; border-collapse: collapse; }
        .rubric-table th, .rubric-table td {
            border: 1px solid #ddd; padding: 8px; text-align: left;
        }
        .rubric-table th { background-color: #f4f4f4; }
        .rubric-table .score-options { text-align: center; }
        .rubric-table .score-options label { margin: 0 5px; }
    </style>
</head>
<body>
    <header>
        <h1>Đánh giá Rubric: <?php echo htmlspecialchars($group['group_name']); ?></h1>
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

        <form action="index.php?action=submit_rubric" method="POST" class="form-container">
            <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">

            <h2>1. Chọn đối tượng đánh giá</h2>
            
            <div class="form-group">
                <label for="evaluated_user_id">Chọn thành viên:</label>
                <select id="evaluated_user_id" name="evaluated_user_id" required>
                    <option value="">-- Chọn thành viên --</option>
                    <?php foreach ($members as $member): ?>
                        <?php if ($member['user_id'] != $_SESSION['user_id']): ?>
                            <option value="<?php echo $member['user_id']; ?>">
                                <?php echo htmlspecialchars($member['username']); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="task_id">Gắn với công việc (không bắt buộc):</label>
                <select id="task_id" name="task_id">
                    <option value="">-- Không gắn --</option>
                    <?php foreach ($tasks as $task): ?>
                        <option value="<?php echo $task['task_id']; ?>">
                            <?php echo htmlspecialchars($task['task_title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <h2>2. Cho điểm (Thang 1-4)</h2>
            <p>1 = Yếu, 2 = Trung bình, 3 = Tốt, 4 = Xuất sắc</p>

            <table class="rubric-table">
                <thead>
                    <tr>
                        <th>Tiêu chí</th>
                        <th>Trọng số</th>
                        <th style="text-align: center;">Điểm (1-4)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($criteria as $name => $weight): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($criteria_names_vn[$name]); ?></td>
                        <td><?php echo ($weight * 100); ?>%</td>
                        <td class="score-options">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                            <label>
                                <input type="radio" 
                                       name="scores[<?php echo $name; ?>]" 
                                       value="<?php echo $i; ?>" required>
                                <?php echo $i; ?>
                            </label>
                            <?php endfor; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <br>
            <button type="submit" class="btn">Gửi Đánh Giá</button>
        </form>

    </main>
</body>
</html>