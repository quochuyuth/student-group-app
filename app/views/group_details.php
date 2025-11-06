<?php
// app/views/group_details.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
// $group, $tasks, $members được truyền từ GroupController
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết nhóm: <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="public/css/style.css">
    
    <style>
        .kanban-board { display: flex; justify-content: space-between; gap: 15px; }
        .kanban-column {
            width: 24%; background-color: #f4f4f4; border-radius: 5px;
            padding: 10px; min-height: 300px;
        }
        .kanban-column h3 { margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #ddd; }
        .task-card {
            background-color: #fff; border: 1px solid #ddd; border-radius: 5px;
            padding: 10px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            cursor: pointer;
        }
        .task-card:active { cursor: grabbing; }
        .task-card p { margin: 5px 0; }
        .task-card .priority {
            font-weight: bold; padding: 2px 5px; border-radius: 3px;
            display: inline-block; font-size: 0.8em;
        }
        .modal {
            display: none; position: fixed; z-index: 1000;
            left: 0; top: 0; width: 100%; height: 100%;
            overflow: auto; background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe; margin: 10% auto; padding: 20px;
            border: 1px solid #888; width: 60%; max-width: 700px; border-radius: 8px;
        }
        .modal-close {
            color: #aaa; float: right; font-size: 28px;
            font-weight: bold; cursor: pointer;
        }
        #task-details-comments {
            max-height: 200px; overflow-y: auto;
            background-color: #f9f9f9; padding: 10px;
            border-radius: 5px; margin-top: 10px;
        }
        .comment { border-bottom: 1px solid #eee; padding: 5px 0; }
        .comment small { color: #555; }
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($group['group_name']); ?></h1>
        <nav>
            <a href="index.php?page=groups">Quay lại Danh sách nhóm</a>
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

        <div style="display: flex; gap: 20px;">
            <section class="function-placeholder form-container" style="flex: 1;">
                <h2>Mời thành viên mới</h2>
                <form action="index.php?action=invite_member" method="POST">
                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                    <div class="form-group">
                        <label for="email_or_username">Nhập Email hoặc Username:</label>
                        <input type="text" id="email_or_username" name="email_or_username">
                    </div>
                    <button type="submit" class="btn">Gửi Lời Mời</button>
                </form>
            </section>
            <section class="function-placeholder form-container" style="flex: 2;">
                <h2>Tạo công việc mới</h2>
                <form action="index.php?action=create_task" method="POST">
                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                    <div class="form-group"><label for="task_title">Tiêu đề:</label><input type="text" id="task_title" name="task_title" required></div>
                    <div class="form-group"><label for="task_description">Mô tả:</label><textarea id="task_description" name="task_description" rows="2"></textarea></div>
                    <div style="display: flex; gap: 10px;">
                        <div class="form-group" style="flex: 1;"><label for="priority">Ưu tiên:</label><select id="priority" name="priority"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option></select></div>
                        <div class="form-group" style="flex: 1;"><label for="assigned_to_user_id">Giao cho:</label><select id="assigned_to_user_id" name="assigned_to_user_id"><option value="">-- Không giao --</option><?php foreach ($members as $member): ?><option value="<?php echo $member['user_id']; ?>"><?php echo htmlspecialchars($member['username']); ?></option><?php endforeach; ?></select></div>
                        <div class="form-group" style="flex: 1;"><label for="due_date">Hết hạn:</label><input type="date" id="due_date" name="due_date"></div>
                        <div class="form-group" style="flex: 1;"><label for="points">Điểm:</label><input type="number" id="points" name="points" value="0" min="0"></div>
                    </div>
                    <button type="submit" class="btn">Tạo Task</button>
                </form>
            </section>
        </div>

        <hr>

        <section class="kanban-board-container">
            <h2>Bảng công việc (Kanban)</h2>
            <?php
            $columns = ['backlog' => [], 'in_progress' => [], 'review' => [], 'done' => []];
            foreach ($tasks as $task) { $columns[$task['status']][] = $task; }
            ?>
            <div class="kanban-board">
                <?php foreach ($columns as $status => $tasks_in_column): ?>
                    <div class="kanban-column" id="col-<?php echo $status; ?>">
                        <h3><?php echo ucfirst(str_replace('_', ' ', $status)); ?></h3>
                        <?php foreach ($tasks_in_column as $task): ?>
                            <div class="task-card" data-task-id="<?php echo $task['task_id']; ?>">
                                <strong><?php echo htmlspecialchars($task['task_title']); ?></strong>
                                <p class="priority priority-<?php echo $task['priority']; ?>"><?php echo ucfirst($task['priority']); ?></p>
                                <p>Giao cho: <?php echo htmlspecialchars($task['assignee_name'] ?? 'Chưa có'); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        
        <hr>

        <div class="group-functions" style="display: flex; gap: 20px;">

            <div class="function-placeholder" style="flex: 1;">
                <h2>Đánh giá thành viên</h2>
                <p>Đánh giá hiệu suất của các thành viên trong nhóm.</p>
                <a href="index.php?page=group_rubric&group_id=<?php echo $group['group_id']; ?>" class="btn">
                    Đi đến trang Đánh giá
                </a>
            </div>

            <div class="function-placeholder" style="flex: 1;">
                <h2>Họp nhóm</h2>
                <p>Nơi đặt lịch họp và xem biên bản họp.</p>
                <a href="index.php?page=group_meetings&group_id=<?php echo $group['group_id']; ?>" class="btn">
                    Quản lý Họp
                </a>
            </div>

        </div>

    </main>

    <div id="task-details-modal" class="modal">
        <div class="modal-content">
            <span class="modal-close" id="modal-close-btn">&times;</span>
            <h2 id="modal-task-title">Tiêu đề Task</h2>
            <p>Giao cho: <strong id="modal-task-assignee"></strong> | Người tạo: <strong id="modal-task-creator"></strong></p>
            <p>Độ ưu tiên: <strong id="modal-task-priority"></strong></p>
            <p>Ngày hết hạn: <strong id="modal-task-due-date"></strong></p>
            <p>Điểm: <strong id="modal-task-points"></strong></p>
            <hr>
            <h3>Mô tả</h3>
            <p id="modal-task-description"></p>
            <hr>
            <h3>Bình luận</h3>
            <div id="task-details-comments"></div>
            <form id="add-comment-form">
                <input type="hidden" id="modal-task-id" name="task_id">
                <div class="form-group">
                    <label for="comment_text">Viết bình luận:</label>
                    <textarea id="comment_text" name="comment_text" rows="2" required></textarea>
                </div>
                <button type="submit" class="btn">Gửi</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- 1. PHẦN KÉO-THẢ ---
            const columnIds = ['col-backlog', 'col-in_progress', 'col-review', 'col-done'];
            columnIds.forEach(colId => {
                const column = document.getElementById(colId);
                if (!column) return;
                new Sortable(column, {
                    group: 'kanban', animation: 150,
                    onEnd: function (evt) {
                        const taskId = evt.item.dataset.taskId;
                        const newStatus = evt.to.id.replace('col-', '');
                        sendUpdateToServer(taskId, newStatus);
                    }
                });
            });

            function sendUpdateToServer(taskId, newStatus) {
                fetch('index.php?action=update_task_status', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ task_id: taskId, new_status: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) { console.log('Cập nhật trạng thái thành công!'); } 
                    else { console.error('Lỗi cập nhật trạng thái:', data.message); }
                })
                .catch(error => console.error('Lỗi fetch:', error));
            }

            // --- 2. PHẦN XỬ LÝ MODAL ---
            const modal = document.getElementById('task-details-modal');
            const closeModalBtn = document.getElementById('modal-close-btn');

            document.querySelectorAll('.task-card').forEach(card => {
                card.addEventListener('click', function() {
                    const taskId = this.dataset.taskId;
                    openTaskModal(taskId);
                });
            });

            function openTaskModal(taskId) {
                fetch(`index.php?action=get_task_details&task_id=${taskId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const task = data.task;
                            document.getElementById('modal-task-title').textContent = task.task_title;
                            document.getElementById('modal-task-assignee').textContent = task.assignee_name || 'Chưa có';
                            document.getElementById('modal-task-creator').textContent = task.creator_name || 'N/A';
                            document.getElementById('modal-task-priority').textContent = task.priority;
                            document.getElementById('modal-task-due-date').textContent = task.due_date || 'N/A';
                            document.getElementById('modal-task-points').textContent = task.points;
                            document.getElementById('modal-task-description').textContent = task.task_description || 'Không có mô tả.';
                            document.getElementById('modal-task-id').value = task.task_id;

                            const commentsContainer = document.getElementById('task-details-comments');
                            commentsContainer.innerHTML = '';
                            if (data.comments.length > 0) {
                                data.comments.forEach(c => appendComment(c, commentsContainer));
                            } else {
                                commentsContainer.innerHTML = '<p>Chưa có bình luận nào.</p>';
                            }
                            modal.style.display = 'block';
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    });
            }

            closeModalBtn.onclick = () => { modal.style.display = 'none'; }
            window.onclick = (event) => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }

            document.getElementById('add-comment-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const commentTextEl = document.getElementById('comment_text');

                fetch('index.php?action=add_task_comment', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const commentsContainer = document.getElementById('task-details-comments');
                        if (commentsContainer.querySelector('p')) {
                            commentsContainer.innerHTML = '';
                        }
                        appendComment(data.comment, commentsContainer);
                        commentTextEl.value = '';
                    } else {
                        alert('Lỗi gửi bình luận: ' + data.message);
                    }
                });
            });

            function appendComment(comment, container) {
                const commentEl = document.createElement('div');
                commentEl.className = 'comment';
                commentEl.innerHTML = `
                    <p><strong>${comment.commenter_name}:</strong> ${comment.comment_text}</p>
                    <small>${new Date(comment.created_at).toLocaleString('vi-VN')}</small>
                `;
                container.appendChild(commentEl);
            }
        });
    </script>
</body>
</html>