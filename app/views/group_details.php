<?php
// app/views/group_details.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
// Các biến $group, $tasks, $members, $messages, $polls, $user_votes được truyền từ GroupController
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết nhóm: <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="public/css/style.css">
    
    <style>
        /* CSS cho Kanban */
        .kanban-board { display: flex; justify-content: space-between; gap: 15px; }
        .kanban-column { width: 24%; background-color: #f4f4f4; border-radius: 5px; padding: 10px; min-height: 300px; }
        .kanban-column h3 { margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #ddd; }
        .task-card { background-color: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 10px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); cursor: pointer; }
        .task-card:active { cursor: grabbing; }
        .task-card p { margin: 5px 0; }
        .task-card .priority { font-weight: bold; padding: 2px 5px; border-radius: 3px; display: inline-block; font-size: 0.8em; }

        /* CSS cho Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 60%; max-width: 700px; border-radius: 8px; }
        .modal-close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        #task-details-comments { max-height: 200px; overflow-y: auto; background-color: #f9f9f9; padding: 10px; border-radius: 5px; margin-top: 10px; }
        .comment { border-bottom: 1px solid #eee; padding: 5px 0; }
        
        /* CSS cho Chat */
        #chat-box { height: 400px; overflow-y: auto; border: 1px solid #ddd; background-color: #f9f9f9; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .chat-message { margin-bottom: 10px; padding: 8px 12px; border-radius: 10px; background-color: #fff; border: 1px solid #eee; max-width: 80%; word-wrap: break-word; }
        .chat-message.is-user { background-color: #dcf8c6; align-self: flex-end; margin-left: auto; }
        .chat-message.is-file { background-color: #e6f7ff; }
        .chat-message.is-file p a { font-weight: bold; text-decoration: none; }
        #chat-controls { display: flex; gap: 10px; }
        #chat-form { flex: 1; display: flex; }
        #chat-form input[type="text"] { flex: 1; border-radius: 5px 0 0 5px; }
        #chat-form button { border-radius: 0 5px 5px 0; }
        #file-upload-btn { flex-shrink: 0; width: 45px; padding: 0; font-size: 1.2em; border-radius: 5px; }
        #hidden-file-form { display: none; }

        /* CSS cho Polls */
        .poll-container { border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 15px; background-color: #fdfdfd; }
        .poll-option { margin: 5px 0; position: relative; padding: 5px; border-radius: 3px; }
        .poll-option label { display: flex; justify-content: space-between; width: 100%; cursor: pointer; }
        .poll-option .vote-count { font-weight: bold; }
        .poll-option .vote-bar { position: absolute; left: 0; top: 0; height: 100%; background-color: #dcf8c6; z-index: -1; opacity: 0.7; }
        
        /* CSS CHO DANH SÁCH FILE TRONG MODAL */
        #modal-task-files { max-height: 150px; overflow-y: auto; background-color: #f9f9f9; padding: 10px; border-radius: 5px; }
        #modal-task-files li { margin-bottom: 5px; }
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
                    <div class="form-group"><label for="email_or_username">Nhập Email hoặc Username:</label><input type="text" id="email_or_username" name="email_or_username"></div>
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

        <div style="display: flex; gap: 20px;">
            <section class="function-placeholder" style="flex: 2;" id="chat">
                <h2>Chat Nhóm</h2>
                <div id="chat-box">
                    <?php if (empty($messages)): ?>
                        <p>Chưa có tin nhắn nào.</p>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <?php 
                            $isUserClass = ($msg['sender_user_id'] == $_SESSION['user_id']) ? 'is-user' : ''; 
                            $isFileClass = !empty($msg['file_id']) ? 'is-file' : '';
                            ?>
                            <div class="chat-message <?php echo $isUserClass; ?> <?php echo $isFileClass; ?>">
                                <strong><?php echo htmlspecialchars($msg['sender_name']); ?>:</strong>
                                <?php if (!empty($msg['file_id'])): ?>
                                    <p>Đã gửi một file: <a href="<?php echo htmlspecialchars($msg['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($msg['file_name']); ?></a></p>
                                <?php else: ?>
                                    <p><?php echo htmlspecialchars($msg['message_content']); ?></p>
                                <?php endif; ?>
                                <small><?php echo date('d/m H:i', strtotime($msg['created_at'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div id="chat-controls">
                    <form id="hidden-file-form" action="index.php?action=send_file" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                        <input type="file" name="group_file" id="group_file_input" onchange="this.form.submit()" title="Đính kèm file">
                    </form>
                    <button id="file-upload-btn" class="btn" onclick="document.getElementById('group_file_input').click();" title="Đính kèm file">📎</button>
                    <form id="chat-form" action="index.php?action=send_message" method="POST">
                        <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                        <input type="text" name="message_content" placeholder="Gõ tin nhắn của bạn..." required autocomplete="off">
                        <button type="submit" class="btn">Gửi</button>
                    </form>
                </div>
            </section>
            
            <section class="function-placeholder" style="flex: 1;" id="polls">
                <h2>Bình chọn</h2>
                
                <div class="form-container" style="background-color: #f9f9f9; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                    <form action="index.php?action=create_poll" method="POST">
                        <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                        <div class="form-group">
                            <label for="poll_question">Câu hỏi:</label>
                            <input type="text" name="poll_question" id="poll_question" required>
                        </div>
                        <div class="form-group"><label>Các lựa chọn:</label>
                            <input type="text" name="options[]" placeholder="Lựa chọn 1" required>
                            <input type="text" name="options[]" placeholder="Lựa chọn 2">
                            <input type="text" name="options[]" placeholder="Lựa chọn 3">
                        </div>
                        <button type="submit" class="btn">Tạo Bình Chọn</button>
                    </form>
                </div>

                <div id="poll-list" style="max-height: 350px; overflow-y: auto;">
                    <?php if (empty($polls)): ?>
                        <p>Chưa có bình chọn nào.</p>
                    <?php else: ?>
                        <?php foreach ($polls as $poll): ?>
                            <div class="poll-container" id="poll-<?php echo $poll['poll_id']; ?>">
                                <strong><?php echo htmlspecialchars($poll['poll_question']); ?></strong>
                                <small>(Bởi: <?php echo htmlspecialchars($poll['creator_name']); ?>)</small>
                                <form action="index.php?action=submit_vote" method="POST">
                                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                    <input type="hidden" name="poll_id" value="<?php echo $poll['poll_id']; ?>">
                                    <?php 
                                    $total_votes = 0;
                                    foreach ($poll['options'] as $opt) { $total_votes += $opt['vote_count']; }
                                    ?>
                                    <?php foreach ($poll['options'] as $option): ?>
                                        <?php 
                                        $user_voted_this = ($user_votes[$poll['poll_id']] ?? 0) == $option['option_id'];
                                        $vote_percent = ($total_votes > 0) ? ($option['vote_count'] / $total_votes) * 100 : 0;
                                        ?>
                                        <div class="poll-option">
                                            <div class="vote-bar" style="width: <?php echo $vote_percent; ?>%;"></div>
                                            <label>
                                                <span>
                                                    <input type="radio" name="option_id" value="<?php echo $option['option_id']; ?>" 
                                                           <?php echo $user_voted_this ? 'checked' : ''; ?> required>
                                                    <?php echo htmlspecialchars($option['option_text']); ?>
                                                </span>
                                                <span class="vote-count">(<?php echo $option['vote_count']; ?>)</span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                    <button type="submit" class="btn btn-small">Bầu chọn</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <hr>

        <section class="kanban-board-container">
            <h2>Bảng công việc (Kanban)</h2>
            <?php
            // Định nghĩa $columns để tránh lỗi
            $columns = ['backlog' => [], 'in_progress' => [], 'review' => [], 'done' => []];
            if (is_array($tasks) || is_object($tasks)) {
                foreach ($tasks as $task) {
                    if (isset($task['status'])) { $columns[$task['status']][] = $task; }
                }
            }
            ?>
            <div class="kanban-board">
                <?php foreach ($columns as $status => $tasks_in_column): ?>
                    <div class="kanban-column" id="col-<?php echo $status; ?>">
                        <h3><?php echo ucfirst(str_replace('_', ' ', $status)); ?></h3>
                        <?php if (is_array($tasks_in_column)): ?>
                            <?php foreach ($tasks_in_column as $task): ?>
                                <div class="task-card" data-task-id="<?php echo $task['task_id']; ?>">
                                    <strong><?php echo htmlspecialchars($task['task_title']); ?></strong>
                                    <p class="priority priority-<?php echo $task['priority']; ?>"><?php echo ucfirst($task['priority']); ?></p>
                                    <p>Giao cho: <?php echo htmlspecialchars($task['assignee_name'] ?? 'Chưa có'); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
            <div class="function-placeholder" style="flex: 1;">
                <h2>Báo cáo & Thống kê</h2>
                <p>Xem biểu đồ tiến độ và điểm đóng góp của nhóm.</p>
                <a href="index.php?page=group_report&group_id=<?php echo $group['group_id']; ?>" class="btn">
                    Xem Báo Cáo
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
            <h3>Tài liệu đính kèm</h3>
            <ul id="modal-task-files">
                </ul>
            
            <form action="index.php?action=attach_file_to_task" method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px;">
                <input type="hidden" name="task_id" id="modal-file-task-id" value="">
                <input type="hidden" name="group_id" id="modal-file-group-id" value="<?php echo $group['group_id']; ?>">
                
                <input type="file" name="task_file" required style="flex: 1;">
                <button type="submit" class="btn btn-small">Đính kèm</button>
            </form>

            <hr>
            <h3>Bình luận</h3>
            <div id="task-details-comments">
                </div>
            <form id="add-comment-form">
                <input type="hidden" id="modal-comment-task-id" name="task_id">
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
            
            // --- 1. PHẦN KÉO-THẢ (Giữ nguyên) ---
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
                    if (!data.success) { console.error('Lỗi cập nhật trạng thái:', data.message); }
                })
                .catch(error => console.error('Lỗi fetch:', error));
            }

            // --- 2. PHẦN MODAL (ĐÃ CẬP NHẬT) ---
            const modal = document.getElementById('task-details-modal');
            const closeModalBtn = document.getElementById('modal-close-btn');
            
            if (modal) {
                document.querySelectorAll('.task-card').forEach(card => {
                    card.addEventListener('click', function() {
                        const taskId = this.dataset.taskId;
                        openTaskModal(taskId);
                    });
                });
                
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
                    fetch('index.php?action=add_task_comment', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const commentsContainer = document.getElementById('task-details-comments');
                            if (commentsContainer.querySelector('p')) { commentsContainer.innerHTML = ''; }
                            appendComment(data.comment, commentsContainer);
                            commentTextEl.value = '';
                        } else {
                            alert('Lỗi gửi bình luận: ' + data.message);
                        }
                    });
                });
            }

            // HÀM MỞ MODAL (ĐÃ CẬP NHẬT ĐỂ LẤY FILE)
            function openTaskModal(taskId) {
                fetch(`index.php?action=get_task_details&task_id=${taskId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const task = data.task;
                            
                            // Đổ dữ liệu Task
                            document.getElementById('modal-task-title').textContent = task.task_title;
                            document.getElementById('modal-task-assignee').textContent = task.assignee_name || 'Chưa có';
                            document.getElementById('modal-task-creator').textContent = task.creator_name || 'N/A';
                            document.getElementById('modal-task-priority').textContent = task.priority;
                            document.getElementById('modal-task-due-date').textContent = task.due_date || 'N/A';
                            document.getElementById('modal-task-points').textContent = task.points;
                            document.getElementById('modal-task-description').textContent = task.task_description || 'Không có mô tả.';
                            
                            // Cập nhật input ẩn cho form Comment VÀ form File
                            document.getElementById('modal-comment-task-id').value = task.task_id;
                            document.getElementById('modal-file-task-id').value = task.task_id;

                            // Đổ dữ liệu Files (MỚI)
                            const filesContainer = document.getElementById('modal-task-files');
                            filesContainer.innerHTML = ''; // Xóa file cũ
                            if (data.files && data.files.length > 0) {
                                data.files.forEach(file => {
                                    const fileEl = document.createElement('li');
                                    fileEl.innerHTML = `<a href="${file.file_path}" target="_blank">${file.file_name}</a>`;
                                    filesContainer.appendChild(fileEl);
                                });
                            } else {
                                filesContainer.innerHTML = '<li>Chưa có file nào.</li>';
                            }
                            
                            // Đổ dữ liệu Comments
                            const commentsContainer = document.getElementById('task-details-comments');
                            commentsContainer.innerHTML = ''; // Xóa comment cũ
                            if (data.comments && data.comments.length > 0) {
                                data.comments.forEach(c => appendComment(c, commentsContainer));
                            } else {
                                commentsContainer.innerHTML = '<p>Chưa có bình luận nào.</p>';
                            }

                            // Hiển thị Modal
                            modal.style.display = 'block';
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    });
            }

            // Hàm trợ giúp: thêm HTML của 1 comment
            function appendComment(comment, container) {
                const commentEl = document.createElement('div');
                commentEl.className = 'comment';
                commentEl.innerHTML = `
                    <p><strong>${comment.commenter_name}:</strong> ${comment.comment_text}</p>
                    <small>${new Date(comment.created_at).toLocaleString('vi-VN')}</small>
                `;
                container.appendChild(commentEl);
            }

            // --- 3. PHẦN TỰ CUỘN CHAT (Giữ nguyên) ---
            const chatBox = document.getElementById('chat-box');
            if (chatBox) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });
    </script>
</body>
</html>