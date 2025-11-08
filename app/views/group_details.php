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

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="public/css/group_details.css">
</head>
<body>
    <div class="bg-overlay"></div>

    <header class="topbar">
        <div class="topbar-left">
            <h1 class="group-title"><?php echo htmlspecialchars($group['group_name']); ?></h1>
            <p class="group-sub">Trang quản lý nhóm — collaborative</p>
        </div>
        <nav class="topnav">
            <a href="index.php?page=dashboard">🏠 Trang chủ</a>
            <a href="index.php?page=profile">👤 Hồ sơ</a>
            <a href="index.php?page=groups">👥 Nhóm</a>
            <a href="index.php?page=groups">📚 Danh sách nhóm</a>
            <a href="index.php?action=logout" class="logout">🚪 Đăng xuất</a>
        </nav>
    </header>

    <main class="container">
        <?php
        if (isset($_SESSION['flash_message'])) {
            echo '<div class="flash-message">' . $_SESSION['flash_message'] . '</div>';
            unset($_SESSION['flash_message']);
        }
        ?>

        <section class="top-grid">
             <div class="card invite-card">
                <h2>Mời thành viên mới</h2>
                <form action="index.php?action=invite_member" method="POST" class="form-inline">
                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                    <label for="email_or_username" class="sr-only">Email hoặc Username</label>
                    <input type="text" id="email_or_username" name="email_or_username" placeholder="Nhập email hoặc username..." />
                    <button type="submit" class="btn">Gửi Lời Mời</button>
                </form>
            </div>

            <div class="card task-create-card">
                <h2>Tạo công việc mới</h2>
                <form action="index.php?action=create_task" method="POST" class="task-form">
                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                    <div class="row">
                        <div class="col">
                            <label for="task_title">Tiêu đề</label>
                            <input type="text" id="task_title" name="task_title" required>
                        </div>
                        <div class="col">
                            <label for="priority">Ưu tiên</label>
                            <select id="priority" name="priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <label for="task_description">Mô tả</label>
                            <textarea id="task_description" name="task_description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="assigned_to_user_id">Giao cho</label>
                            <select id="assigned_to_user_id" name="assigned_to_user_id">
                                <option value="">-- Không giao --</option>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?php echo $member['user_id']; ?>"><?php echo htmlspecialchars($member['username']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <label for="due_date">Hết hạn</label>
                            <input type="date" id="due_date" name="due_date">
                        </div>
                        <div class="col">
                            <label for="points">Điểm</label>
                            <input type="number" id="points" name="points" value="0" min="0">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Tạo Task</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="mid-grid">
            
            <div class="card chat-card" id="chat">
                <div class="card-head">
                    <div class="chat-head-left">
                        <h2>Chat Nhóm</h2>
                        <small class="muted">Realtime / chia sẻ file</small>
                    </div>
                    </div>

                <div id="chat-box" class="chat-box">
                    <?php if (empty($messages)): ?>
                        <p class="muted">Chưa có tin nhắn nào.</p>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <?php 
                            $isUserClass = ($msg['sender_user_id'] == $_SESSION['user_id']) ? 'is-user' : ''; 
                            $isFileClass = !empty($msg['file_id']) ? 'is-file' : '';
                            $isPollClass = !empty($msg['poll_id']) ? 'is-poll' : '';
                            ?>
                            <div class="chat-message <?php echo $isUserClass; ?> <?php echo $isFileClass; ?> <?php echo $isPollClass; ?>">
                                <div class="chat-meta">
                                    <div class="avatar"><?php echo strtoupper(substr($msg['sender_name'],0,1)); ?></div>
                                    <div class="meta-text">
                                        <strong><?php echo htmlspecialchars($msg['sender_name']); ?></strong>
                                        <small class="time"><?php echo date('d/m H:i', strtotime($msg['created_at'])); ?></small>
                                    </div>
                                </div>

                                <div class="chat-body">
                                    <?php if (!empty($msg['file_id'])): ?>
                                        <p class="file-line">Đã gửi một file: <a href="<?php echo htmlspecialchars($msg['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($msg['file_name']); ?></a></p>
                                    
                                    <?php elseif (!empty($msg['poll_id'])): ?>
                                        <?php
                                        $current_poll = null;
                                        foreach ($polls as $poll) {
                                            if ($poll['poll_id'] == $msg['poll_id']) {
                                                $current_poll = $poll;
                                                break;
                                            }
                                        }
                                        ?>
                                        <?php if ($current_poll): ?>
                                            <div class="poll-container-in-chat">
                                                <div class="poll-head">
                                                    <strong><?php echo htmlspecialchars($current_poll['poll_question']); ?></strong>
                                                    <small class="muted">(Bởi: <?php echo htmlspecialchars($current_poll['creator_name']); ?>)</small>
                                                </div>
                                                <form action="index.php?action=submit_vote" method="POST">
                                                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                                    <input type="hidden" name="poll_id" value="<?php echo $current_poll['poll_id']; ?>">
                                                    <?php 
                                                    $total_votes = 0;
                                                    foreach ($current_poll['options'] as $opt) { $total_votes += $opt['vote_count']; }
                                                    ?>
                                                    <?php foreach ($current_poll['options'] as $option): ?>
                                                        <?php 
                                                        $user_voted_this = ($user_votes[$current_poll['poll_id']] ?? 0) == $option['option_id'];
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
                                                    <div style="margin-top:8px;">
                                                        <button type="submit" class="btn btn-small">Bầu chọn</button>
                                                    </div>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p><?php echo htmlspecialchars($msg['message_content']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div id="chat-controls" class="chat-controls">
                    <form id="hidden-file-form" action="index.php?action=send_file" method="POST" enctype="multipart/form-data" style="display: none;">
                        <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                        <input type="file" name="group_file" id="group_file_input" onchange="this.form.submit()" title="Đính kèm file">
                    </form>

                    <button id="file-upload-btn" class="btn-icon" onclick="document.getElementById('group_file_input').click();" title="Đính kèm file">📎</button>
                    
                    <button id="create-poll-btn" class="btn-icon" title="Tạo bình chọn">📊</button>

                    <form id="chat-form" action="index.php?action=send_message" method="POST" class="chat-form">
                        <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                        <input type="text" name="message_content" placeholder="Gõ tin nhắn của bạn..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary">Gửi</button>
                    </form>
                </div>
            </div>

            <aside class="card info-sidebar" id="info-sidebar">
                <div class="info-sidebar-header">
                    <h3>Thông tin hội thoại</h3>
                    </div>
                <div class="info-sidebar-content">
                    <h4>Thành viên (<?php echo count($members); ?>)</h4>
                    <ul class="member-list">
                        <?php foreach ($members as $member): ?>
                            <li>
                                <a href="index.php?page=profile&id=<?php echo $member['user_id']; ?>" class="member-link">
                                    <div class="avatar"><?php echo strtoupper(substr($member['username'],0,1)); ?></div>
                                    <span><?php echo htmlspecialchars($member['username']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    </div>
            </aside>

        </section>

        <section class="kanban-section">
             <h2>Bảng công việc (Kanban)</h2>
            <?php
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
                                    <div class="task-head">
                                        <strong><?php echo htmlspecialchars($task['task_title']); ?></strong>
                                        <span class="priority priority-<?php echo $task['priority']; ?>"><?php echo ucfirst($task['priority']); ?></span>
                                    </div>
                                    <p class="task-meta">Giao cho: <?php echo htmlspecialchars($task['assignee_name'] ?? 'Chưa có'); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="quick-links">
             <div class="card">
                <h3>Đánh giá thành viên</h3>
                <p>Đánh giá hiệu suất của các thành viên trong nhóm.</p>
                <a href="index.php?page=group_rubric&group_id=<?php echo $group['group_id']; ?>" class="btn">Đi đến trang Đánh giá</a>
            </div>
            <div class="card">
                <h3>Họp nhóm</h3>
                <p>Nơi đặt lịch họp và xem biên bản họp.</p>
                <a href="index.php?page=group_meetings&group_id=<?php echo $group['group_id']; ?>" class="btn">Quản lý Họp</a>
            </div>
            <div class="card">
                <h3>Báo cáo & Thống kê</h3>
                <p>Xem biểu đồ tiến độ và điểm đóng góp của nhóm.</p>
                <a href="index.php?page=group_report&group_id=<?php echo $group['group_id']; ?>" class="btn">Xem Báo Cáo</a>
            </div>
        </section>

    </main>

    <div id="create-poll-modal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <span class="modal-close" id="close-poll-modal-btn">&times;</span>
            <h2>Tạo bình chọn mới</h2>
            <form action="index.php?action=create_poll" method="POST" id="create-poll-form">
                <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                <div class="form-group">
                    <label for="poll_question_modal">Câu hỏi</label>
                    <input type="text" name="poll_question" id="poll_question_modal" required>
                </div>
                <div class="form-group poll-options-modal">
                    <label>Các lựa chọn</label>
                    <input type="text" name="options[]" placeholder="Lựa chọn 1" required>
                    <input type="text" name="options[]" placeholder="Lựa chọn 2">
                    <input type="text" name="options[]" placeholder="Lựa chọn 3">
                </div>
                <button type="submit" class="btn">Tạo Bình Chọn</button>
            </form>
        </div>
    </div>

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
            <ul id="modal-task-files"></ul>
            <form action="index.php?action=attach_file_to_task" method="POST" enctype="multipart/form-data" class="file-attach-form">
                <input type="hidden" name="task_id" id="modal-file-task-id" value="">
                <input type="hidden" name="group_id" id="modal-file-group-id" value="<?php echo $group['group_id']; ?>">
                <input type="file" name="task_file" required>
                <button type="submit" class="btn btn-small">Đính kèm</button>
            </form>
            <hr>
            <h3>Bình luận</h3>
            <div id="task-details-comments" class="comments-box"></div>
            <form id="add-comment-form" class="comment-form">
                <input type="hidden" id="modal-comment-task-id" name="task_id">
                <div class="form-group">
                    <label for="comment_text" class="sr-only">Viết bình luận</label>
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

            // --- 2. PHẦN MODAL TASK (Giữ nguyên) ---
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
                /* (BỎ SỰ KIỆN window.onclick CŨ) */

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

            // HÀM MỞ MODAL TASK (Giữ nguyên)
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
                            
                            document.getElementById('modal-comment-task-id').value = task.task_id;
                            document.getElementById('modal-file-task-id').value = task.task_id;

                            const filesContainer = document.getElementById('modal-task-files');
                            filesContainer.innerHTML = '';
                            if (data.files && data.files.length > 0) {
                                data.files.forEach(file => {
                                    const fileEl = document.createElement('li');
                                    fileEl.innerHTML = `<a href="${file.file_path}" target="_blank">${file.file_name}</a>`;
                                    filesContainer.appendChild(fileEl);
                                });
                            } else {
                                filesContainer.innerHTML = '<li>Chưa có file nào.</li>';
                            }
                            
                            const commentsContainer = document.getElementById('task-details-comments');
                            commentsContainer.innerHTML = '';
                            if (data.comments && data.comments.length > 0) {
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

            // Hàm trợ giúp: thêm HTML của 1 comment (Giữ nguyên)
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

            // --- 4. (XÓA BỎ) LOGIC CHO SIDEBAR THÔNG TIN ---
            /* (Toàn bộ code JS cho toggleBtn, closeInfoBtn đã bị xóa) */

            // --- 5. (SỬA LẠI) LOGIC CHO MODAL TẠO POLL ---
            const pollModal = document.getElementById('create-poll-modal');
            const openPollBtn = document.getElementById('create-poll-btn');
            const closePollBtn = document.getElementById('close-poll-modal-btn');

            if (pollModal && openPollBtn && closePollBtn) {
                openPollBtn.addEventListener('click', (e) => {
                    e.preventDefault(); 
                    pollModal.style.display = 'block';
                });
                
                closePollBtn.addEventListener('click', () => {
                    pollModal.style.display = 'none';
                });

                // (SỬA LẠI) Đóng modal khi click ra ngoài
                window.addEventListener('click', (event) => {
                    if (event.target == pollModal) {
                        pollModal.style.display = 'none';
                    }
                    // Đóng modal task detail (giữ nguyên)
                    if (event.target == modal) {
                        modal.style.display = 'none';
                    }
                });
            }

        });
    </script>
</body>
</html>