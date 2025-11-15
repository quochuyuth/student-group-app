<?php
// Tệp: app/views/group_meetings.php (ĐÃ THÊM NÚT VÀO HỌP)

// 1. Gọi Header
require 'app/views/layout/header.php'; 

// Các biến $group và $meetings đã được MeetingController tải
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">📅 Quản lý Họp nhóm: <?php echo htmlspecialchars($group['group_name']); ?></h1>
    <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại nhóm
    </a>
</div>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-success shadow-sm mb-4">
        <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<div class="row">

    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle"></i> Tạo cuộc họp mới</h6>
            </div>
            <div class="card-body">
                <form action="index.php?action=create_meeting" method="POST">
                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                    
                    <div class="form-group">
                        <label for="meeting_title">Tiêu đề cuộc họp:</label>
                        <input type="text" class="form-control" id="meeting_title" name="meeting_title" required>
                    </div>

                    <div class="form-group">
                        <label for="start_time">Thời gian bắt đầu:</label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                    </div>

                    <div class="form-group">
                        <label for="agenda">Nội dung (Agenda):</label>
                        <textarea class="form-control" id="agenda" name="agenda" rows="5" placeholder="Gạch đầu dòng các nội dung cần thảo luận..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-icon-split">
                        <span class="icon text-white-50"><i class="fas fa-calendar-plus"></i></span>
                        <span class="text">Tạo Lịch họp</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ul"></i> Danh sách các cuộc họp</h6>
            </div>
            <div class="card-body">
                <?php if (empty($meetings)): ?>
                    <p class="text-muted text-center mt-3">Chưa có cuộc họp nào được đặt.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($meetings as $meeting): ?>
                            <div class="list-group-item list-group-item-action flex-column align-items-start mb-2 shadow-sm border-left-info">
                                
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 text-primary"><?php echo htmlspecialchars($meeting['meeting_title']); ?></h5>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($meeting['start_time'])); ?></small>
                                </div>
                                <p class="mb-1 text-gray-700">
                                    Nội dung: <?php echo htmlspecialchars(substr($meeting['agenda'], 0, 100)) . '...'; ?>
                                </p>
                                <small class="text-muted">Người tạo: <?php echo htmlspecialchars($meeting['creator_name']); ?></small>
                                
                                <div class="mt-2 text-right">
                                    <a href="index.php?page=join_meeting&id=<?php echo $meeting['meeting_id']; ?>" class="btn btn-success btn-sm" target="_blank">
                                        <i class="fas fa-video"></i> Vào họp
                                    </a>
                                    <a href="index.php?page=meeting_details&id=<?php echo $meeting['meeting_id']; ?>" class="btn btn-primary btn-sm ml-1">
                                        <i class="fas fa-file-alt"></i> Xem biên bản
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>