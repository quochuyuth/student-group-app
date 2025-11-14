<?php
// Tệp: app/views/group_details.php (ĐÃ TÁCH CHAT - Trang Kanban)

// 1. Gọi Header
require 'app/views/layout/header.php'; 

// Các biến $group, $tasks, $members được GroupController->show() tải
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-users-cog"></i> <?php echo htmlspecialchars($group['group_name']); ?></h1>
    <a href="index.php?page=groups" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại Danh sách nhóm
    </a>
</div>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-info shadow-sm mb-4" id="flashMessage">
        <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>


<div class="row">
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-plus"></i> Mời thành viên mới</h6>
            </div>
            <div class="card-body">
                <form action="index.php?action=invite_member" method="POST">
                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                    <div class="form-group">
                        <label for="email_or_username">Email hoặc Username:</label>
                        <input type="text" class="form-control" id="email_or_username" name="email_or_username" placeholder="Nhập email hoặc username..." required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-icon-split">
                        <span class="icon text-white-50"><i class="fas fa-paper-plane"></i></span>
                        <span class="text">Gửi Lời Mời</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle"></i> Tạo công việc mới</h6>
            </div>
            <div class="card-body">
                <form action="index.php?action=create_task" method="POST">
                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="task_title">Tiêu đề</label>
                            <input type="text" class="form-control" id="task_title" name="task_title" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="priority">Ưu tiên</label>
                            <select id="priority" name="priority" class="form-control">
                                <option value="low">Thấp</option>
                                <option value="medium" selected>Trung bình</option>
                                <option value="high">Cao</option>
                                <option value="critical">Khẩn cấp</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="task_description">Mô tả (ngắn)</label>
                        <textarea id="task_description" name="task_description" rows="1" class="form-control"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="assigned_to_user_id">Giao cho</label>
                            <select id="assigned_to_user_id" name="assigned_to_user_id" class="form-control">
                                <option value="">-- Không giao --</option>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?php echo $member['user_id']; ?>"><?php echo htmlspecialchars($member['username']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="due_date">Hết hạn</label>
                            <input type="date" class="form-control" id="due_date" name="due_date">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="points">Điểm</label>
                            <input type="number" class="form-control" id="points" name="points" value="0" min="0">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Tạo Task</button>
                </form>
            </div>
        </div>
    </div>
</div> 

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tasks"></i> Bảng công việc (Kanban)</h6>
    </div>
    <div class="card-body bg-light">
        <?php
        $columns = ['backlog' => [], 'in_progress' => [], 'review' => [], 'done' => []];
        if (is_array($tasks)) {
            foreach ($tasks as $task) {
                if (isset($task['status'])) { $columns[$task['status']][] = $task; }
            }
        }
        ?>
        <div class="row">
            <div class="col-lg-3">
                <div class="card bg-gray-100 border-bottom-secondary">
                    <div class="card-header bg-gray-300 py-3"><h6 class="m-0 font-weight-bold text-dark">Backlog</h6></div>
                    <div class="card-body kanban-column" id="col-backlog">
                        <?php foreach ($columns['backlog'] as $task): ?>
                            <?php include 'app/views/partials/_task_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card bg-gray-100 border-bottom-primary">
                    <div class="card-header bg-primary py-3"><h6 class="m-0 font-weight-bold text-white">In Progress</h6></div>
                    <div class="card-body kanban-column" id="col-in_progress">
                        <?php foreach ($columns['in_progress'] as $task): ?>
                            <?php include 'app/views/partials/_task_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card bg-gray-100 border-bottom-warning">
                    <div class="card-header bg-warning py-3"><h6 class="m-0 font-weight-bold text-white">Review</h6></div>
                    <div class="card-body kanban-column" id="col-review">
                        <?php foreach ($columns['review'] as $task): ?>
                            <?php include 'app/views/partials/_task_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card bg-gray-100 border-bottom-success">
                    <div class="card-header bg-success py-3"><h6 class="m-0 font-weight-bold text-white">Done</h6></div>
                    <div class="card-body kanban-column" id="col-done">
                        <?php foreach ($columns['done'] as $task): ?>
                            <?php include 'app/views/partials/_task_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 

<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Giao tiếp</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Chat Nhóm</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-comments fa-2x text-gray-300"></i></div>
                </div>
                <a href="index.php?page=group_chat&id=<?php echo $group['group_id']; ?>" class="stretched-link"></a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Đánh giá</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rubric Nhóm</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-star-half-alt fa-2x text-gray-300"></i></div>
                </div>
                <a href="index.php?page=group_rubric&group_id=<?php echo $group['group_id']; ?>" class="stretched-link"></a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                 <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Lịch trình</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Họp Nhóm</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-calendar-alt fa-2x text-gray-300"></i></div>
                </div>
                <a href="index.php?page=group_meetings&group_id=<?php echo $group['group_id']; ?>" class="stretched-link"></a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Thống kê</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Báo cáo</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-chart-bar fa-2x text-gray-300"></i></div>
                </div>
                <a href="index.php?page=group_report&group_id=<?php echo $group['group_id']; ?>" class="stretched-link"></a>
            </div>
        </div>
    </div>
    </div> 

