<?php
// Tệp: app/views/group_chat.php (Trang CHAT MỚI)

// 1. Gọi Header
require 'app/views/layout/header.php'; 

// Các biến $group, $members, $messages, $polls, $user_votes, $chat_files
// đã được GroupController->showChat() tải
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-comments"></i> Chat Nhóm: <?php echo htmlspecialchars($group['group_name']); ?></h1>
    <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại trang chi tiết
    </a>
</div>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-info shadow-sm mb-4" id="flashMessage">
        <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>


<div class="row">

    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-comments"></i> Thảo luận</h6>
            </div>
            <div class="card-body">
                <div id="chat-box" class="border rounded p-3 bg-light" style="height: 450px; overflow-y: auto;">
                    <?php if (empty($messages)): ?>
                        <p class="text-muted text-center">Chưa có tin nhắn nào.</p>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <?php $isUserClass = ($msg['sender_user_id'] == $_SESSION['user_id']) ? 'bg-primary text-white ml-auto' : 'bg-white'; ?>
                            <?php $isPollClass = !empty($msg['poll_id']) ? 'bg-light border-primary' : ''; ?>
                            
                            <div class="card mb-2 shadow-sm <?php echo $isUserClass; ?> <?php echo $isPollClass; ?>" style="max-width: 85%;">
                                <div class="card-body py-2 px-3">
                                    <strong class="d-block"><?php echo htmlspecialchars($msg['sender_name']); ?>:</strong>
                                    
                                    <?php if (!empty($msg['file_id'])): ?>
                                        <p class="mb-0">Đã gửi một file: 
                                            <a class="<?php echo ($msg['sender_user_id'] == $_SESSION['user_id']) ? 'text-white' : 'text-primary'; ?>" 
                                               href="<?php echo htmlspecialchars($msg['file_path']); ?>" target="_blank">
                                                <i class="fas fa-file-download"></i> <?php echo htmlspecialchars($msg['file_name']); ?>
                                            </a>
                                        </p>
                                    <?php elseif (!empty($msg['poll_id'])): ?>
                                        <?php
                                        $current_poll = null;
                                        foreach ($polls as $poll) {
                                            if ($poll['poll_id'] == $msg['poll_id']) { $current_poll = $poll; break; }
                                        }
                                        ?>
                                        <?php if ($current_poll): ?>
                                            <div class="poll-container-in-chat mt-2">
                                                <strong><?php echo htmlspecialchars($current_poll['poll_question']); ?></strong>
                                                <form action="index.php?action=submit_vote" method="POST" class="mt-2">
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
                                                        <div class="poll-option position-relative small my-1">
                                                            <div class="vote-bar bg-info" style="width: <?php echo $vote_percent; ?>%; height: 100%; position: absolute; left: 0; top: 0; opacity: 0.2;"></div>
                                                            <div class="custom-control custom-radio position-relative p-2">
                                                                <input type="radio" id="opt-<?php echo $option['option_id']; ?>" name="option_id" value="<?php echo $option['option_id']; ?>" class="custom-control-input" <?php echo $user_voted_this ? 'checked' : ''; ?> required>
                                                                <label class="custom-control-label" for="opt-<?php echo $option['option_id']; ?>">
                                                                    <?php echo htmlspecialchars($option['option_text']); ?> 
                                                                    <span class="text-muted">(<?php echo $option['vote_count']; ?>)</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <button type="submit" class="btn btn-primary btn-sm mt-2">Bầu chọn</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($msg['message_content'])); ?></p>
                                    <?php endif; ?>
                                    <small class="d-block text-right opacity-75 mt-1" style="font-size: 0.75rem;"><?php echo date('d/m H:i', strtotime($msg['created_at'])); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="mt-3">
                    <form id="chat-form" action="index.php?action=send_message" method="POST" class="d-flex">
                        <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                        <input type="text" id="chat-message-input" name="message_content" class="form-control" placeholder="Gõ tin nhắn của bạn..." required autocomplete="off">
                        
                        <button type="button" class="btn btn-info ml-2" id="create-poll-btn" title="Tạo bình chọn" data-toggle="modal" data-target="#createPollModal">
                            <i class="fas fa-poll"></i>
                        </button>
                        <button type="button" class="btn btn-secondary ml-2" id="file-upload-btn" title="Đính kèm file" onclick="document.getElementById('group_file_input').click();">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-paper-plane"></i></button>
                    </form>
                    <form id="hidden-file-form" action="index.php?action=send_file" method="POST" enctype="multipart/form-data" class="d-none">
                        <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                        <input type="file" name="group_file" id="group_file_input" onchange="this.form.submit()">
                    </form>
                </div>
            </div>
        </div>
    </div> 

    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle"></i> Thông tin hội thoại</h6>
            </div>
            <div class="accordion" id="infoSidebarAccordion">
                <div class="card mb-0">
                    <div class="card-header py-3" id="headingMembers">
                        <h6 class="mb-0">
                            <button class="btn btn-link btn-block text-left text-dark font-weight-bold" type="button" data-toggle="collapse" data-target="#collapseMembers" aria-expanded="true" aria-controls="collapseMembers">
                                Thành viên (<?php echo count($members); ?>)
                            </button>
                        </h6>
                    </div>
                    <div id="collapseMembers" class="collapse show" aria-labelledby="headingMembers" data-parent="#infoSidebarAccordion">
                        <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($members as $member): ?>
                                    <li class="list-group-item d-flex align-items-center p-1">
                                        <img class="img-profile rounded-circle" src="public/img/undraw_profile.svg" style="width: 30px; height: 30px;">
                                        <a href="index.php?page=profile&id=<?php echo $member['user_id']; ?>" class="ml-2 text-gray-800">
                                            <?php echo htmlspecialchars($member['username']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card mb-0">
                    <div class="card-header py-3" id="headingFiles">
                        <h6 class="mb-0">
                            <button class="btn btn-link btn-block text-left text-dark font-weight-bold collapsed" type="button" data-toggle="collapse" data-target="#collapseFiles" aria-expanded="false" aria-controls="collapseFiles">
                                Kho lưu trữ file (<?php echo count($chat_files); ?>)
                            </button>
                        </h6>
                    </div>
                    <div id="collapseFiles" class="collapse" aria-labelledby="headingFiles" data-parent="#infoSidebarAccordion">
                        <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                            <?php if (empty($chat_files)): ?>
                                <p class="text-muted small">Chưa có file nào được gửi.</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                <?php foreach ($chat_files as $file): ?>
                                    <li class="list-group-item p-1">
                                        <a href="<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank" class="text-decoration-none">
                                            <i class="fas fa-file-alt text-gray-500"></i>
                                            <span class="text-primary small"><?php echo htmlspecialchars($file['file_name']); ?></span>
                                            <span class="d-block text-muted small">
                                                Bởi: <?php echo htmlspecialchars($file['uploader_name']); ?>
                                            </span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div> 

<div class="modal fade" id="createPollModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Tạo bình chọn mới</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="index.php?action=create_poll" method="POST" id="create-poll-form">
                <div class="modal-body">
                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                    <div class="form-group">
                        <label for="poll_question_modal">Câu hỏi</label>
                        <input type="text" name="poll_question" id="poll_question_modal" class="form-control" required>
                    </div>
                    <div class="form-group poll-options-modal">
                        <label>Các lựa chọn</label>
                        <input type="text" name="options[]" class="form-control mb-2" placeholder="Lựa chọn 1" required>
                        <input type="text" name="options[]" class="form-control mb-2" placeholder="Lựa chọn 2">
                        <input type="text" name="options[]" class="form-control mb-2" placeholder="Lựa chọn 3">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo Bình Chọn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- TỰ CUỘN CHAT ---
    const chatBox = document.getElementById('chat-box');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
});
</script>

<style>
/* CSS cho Poll bên trong chat */
.poll-option { border: 1px solid #eee; }
.poll-option .custom-control-label::before,
.poll-option .custom-control-label::after {
    top: 0.4rem; /* Căn chỉnh lại radio button */
}
</style>

<?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>