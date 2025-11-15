<?php
// Tệp: app/views/meeting_room.php (ĐÃ SỬA LỖI JITSI)

// 1. Gọi Header
require 'app/views/layout/header.php'; 

// Các biến $meeting và $group đã được MeetingController->joinMeeting() tải
// ($upcoming_tasks, $notification_count cũng được tải từ index.php)

// 2. Tạo tên phòng họp (Giữ nguyên)
$safe_group_name = preg_replace("/[^a-zA-Z0-9]/", "", $group['group_name']);
$room_name = $safe_group_name . "Meeting" . $meeting['meeting_id'];

// 3. (MỚI) Chuẩn bị tên và email của user cho Jitsi
$user_display_name = $_SESSION['username'] ?? 'Thành viên';
$user_email = $_SESSION['user_email'] ?? ''; // Giả sử bạn có lưu email trong session

?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-video"></i> Phòng họp: <?php echo htmlspecialchars($meeting['meeting_title']); ?></h1>
    <a href="index.php?page=group_meetings&group_id=<?php echo $meeting['group_id']; ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
    </a>
</div>

<div class="card shadow mb-4" style="height: 75vh;"> 
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Phòng họp trực tuyến</h6>
    </div>
    <div class="card-body p-0">
        
        <iframe 
            allow="camera; microphone; fullscreen; display-capture; autoplay" 
            style="height: 100%; width: 100%; border: 0;"
            
            src="https://meet.jit.si/<?php echo htmlspecialchars($room_name); ?>#config.prejoinPageEnabled=false&userInfo.displayName='<?php echo urlencode($user_display_name); ?>'&userInfo.email='<?php echo urlencode($user_email); ?>'">
        </iframe>
        
    </div>
</div>

<?php
// 4. Gọi Footer
require 'app/views/layout/footer.php'; 
?>