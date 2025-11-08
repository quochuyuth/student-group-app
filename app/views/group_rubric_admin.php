<?php
// app/views/group_rubric_admin.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
// Các biến $group, $members, $criteria được truyền từ RubricController
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá thành viên - <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="public/css/group_rubric.css">
    <style>
        /* Các style bổ sung cho phần phản hồi */
        .feedback-section {
            background-color: #f9f9f9;
            border-left: 5px solid #007bff;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .feedback-section h3 { margin-top: 0; color: #007bff; }
        .feedback-section p { font-style: italic; color: #555; white-space: pre-wrap; }
        .feedback-section .no-feedback { color: #777; }
        .manage-link {
            display: inline-block;
            background: #ffc107;
            color: #333;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <header class="dashboard-header">
        <div class="logo">Student<span>Group</span>App</div>
        <nav>
            <a href="index.php?page=dashboard">Trang Chủ</a>
            <a href="index.php?page=profile">Hồ sơ</a>
            <a href="index.php?page=groups">Quản Lí Nhóm</a>
            <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>">Chi tiết nhóm</a>
            <a href="index.php?action=logout" class="btn-logout">Đăng Xuất</a>
        </nav>
    </header>

    <main class="container">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="flash-message"><?= $_SESSION['flash_message']; ?></div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <div class="form-container">
            <h2>Đánh giá thành viên nhóm "<?php echo htmlspecialchars($group['group_name']); ?>"</h2>
            
            <a href="index.php?page=manage_rubric&group_id=<?php echo $group['group_id']; ?>" class="manage-link">
                ⚙️ Quản lý Tiêu chí & Trọng số
            </a>
            
            <form action="index.php?action=submit_rubric" method="POST">
                <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">

                <div class="form-group">
                    <label for="evaluated_member">Chọn thành viên:</label>
                    <select id="evaluated_member" name="evaluated_user_id" required>
                        <option value="">-- Chọn thành viên --</option>
                        <?php foreach ($members as $member): ?>
                            <?php if ($member['user_id'] != $_SESSION['user_id']): // Không cho tự đánh giá mình ?>
                                <option value="<?php echo $member['user_id']; ?>">
                                    <?php echo htmlspecialchars($member['username']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                
                <div class="rubric-section">
                    <h3>2. Cho điểm (Thang 1-4)</h3>
                    <p>1 = Yếu, 2 = Trung bình, 3 = Tốt, 4 = Xuất sắc</p>
                    
                    <?php if (empty($criteria)): ?>
                        <p style="color: red; font-weight: bold;">Chưa có tiêu chí nào được thiết lập. Vui lòng vào "Quản lý Tiêu chí" để thêm.</p>
                    <?php else: ?>
                        <table class="rubric-table">
                            <thead>
                                <tr>
                                    <th>Tiêu chí</th>
                                    <th>Trọng số</th>
                                    <th>Điểm (1-4)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($criteria as $c): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($c['criteria_name']); ?></td>
                                        <td><?php echo ($c['criteria_weight'] * 100); ?>%</td>
                                        <td>
                                            <div class="score-options">
                                                <label><input type="radio" name="scores[<?php echo $c['criteria_id']; ?>]" value="1" required> 1</label>
                                                <label><input type="radio" name="scores[<?php echo $c['criteria_id']; ?>]" value="2"> 2</label>
                                                <label><input type="radio" name="scores[<?php echo $c['criteria_id']; ?>]" value="3"> 3</label>
                                                <label><input type="radio" name="scores[<?php echo $c['criteria_id']; ?>]" value="4"> 4</label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="submit" class="btn" style="margin-top: 15px;">Hoàn thành đánh giá</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        
        <div id="member-feedback-container" class="form-container" style="display: none;">
            <h3>Phản hồi của thành viên <span id="feedback-member-name" style="color: #007bff;"></span></h3>
            <div class="feedback-section">
                <p id="feedback-content" class="no-feedback"><i>Chưa có phản hồi từ thành viên này.</i></p>
            </div>
        </div>
        
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const evaluatedMemberSelect = document.getElementById('evaluated_member');
            const feedbackContainer = document.getElementById('member-feedback-container');
            const feedbackMemberName = document.getElementById('feedback-member-name');
            const feedbackContent = document.getElementById('feedback-content');
            const groupId = document.querySelector('input[name="group_id"]').value;

            evaluatedMemberSelect.addEventListener('change', function() {
                const selectedMemberId = this.value;
                const selectedMemberName = this.options[this.selectedIndex].text;

                if (selectedMemberId) {
                    feedbackContainer.style.display = 'block';
                    feedbackMemberName.textContent = selectedMemberName;
                    feedbackContent.textContent = 'Đang tải phản hồi...';
                    feedbackContent.classList.remove('no-feedback');

                    fetch(`index.php?action=get_member_feedback&group_id=${groupId}&member_id=${selectedMemberId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                feedbackContent.textContent = `Lỗi: ${data.error}`;
                                feedbackContent.classList.add('no-feedback');
                            } else {
                                if (data.feedback_content) {
                                    feedbackContent.textContent = data.feedback_content;
                                    feedbackContent.classList.remove('no-feedback');
                                } else {
                                    feedbackContent.textContent = 'Chưa có phản hồi từ thành viên này.';
                                    feedbackContent.classList.add('no-feedback');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching feedback:', error);
                            feedbackContent.textContent = 'Không thể tải phản hồi. Vui lòng thử lại.';
                            feedbackContent.classList.add('no-feedback');
                        });
                } else {
                    feedbackContainer.style.display = 'none';
                    feedbackMemberName.textContent = '';
                    feedbackContent.textContent = '';
                }
            });
        });
    </script>
</body>
</html>