<div class="modal fade" id="taskDetailsModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">Chi tiết Task</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal-task-content">
                    <p class="text-center">Đang tải...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // --- 1. KÉO-THẢ KANBAN (Giữ nguyên) ---
    const columnIds = ['col-backlog', 'col-in_progress', 'col-review', 'col-done'];
    columnIds.forEach(colId => {
        const column = document.getElementById(colId);
        if (column) {
            new Sortable(column, {
                group: 'kanban', 
                animation: 150,
                onEnd: function (evt) {
                    const taskId = evt.item.dataset.taskId;
                    const newStatus = evt.to.id.replace('col-', '');
                    
                    fetch('index.php?action=update_task_status', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ task_id: taskId, new_status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) { 
                            console.error('Lỗi cập nhật trạng thái:', data.message); 
                        }
                    })
                    .catch(error => console.error('Lỗi fetch:', error));
                }
            });
        }
    });

    // --- 2. MODAL CHI TIẾT TASK (Giữ nguyên) ---
    $('.task-card').on('click', function() {
        const taskId = $(this).data('task-id');
        const modalContent = $('#modal-task-content');
        
        $('#taskDetailsModal').modal('show');
        modalContent.html('<p class="text-center">Đang tải chi tiết task...</p>');

        fetch(`index.php?action=get_task_details&task_id=${taskId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const task = data.task;
                    let filesHtml = '<p class="small text-muted">Chưa có file.</p>';
                    if (data.files && data.files.length > 0) {
                        filesHtml = '<ul class="list-group list-group-flush">';
                        data.files.forEach(file => {
                            filesHtml += `<li class="list-group-item py-1 px-0"><a href="${file.file_path}" target="_blank"><i class="fas fa-file"></i> ${file.file_name}</a></li>`;
                        });
                        filesHtml += '</ul>';
                    }

                    let commentsHtml = '<p class="small text-muted">Chưa có bình luận.</p>';
                    if (data.comments && data.comments.length > 0) {
                        commentsHtml = '';
                        data.comments.forEach(c => {
                            commentsHtml += `
                                <div class="mb-2">
                                    <strong>${c.commenter_name}:</strong>
                                    <p class="mb-0 bg-light p-2 rounded">${c.comment_text}</p>
                                    <small class="text-muted">${new Date(c.created_at).toLocaleString('vi-VN')}</small>
                                </div>`;
                        });
                    }

                    modalContent.html(`
                        <h4 class="text-primary">${task.task_title}</h4>
                        <p><strong>Giao cho:</strong> ${task.assignee_name || 'Chưa có'} | <strong>Người tạo:</strong> ${task.creator_name || 'N/A'}</p>
                        <p><strong>Ưu tiên:</strong> ${task.priority} | <strong>Hết hạn:</strong> ${task.due_date || 'N/A'} | <strong>Điểm:</strong> ${task.points}</p>
                        <hr>
                        <h6 class="font-weight-bold">Mô tả:</h6>
                        <p>${task.task_description || 'Không có mô tả.'}</p>
                        <hr>
                        
                        <h6 class="font-weight-bold">Tài liệu đính kèm</h6>
                        ${filesHtml}
                        <form action="index.php?action=attach_file_to_task" method="POST" enctype="multipart/form-data" class="form-inline mt-2">
                            <input type="hidden" name="task_id" value="${taskId}">
                            <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                            <input type="file" name="task_file" class="form-control-file form-control-sm" required>
                            <button type="submit" class="btn btn-primary btn-sm ml-2">Đính kèm</button>
                        </form>
                        <hr>

                        <h6 class="font-weight-bold">Bình luận</h6>
                        <div id="task-details-comments" class="mb-3" style="max-height: 150px; overflow-y: auto;">
                            ${commentsHtml}
                        </div>
                        <form id="add-comment-form">
                            <input type="hidden" name="task_id" value="${taskId}">
                            <div class="form-group">
                                <textarea name="comment_text" class="form-control" rows="2" placeholder="Viết bình luận..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Gửi</button>
                        </form>
                    `);
                } else {
                    modalContent.html('<p class="text-danger">Lỗi: Không thể tải chi tiết task.</p>');
                }
            });
    });

    // Xử lý submit Form Comment (AJAX)
    $('#modal-task-content').on('submit', '#add-comment-form', function(e) {
        e.preventDefault();
        const form = $(this);
        const formData = new FormData(this);
        
        fetch('index.php?action=add_task_comment', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const comment = data.comment;
                const commentsContainer = $('#task-details-comments');
                if (commentsContainer.find('.small.text-muted').length > 0) {
                    commentsContainer.html(''); // Xóa chữ "Chưa có bình luận"
                }
                commentsContainer.append(`
                    <div class="mb-2">
                        <strong>${comment.commenter_name}:</strong>
                        <p class="mb-0 bg-light p-2 rounded">${comment.comment_text}</p>
                        <small class="text-muted">${new Date(comment.created_at).toLocaleString('vi-VN')}</small>
                    </div>
                `);
                form.trigger('reset'); // Xóa text trong textarea
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
    });

    // --- (ĐÃ XÓA) Logic Chat scroll và Poll Modal ---
});
</script>

<style>
.kanban-column {
    min-height: 400px; 
    border-radius: 4px;
}
.task-card {
    /* Thẻ task card sẽ được tạo bởi file partial */
}
/* CSS cho Poll bên trong chat (giống code cũ) */
.poll-option { border: 1px solid #eee; }
.poll-option .custom-control-label::before,
.poll-option .custom-control-label::after {
    top: 0.4rem; /* Căn chỉnh lại radio button */
}
/* CSS cho task-card (từ file partial) */
.task-card-priority {
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 10px;
    color: #fff;
}
.priority-low { background-color: #1cc88a; } /* Success */
.priority-medium { background-color: #4e73df; } /* Primary */
.priority-high { background-color: #f6c23e; } /* Warning */
.priority-critical { background-color: #e74a3b; } /* Danger */
</style>

<?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>