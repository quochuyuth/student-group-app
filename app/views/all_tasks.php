<?php
// Tệp: app/views/all_tasks.php (FILE MỚI)
require 'app/views/layout/header.php'; 

// $page_title và $tasks đã được TaskController->showAllTasks() cung cấp
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tasks"></i> <?php echo htmlspecialchars($page_title); ?></h1>
    <a href="index.php?page=dashboard" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại Bảng điều khiển
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách công việc</h6>
    </div>
    <div class="card-body">
        <?php if (empty($tasks)): ?>
            <p class="text-muted text-center mt-3">Bạn không có công việc nào trong danh mục này.</p>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($tasks as $task): ?>
                    <?php
                        // Xác định màu sắc dựa trên trạng thái
                        $status_color = 'secondary'; // Mặc định (Backlog)
                        if ($task['status'] == 'in_progress') $status_color = 'info';
                        if ($task['status'] == 'review') $status_color = 'warning';
                        if ($task['status'] == 'done') $status_color = 'success';
                    ?>
                    <!-- Link đến trang group_details (Kanban) -->
                    <a href="index.php?page=group_details&id=<?php echo $task['group_id']; ?>" 
                       class="list-group-item list-group-item-action flex-column align-items-start mb-2 shadow-sm border-left-<?php echo $status_color; ?>">
                        
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 text-primary"><?php echo htmlspecialchars($task['task_title']); ?></h5>
                            <span class="badge badge-<?php echo $status_color; ?> p-2"><?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?></span>
                        </div>
                        <p class="mb-1 text-gray-700">
                            <strong>Nhóm:</strong> <?php echo htmlspecialchars($task['group_name']); ?>
                        </p>
                        <small class="text-muted">
                            Ưu tiên: <?php echo ucfirst($task['priority']); ?>
                            <?php if (!empty($task['due_date'])): ?>
                                | Hết hạn: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?>
                            <?php endif; ?>
                        </small>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>