<?php
// Tệp: app/views/meeting_details.php (Bản HOÀN THIỆN với SB Admin 2)

// 1. Gọi Header
require 'app/views/layout/header.php'; 

// Các biến $meeting và $user_rating đã được MeetingController tải
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Chi tiết cuộc họp</h1>
    <a href="index.php?page=group_meetings&group_id=<?php echo $meeting['group_id']; ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại Danh sách họp
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
                <h6 class="m-0 font-weight-bold text-primary"><?php echo htmlspecialchars($meeting['meeting_title']); ?></h6>
            </div>
            <div class="card-body">
                <p>
                    <strong><i class="fas fa-calendar-alt mr-2"></i>Thời gian:</strong> 
                    <?php echo date('d/m/Y H:i', strtotime($meeting['start_time'])); ?>
                </p>
                <p>
                    <strong><i class="fas fa-user mr-2"></i>Người tạo:</strong> 
                    <?php echo htmlspecialchars($meeting['creator_name']); ?>
                </p>
                <hr>
                <strong><i class="fas fa-list-alt mr-2"></i>Nội dung (Agenda):</strong>
                <pre class="bg-light p-3 rounded mt-2" style="white-space: pre-wrap; font-family: inherit;"><?php echo htmlspecialchars($meeting['agenda']); ?></pre>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-star"></i> Đánh giá của bạn</h6>
            </div>
            <div class="card-body text-center">
                <p>Bạn cảm thấy cuộc họp này hiệu quả ở mức nào?</p>
                <form action="index.php?action=submit_meeting_rating" method="POST">
                    <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
                    <div class="rating-stars">
                        <?php for ($i=5; $i>=1; $i--): ?>
                            <input type="radio" id="star<?php echo $i; ?>" name="satisfaction_rating" value="<?php echo $i; ?>" <?php echo ($user_rating == $i) ? 'checked' : ''; ?> required>
                            <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> sao">&#9733;</label>
                        <?php endfor; ?>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm mt-3">
                        <i class="fas fa-paper-plane"></i> Gửi Đánh Giá
                    </button>
                </form>
            </div>
        </div>

    </div>

    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit"></i> Biên bản họp (Minutes)</h6>
            </div>
            <div class="card-body">
                <form action="index.php?action=save_minutes" method="POST">
                    <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
                    
                    <div class="form-group">
                        <label for="minutes">Nội dung đã diễn ra:</label>
                        <textarea class="form-control" id="minutes" name="minutes" rows="10"><?php echo htmlspecialchars($meeting['minutes'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="action_items">Việc cần làm sau họp (Action Items):</label>
                        <textarea class="form-control" id="action_items" name="action_items" rows="5"><?php echo htmlspecialchars($meeting['action_items'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-icon-split">
                        <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                        <span class="text">Lưu Biên Bản</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.rating-stars { 
    display:flex; 
    flex-direction:row-reverse; 
    justify-content:center; 
}
.rating-stars input[type="radio"] { display:none; }
.rating-stars label {
    font-size: 2.5em; /* To hơn 1 chút */
    color: #e0e0e0; /* Màu xám nhạt */
    cursor: pointer;
    padding: 0 3px;
    transition: 0.2s;
}
.rating-stars input[type="radio"]:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label { 
    color: #f5c518; /* Màu vàng */
}
</style>

<?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>