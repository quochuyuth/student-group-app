<?php
// app/views/group_rubric_manager.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
// Các biến $group, $criteria, $total_weight_sum được truyền từ RubricController
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tiêu chí - <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="public/css/group_rubric.css">
</head>
<body>
    <div class="background"></div>

    <header class="dashboard-header">
        <div class="logo">Student<span>Group</span>App</div>
        <nav>
            <a href="index.php?page=dashboard">Trang Chủ</a>
            <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>">Chi tiết nhóm</a>
            <a href="index.php?page=group_rubric&group_id=<?php echo $group['group_id']; ?>">Trang Đánh giá</a>
            <a href="index.php?action=logout" class="btn-logout">Đăng Xuất</a>
        </nav>
    </header>

    <main class="container">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="flash-message"><?= $_SESSION['flash_message']; ?></div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <div class="form-container">
            <h2>Quản lý Tiêu chí Đánh giá</h2>
            <p>Thiết lập các tiêu chí và trọng số (%) cho nhóm này. Tổng trọng số phải bằng 100%.</p>

            <table class="rubric-table">
                <thead>
                    <tr>
                        <th>Tên tiêu chí</th>
                        <th>Trọng số</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_weight_display = 0;
                    if (empty($criteria)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">Chưa có tiêu chí nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        foreach ($criteria as $c): 
                            $weight_percent = $c['criteria_weight'] * 100;
                            $total_weight_display += $weight_percent;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['criteria_name']); ?></td>
                                <td><?php echo $weight_percent; ?>%</td>
                                <td>
                                    <form action="index.php?action=delete_criteria" method="POST" style="display:inline;">
                                        <input type="hidden" name="criteria_id" value="<?php echo $c['criteria_id']; ?>">
                                        <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                        <button type="submit" class="btn" style="background: #dc3545;" onclick="return confirm('Bạn có chắc muốn xóa tiêu chí này?');">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f0f4ff; font-weight: bold;">
                        <td style="text-align: right;">TỔNG CỘNG</td>
                        <td colspan="2" style="<?php echo ($total_weight_display != 100) ? 'color: red;' : 'color: green;'; ?>">
                            <?php echo $total_weight_display; ?> / 100%
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="form-container">
            <h3>Thêm tiêu chí mới</h3>
            <form action="index.php?action=add_criteria" method="POST">
                <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                <div class="form-group">
                    <label for="criteria_name">Tên tiêu chí:</label>
                    <input type="text" id="criteria_name" name="criteria_name" required>
                </div>
                <div class="form-group">
                    <label for="criteria_weight">Trọng số (%):</label>
                    <input type="number" id="criteria_weight" name="criteria_weight" min="1" max="100" required placeholder="Nhập số, ví dụ: 30">
                </div>
                <button type="submit" class="btn">Thêm Tiêu chí</button>
            </form>
        </div>
    </main>
</body>
</html>