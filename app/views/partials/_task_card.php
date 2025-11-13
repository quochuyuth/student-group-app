<?php
// Tệp: app/views/partials/_task_card.php
// Đây là HTML cho 1 cái thẻ Task Card trên bảng Kanban
// Biến $task được truyền từ file group_details.php
?>
<div class="card shadow-sm mb-2 task-card" data-task-id="<?php echo $task['task_id']; ?>" style="cursor: grab;">
    <div class="card-body p-2">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="font-weight-bold text-gray-800 small"><?php echo htmlspecialchars($task['task_title']); ?></span>
            
            <span class="task-card-priority priority-<?php echo $task['priority']; ?>">
                <?php echo ucfirst($task['priority']); ?>
            </span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <img class="img-profile rounded-circle" 
                 src="public/img/undraw_profile_2.svg" 
                 style="width: 20px; height: 20px;"
                 title="Giao cho: <?php echo htmlspecialchars($task['assignee_name'] ?? 'Chưa có'); ?>">
            
            <?php if (!empty($task['due_date'])): ?>
            <span class="small text-danger">
                <i class="fas fa-calendar-alt"></i> <?php echo date('d/m', strtotime($task['due_date'])); ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
</div>