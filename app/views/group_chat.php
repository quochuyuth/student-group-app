<?php
// Tệp: app/views/group_chat.php (ĐÃ NÂNG CẤP VỚI AJAX POLLING)

// 1. Gọi Header
require 'app/views/layout/header.php'; 

// $current_user_role, $group, $members, $messages, $polls, $user_votes, $chat_files, $reactions
// đã được GroupController->showChat() tải

$current_filter_user = $_GET['filter_user'] ?? '';
$current_filter_date_from = $_GET['filter_date_from'] ?? '';
$current_filter_date_to = $_GET['filter_date_to'] ?? '';

// (MỚI) Lấy ID của tin nhắn cuối cùng (nếu có) để JS bắt đầu hỏi từ đây
$last_message_id = 0;
if (!empty($messages)) {
    $last_message = end($messages); // Lấy tin nhắn cuối cùng trong mảng
    $last_message_id = $last_message['message_id'];
}
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
                        <p class="text-muted text-center" id="chat-empty-message">Chưa có tin nhắn nào.</p>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <?php 
                                $isUser = ($msg['sender_user_id'] == $_SESSION['user_id']);
                                $isUserClass = $isUser ? 'bg-primary text-white ml-auto' : 'bg-white';
                                $isPollClass = !empty($msg['poll_id']) ? 'bg-light border-primary' : '';
                                $current_reactions = $reactions[$msg['message_id']] ?? [];

                                $message_html = '';
                                $message_snippet = ''; 

                                if (!empty($msg['file_id'])) {
                                    $message_snippet = 'Đã gửi file: ' . htmlspecialchars($msg['file_name']);
                                    $message_html = '<p class="mb-0">Đã gửi một file: 
                                                        <a class="' . ($isUser ? 'text-white' : 'text-primary') . '" 
                                                           href="' . htmlspecialchars($msg['file_path']) . '" target="_blank">
                                                            <i class="fas fa-file-download"></i> ' . htmlspecialchars($msg['file_name']) . '
                                                        </a>
                                                    </p>';
                                } 
                                elseif (!empty($msg['poll_id'])) {
                                    $current_poll = null;
                                    foreach ($polls as $poll) {
                                        if ($poll['poll_id'] == $msg['poll_id']) { $current_poll = $poll; break; }
                                    }
                                    
                                    if ($current_poll) {
                                        $message_snippet = 'Bình chọn: ' . htmlspecialchars($current_poll['poll_question']);
                                        ob_start(); 
                                        ?>
                                        <div class="poll-container-in-chat mt-2">
                                            <strong><?php echo htmlspecialchars($current_poll['poll_question']); ?></strong>
                                            <form action="index.php?action=submit_vote" method="POST" class="mt-2">
                                                <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                                <input type="hidden" name="poll_id" value="<?php echo $current_poll['poll_id']; ?>">
                                                <?php 
                                                $total_votes = 0;
                                                if (!empty($current_poll['options'])) {
                                                    foreach ($current_poll['options'] as $opt) { $total_votes += $opt['vote_count']; }
                                                }
                                                ?>
                                                <?php if (!empty($current_poll['options'])): foreach ($current_poll['options'] as $option): ?>
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
                                                <?php endforeach; endif; ?>
                                                <button type="submit" class="btn btn-primary btn-sm mt-2">Bầu chọn</button>
                                            </form>
                                        </div>
                                        <?php
                                        $message_html = ob_get_clean();
                                    }
                                } 
                                else {
                                    $message_snippet = htmlspecialchars(mb_substr($msg['message_content'], 0, 50));
                                    $message_html = '<p class="mb-0">' . nl2br(htmlspecialchars($msg['message_content'])) . '</p>';
                                }
                            ?>
                            
                            <div class="message-container mb-2 <?php echo $isUser ? 'd-flex justify-content-end' : ''; ?>" 
                                 data-message-id="<?php echo $msg['message_id']; ?>" 
                                 data-sender-name="<?php echo htmlspecialchars($msg['sender_name']); ?>"
                                 data-message-content="<?php echo $message_snippet; ?>">

                                <div class="card shadow-sm <?php echo $isUserClass; ?> <?php echo $isPollClass; ?>" style="max-width: 85%; position: relative;">
                                    
                                    <div class="message-actions <?php echo $isUser ? 'is-user' : ''; ?>">
                                        <button class="btn btn-sm btn-light btn-reaction" title="Thả cảm xúc">🙂</button>
                                        <button class="btn btn-sm btn-light btn-reply" title="Trả lời"><i class="fas fa-reply"></i></button>
                                    </div>
                                    
                                    <div class="reaction-picker shadow-sm">
                                        <span data-emoji="👍">👍</span>
                                        <span data-emoji="❤️">❤️</span>
                                        <span data-emoji="😂">😂</span>
                                        <span data-emoji="😮">😮</span>
                                        <span data-emoji="😢">😢</span>
                                        <span data-emoji="🙏">🙏</span>
                                    </div>

                                    <div class="card-body py-2 px-3">
                                        
                                        <?php if (!empty($msg['reply_to_message_id'])): ?>
                                            <div class="replied-message-snippet">
                                                <strong>Trả lời <?php echo htmlspecialchars($msg['replied_sender_name'] ?? '...'); ?>:</strong>
                                                <?php 
                                                    $reply_snippet = 'Tin nhắn đã bị xóa'; 
                                                    if (!empty($msg['replied_file_name'])) {
                                                        $reply_snippet = 'Đã gửi file: ' . htmlspecialchars($msg['replied_file_name']);
                                                    } elseif (!empty($msg['replied_message_content'])) {
                                                        $reply_snippet = htmlspecialchars(mb_substr($msg['replied_message_content'], 0, 100)) . '...';
                                                    }
                                                ?>
                                                <p class="mb-0"><?php echo $reply_snippet; ?></p> 
                                            </div>
                                        <?php endif; ?>

                                        <strong class="d-block"><?php echo htmlspecialchars($msg['sender_name']); ?>:</strong>
                                        
                                        <?php echo $message_html; ?>
                                        
                                        <small class="d-block text-right opacity-75 mt-1" style="font-size: 0.75rem;"><?php echo date('d/m H:i', strtotime($msg['created_at'])); ?></small>
                                    </div>

                                    <div class="reactions-display <?php echo $isUser ? 'is-user' : ''; ?>">
                                        <?php foreach ($current_reactions as $react): ?>
                                            <?php 
                                                $user_has_reacted = in_array($_SESSION['user_id'], explode(',', $react['user_ids_list']));
                                            ?>
                                            <span class="reaction-emoji <?php echo $user_has_reacted ? 'user-reacted' : ''; ?>" 
                                                  data-emoji="<?php echo $react['emoji_char']; ?>">
                                                <?php echo $react['emoji_char']; ?>
                                                <small><?php echo $react['emoji_count']; ?></small>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div id="chat-box-bottom"></div>
                </div>
                
                <div class="mt-3 position-relative"> 
                    
                    <div id="main-emoji-picker" class="shadow">
                        <div class="emoji-list"></div>
                    </div>
                
                    <div id="replying-to-bar" class="alert alert-info py-2 px-3 small" style="display: none;">
                        <button type="button" id="cancel-reply-btn" class="close" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        Đang trả lời <strong></strong>
                        <p class="mb-0 text-muted" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></p>
                    </div>

                    <form id="chat-form" action="index.php?action=send_message" method="POST" class="d-flex">
                        <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                        <input type="hidden" name="reply_to_message_id" id="reply-to-message-id-input">
                        
                        <input type="text" id="chat-message-input" name="message_content" class="form-control" placeholder="Gõ tin nhắn của bạn..." required autocomplete="off">
                        
                        <button type="button" class="btn btn-light ml-2" id="main-emoji-toggle-btn" title="Biểu tượng cảm xúc">
                            <i class="far fa-smile"></i>
                        </button>
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
                        <input type="hidden" name="reply_to_message_id" id="reply-to-message-id-file-input">
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
                                    <li class="list-group-item d-flex justify-content-between align-items-center p-1">
                                        <div class="d-flex align-items-center">
                                            <img class="img-profile rounded-circle" src="public/img/undraw_profile.svg" style="width: 30px; height: 30px;">
                                            <a href="index.php?page=profile&id=<?php echo $member['user_id']; ?>" class="ml-2 text-gray-800">
                                                <?php echo htmlspecialchars($member['username']); ?>
                                                <?php if($member['role'] == 'admin') echo ' (Trưởng nhóm)'; ?>
                                            </a>
                                        </div>
                                        
                                        <?php 
                                        if ($current_user_role == 'admin' && $member['user_id'] != $_SESSION['user_id'] && $member['role'] != 'admin'): 
                                        ?>
                                            <form action="index.php?action=remove_member" method="POST" class="form-remove-member d-inline">
                                                <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                                <input type="hidden" name="user_id" value="<?php echo $member['user_id']; ?>">
                                                <input type="hidden" name="redirect_page" value="group_chat">
                                                <button type="submit" class="btn btn-danger btn-sm btn-circle btn-remove-member" 
                                                   title="Xóa <?php echo htmlspecialchars($member['username']); ?> khỏi nhóm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
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
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            
                            <form action="index.php" method="GET" class="mb-3">
                                <input type="hidden" name="page" value="group_chat">
                                <input type="hidden" name="id" value="<?php echo $group['group_id']; ?>">
                                
                                <div class="form-group">
                                    <label for="filter_user" class="small">Lọc theo người gửi:</label>
                                    <select name="filter_user" id="filter_user" class="form-control form-control-sm">
                                        <option value="">-- Tất cả thành viên --</option>
                                        <?php foreach ($members as $member): ?>
                                            <option value="<?php echo $member['user_id']; ?>" <?php echo ($member['user_id'] == $current_filter_user) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($member['username']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-6">
                                        <label for="filter_date_from" class="small">Từ ngày:</label>
                                        <input type="date" name="filter_date_from" id="filter_date_from" class="form-control form-control-sm" value="<?php echo htmlspecialchars($current_filter_date_from); ?>">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="filter_date_to" class="small">Đến ngày:</label>
                                        <input type="date" name="filter_date_to" id="filter_date_to" class="form-control form-control-sm" value="<?php echo htmlspecialchars($current_filter_date_to); ?>">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                                <a href="index.php?page=group_chat&id=<?php echo $group['group_id']; ?>" class="btn btn-secondary btn-sm">
                                    Xóa lọc
                                </a>
                            </form>
                            
                            <hr>

                            <?php if (empty($chat_files)): ?>
                                <p class="text-muted small text-center">
                                    <?php echo (!empty($current_filter_user) || !empty($current_filter_date_from) || !empty($current_filter_date_to)) ? 'Không tìm thấy file nào khớp.' : 'Chưa có file nào được gửi.'; ?>
                                </p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                <?php foreach ($chat_files as $file): ?>
                                    <li class="list-group-item p-1">
                                        <a href="<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank" class="text-decoration-none">
                                            <i class="fas fa-file-alt text-gray-500"></i>
                                            <span class="text-primary small"><?php echo htmlspecialchars($file['file_name']); ?></span>
                                            <span class="d-block text-muted small">
                                                Bởi: <?php echo htmlspecialchars($file['uploader_name']); ?>
                                                (<?php echo date('d/m/y', strtotime($file['created_at'])); ?>)
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

<style>
.poll-option { border: 1px solid #eee; }
.poll-option .custom-control-label::before,
.poll-option .custom-control-label::after {
    top: 0.4rem; 
}
.message-actions {
    position: absolute;
    top: -15px;
    z-index: 10;
    display: none; 
}
.message-container:hover .message-actions {
    display: block;
}
.message-actions { right: -10px; }
.message-actions.is-user { left: -10px; }
.message-actions .btn {
    padding: 0.1rem 0.4rem;
    font-size: 0.75rem;
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 10px;
}
.message-actions .btn:hover { background: #eee; }
.reaction-picker {
    display: none; 
    position: absolute;
    top: -45px;
    z-index: 20;
    background: #fff;
    border-radius: 20px;
    padding: 5px 10px;
    border: 1px solid #ddd;
}
.reaction-picker.show { display: block; }
.reaction-picker span {
    font-size: 1.25rem;
    cursor: pointer;
    padding: 2px;
    transition: transform 0.1s ease;
}
.reaction-picker span:hover { transform: scale(1.3); }
.reactions-display {
    position: absolute;
    bottom: -12px;
    left: 10px;
    z-index: 5;
    display: flex;
    gap: 2px;
}
.reactions-display.is-user { right: 10px; left: auto; }
.reaction-emoji {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 0px 6px;
    font-size: 0.75rem;
    cursor: pointer;
}
.reaction-emoji small {
    font-weight: 600;
    color: #4e73df;
    margin-left: 2px;
}
.reaction-emoji.user-reacted {
    background: #e9eefe;
    border-color: #4e73df;
}
.replied-message-snippet {
    background: rgba(0, 0, 0, 0.05);
    border-left: 3px solid #4e73df;
    padding: 5px 8px;
    margin-bottom: 5px;
    border-radius: 4px;
}
.bg-primary .replied-message-snippet {
    background: rgba(255, 255, 255, 0.2);
    border-left-color: #fff;
}
.bg-primary .replied-message-snippet strong {
    color: #fff;
}
.bg-primary .replied-message-snippet p {
    color: #f8f9fa;
    opacity: 0.9;
}
.replied-message-snippet p {
    font-style: italic;
    opacity: 0.8;
    margin-bottom: 0 !important;
}
#replying-to-bar {
    position: relative;
    padding-right: 30px; 
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}
#cancel-reply-btn {
    position: absolute;
    top: 5px;
    right: 10px;
    font-size: 1.5rem;
    padding: 0;
    line-height: 1;
}
#main-emoji-picker {
    display: none; 
    position: absolute;
    bottom: 100%; 
    left: 0;
    width: 300px;
    height: 250px;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    overflow-y: auto;
    padding: 10px;
    z-index: 100;
}
#main-emoji-picker.show {
    display: block;
}
#main-emoji-picker .emoji-list {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
#main-emoji-picker .emoji-list span {
    font-size: 1.5rem;
    cursor: pointer;
    padding: 5px;
    border-radius: 5px;
}
#main-emoji-picker .emoji-list span:hover {
    background: #eee;
}
</style>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatBox = document.getElementById('chat-box');
    const currentUserId = <?php echo $_SESSION['user_id']; ?>;
    const currentGroupId = <?php echo $group['group_id']; ?>;
    
    // (MỚI) Lấy ID tin nhắn cuối cùng để bắt đầu Polling
    let lastMessageId = <?php echo $last_message_id; ?>;
    
    // (MỚI) Biến theo dõi tab
    let isTabActive = true;
    let unreadCount = 0;
    const originalTitle = document.title; // Lưu tiêu đề gốc

    // (MỚI) Kiểm tra khi nào tab active/inactive
    document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === 'visible') {
            isTabActive = true;
            unreadCount = 0;
            document.title = originalTitle; // Reset tiêu đề khi quay lại
        } else {
            isTabActive = false;
        }
    });


    // --- 1. TỰ CUỘN CHAT ---
    function scrollToBottom(behavior = 'smooth') {
        const chatBoxBottom = document.getElementById('chat-box-bottom');
        if (chatBoxBottom) {
            chatBoxBottom.scrollIntoView({ behavior: behavior });
        }
    }
    // Cuộn xuống ngay lập tức khi tải trang
    scrollToBottom('instant'); 


    // --- 2. LOGIC CHO REPLY (TRẢ LỜI) ---
    const replyBar = document.getElementById('replying-to-bar');
    const replyBarName = replyBar.querySelector('strong');
    const replyBarContent = replyBar.querySelector('p');
    const cancelReplyBtn = document.getElementById('cancel-reply-btn');
    const replyIdInput = document.getElementById('reply-to-message-id-input');
    const replyIdFileInput = document.getElementById('reply-to-message-id-file-input');
    const chatInput = document.getElementById('chat-message-input');

    chatBox.addEventListener('click', function(e) {
        const replyBtn = e.target.closest('.btn-reply');
        if (!replyBtn) return;
        e.preventDefault();
        const messageContainer = replyBtn.closest('.message-container');
        const messageId = messageContainer.dataset.messageId;
        const senderName = messageContainer.dataset.senderName;
        const contentSnippet = messageContainer.dataset.messageContent;

        replyBarName.textContent = senderName;
        replyBarContent.textContent = contentSnippet;
        replyBar.style.display = 'block';
        replyIdInput.value = messageId;
        replyIdFileInput.value = messageId; 
        chatInput.focus();
    });

    cancelReplyBtn.addEventListener('click', function(e) {
        e.preventDefault();
        replyBar.style.display = 'none';
        replyIdInput.value = '';
        replyIdFileInput.value = '';
    });


    // --- 3. LOGIC CHO REACTION (THẢ CẢM XÚC) ---
    chatBox.addEventListener('click', function(e) {
        // (Code xử lý reaction không đổi)
        const reactBtn = e.target.closest('.btn-reaction');
        if (reactBtn) {
            e.preventDefault();
            e.stopPropagation();
            document.querySelectorAll('.reaction-picker.show').forEach(picker => picker.classList.remove('show'));
            const picker = reactBtn.closest('.message-container').querySelector('.reaction-picker');
            if (picker) {
                picker.classList.add('show');
            }
        }
        const emojiSpan = e.target.closest('.reaction-picker span[data-emoji]');
        if (emojiSpan) {
            e.preventDefault();
            e.stopPropagation();
            const emoji = emojiSpan.dataset.emoji;
            const messageContainer = emojiSpan.closest('.message-container');
            const messageId = messageContainer.dataset.messageId;
            emojiSpan.closest('.reaction-picker').classList.remove('show');
            sendReaction(messageId, emoji, messageContainer);
        }
        const reactionEmoji = e.target.closest('.reaction-emoji[data-emoji]');
        if (reactionEmoji) {
            e.preventDefault();
            const emoji = reactionEmoji.dataset.emoji;
            const messageContainer = reactionEmoji.closest('.message-container');
            const messageId = messageContainer.dataset.messageId;
            sendReaction(messageId, emoji, messageContainer);
        }
    });

    function sendReaction(messageId, emoji, messageContainer) {
        // (Code hàm này không đổi)
        fetch('index.php?action=handleReaction', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ message_id: messageId, emoji: emoji })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateReactionsUI(messageContainer, emoji, data.action);
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => console.error('Lỗi fetch reaction:', error));
    }

    function updateReactionsUI(messageContainer, emoji, action) {
        // (Code hàm này không đổi)
        const displayWrapper = messageContainer.querySelector('.reactions-display');
        let emojiSpan = displayWrapper.querySelector(`.reaction-emoji[data-emoji="${emoji}"]`);
        if (action === 'updated') {
             const oldReacted = displayWrapper.querySelector('.reaction-emoji.user-reacted');
             if(oldReacted) {
                const oldCount = parseInt(oldReacted.querySelector('small').textContent);
                if(oldCount === 1) oldReacted.remove();
                else {
                    oldReacted.querySelector('small').textContent = oldCount - 1;
                    oldReacted.classList.remove('user-reacted');
                }
             }
        }
        if (action === 'added' || action === 'updated') {
            if (!emojiSpan) {
                emojiSpan = document.createElement('span');
                emojiSpan.className = 'reaction-emoji user-reacted';
                emojiSpan.dataset.emoji = emoji;
                emojiSpan.innerHTML = `${emoji}<small>1</small>`;
                displayWrapper.appendChild(emojiSpan);
            } else {
                const count = parseInt(emojiSpan.querySelector('small').textContent);
                emojiSpan.querySelector('small').textContent = count + 1;
                emojiSpan.classList.add('user-reacted');
            }
        } 
        else if (action === 'removed') {
            if (emojiSpan) {
                const count = parseInt(emojiSpan.querySelector('small').textContent);
                if (count === 1) {
                    emojiSpan.remove();
                } else {
                    emojiSpan.querySelector('small').textContent = count - 1;
                    emojiSpan.classList.remove('user-reacted');
                }
            }
        }
    }

    // --- 4. LOGIC CHO EMOJI PICKER (Input chính) ---
    const mainEmojiToggle = document.getElementById('main-emoji-toggle-btn');
    const mainEmojiPicker = document.getElementById('main-emoji-picker');
    const emojiList = mainEmojiPicker.querySelector('.emoji-list');
    const emojis = [
        '🙂', '😂', '❤️', '👍', '🎉', '🙏', '🤔', '😢', '😮', '😡', '😎', '😍', 
        '😊', '🥳', '🤯', '🥱', '😴', '😜', '🤮', '😇', '😅', '🤣', '🙄'
    ];
    emojis.forEach(emoji => {
        const span = document.createElement('span');
        span.textContent = emoji;
        emojiList.appendChild(span);
    });
    mainEmojiToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        mainEmojiPicker.classList.toggle('show');
    });
    emojiList.addEventListener('click', function(e) {
        if (e.target.tagName === 'SPAN') {
            const icon = e.target.textContent;
            const start = chatInput.selectionStart;
            const end = chatInput.selectionEnd;
            const text = chatInput.value;
            chatInput.value = text.substring(0, start) + icon + text.substring(end);
            chatInput.selectionStart = chatInput.selectionEnd = start + icon.length;
            chatInput.focus();
        }
    });

    // --- 5. ĐÓNG CÁC PICKER KHI CLICK RA NGOÀI ---
    document.addEventListener('click', function (e) {
        document.querySelectorAll('.reaction-picker.show').forEach(picker => {
            if (!picker.contains(e.target) && !e.target.closest('.btn-reaction')) {
                picker.classList.remove('show');
            }
        });
        
        if (mainEmojiPicker.classList.contains('show')) {
            if (!mainEmojiPicker.contains(e.target) && e.target !== mainEmojiToggle && !e.target.closest('#main-emoji-toggle-btn')) {
                mainEmojiPicker.classList.remove('show');
            }
        }
    });
    
    // --- 6. XÁC NHẬN XÓA THÀNH VIÊN ---
    document.querySelectorAll('.form-remove-member').forEach(form => {
        form.addEventListener('submit', function(e) {
            const memberName = this.closest('li').querySelector('a.text-gray-800').textContent.trim();
            const confirmation = confirm(`Bạn có chắc chắn muốn xóa "${memberName}" khỏi nhóm không? Hành động này không thể hoàn tác.`);
            if (!confirmation) {
                e.preventDefault(); 
            }
        });
    });

    // --- 7. *** (MỚI) AJAX POLLING ĐỂ TẢI TIN NHẮN MỚI *** ---
    function fetchNewMessages() {
        fetch(`index.php?action=getNewMessages&group_id=${currentGroupId}&last_message_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.messages.length > 0) {
                    
                    const emptyMsg = document.getElementById('chat-empty-message');
                    if (emptyMsg) emptyMsg.remove();
                    
                    data.messages.forEach(msg => {
                        // Kiểm tra xem tin nhắn đã tồn tại chưa
                        if (!document.querySelector(`.message-container[data-message-id="${msg.message_id}"]`)) {
                            
                            // (MỚI) Kiểm tra xem có đang cuộn ở cuối không
                            const isAtBottom = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 50;
                            
                            appendMessageToChat(msg, data);
                            lastMessageId = msg.message_id; 

                            // Chỉ tự động cuộn nếu user đang ở cuối
                            if(isAtBottom) {
                                scrollToBottom('smooth');
                            }
                            
                            // Cập nhật tiêu đề tab nếu user không ở tab này
                            if (!isTabActive) {
                                unreadCount++;
                                document.title = `(${unreadCount}) ${originalTitle}`;
                            }
                        }
                    });
                }
            })
            .catch(error => console.error('Lỗi Polling:', error));
    }

    // Bắt đầu Polling: 5 giây một lần
    setInterval(() => {
        // Chỉ fetch khi tab đang active
        if (isTabActive) {
            fetchNewMessages();
        } else {
            // Kiểm tra ngầm để cập nhật tiêu đề tab
            fetch(`index.php?action=getNewMessages&group_id=${currentGroupId}&last_message_id=${lastMessageId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.messages.length > 0) {
                        
                        // Cập nhật lastMessageId ngay cả khi tab không active
                        const lastNewMsg = data.messages[data.messages.length - 1];
                        if (lastNewMsg.message_id > lastMessageId) {
                            lastMessageId = lastNewMsg.message_id;
                        }

                        // Cập nhật tiêu đề tab
                        if (!isTabActive) {
                             unreadCount += data.messages.length;
                             document.title = `(${unreadCount}) ${originalTitle}`;
                        }
                    }
                });
        }
    }, 5000); // 5000ms = 5 giây

    
    // --- 8. *** (MỚI) HÀM TẠO HTML TIN NHẮN (Tái sử dụng code) *** ---
    function appendMessageToChat(msg, data) {
        // Kiểm tra xem các biến data có tồn tại không
        const reactions = data.reactions || {};
        const polls = data.polls || [];
        const user_votes = data.user_votes || {};
        
        const isUser = (msg.sender_user_id == data.current_user_id);
        const isUserClass = isUser ? 'bg-primary text-white ml-auto' : 'bg-white';
        const isPollClass = msg.poll_id ? 'bg-light border-primary' : '';
        const current_reactions = reactions[msg.message_id] || [];

        let message_html = '';
        let message_snippet = '';

        if (msg.file_id) {
            message_snippet = 'Đã gửi file: ' + (msg.file_name || 'file');
            message_html = `<p class="mb-0">Đã gửi một file: 
                                <a class="${isUser ? 'text-white' : 'text-primary'}" 
                                   href="${msg.file_path}" target="_blank">
                                    <i class="fas fa-file-download"></i> ${msg.file_name}
                                </a>
                            </p>`;
        } 
        else if (msg.poll_id) {
            // (SỬA LỖI) Tìm poll trong data.polls
            const current_poll = polls.find(p => p.poll_id == msg.poll_id);
            if (current_poll) {
                message_snippet = 'Bình chọn: ' + current_poll.poll_question;
                
                let poll_options_html = '';
                let total_votes = 0;
                if(current_poll.options) {
                    current_poll.options.forEach(opt => { total_votes += parseInt(opt.vote_count); });
                }

                if(current_poll.options) {
                    current_poll.options.forEach(option => {
                        // (SỬA LỖI) Tìm vote trong data.user_votes
                        const user_voted_this = (user_votes[current_poll.poll_id] || 0) == option.option_id;
                        const vote_percent = (total_votes > 0) ? (option.vote_count / total_votes) * 100 : 0;
                        
                        poll_options_html += `
                            <div class="poll-option position-relative small my-1">
                                <div class="vote-bar bg-info" style="width: ${vote_percent}%; height: 100%; position: absolute; left: 0; top: 0; opacity: 0.2;"></div>
                                <div class="custom-control custom-radio position-relative p-2">
                                    <input type="radio" id="opt-${option.option_id}-${msg.message_id}" name="option_id_${msg.message_id}" value="${option.option_id}" class="custom-control-input" ${user_voted_this ? 'checked' : ''} required>
                                    <label class="custom-control-label" for="opt-${option.option_id}-${msg.message_id}">
                                        ${option.option_text} 
                                        <span class="text-muted">(${option.vote_count})</span>
                                    </label>
                                </div>
                            </div>`;
                    });
                }

                message_html = `
                    <div class="poll-container-in-chat mt-2">
                        <strong>${current_poll.poll_question}</strong>
                        <form action="index.php?action=submit_vote" method="POST" class="mt-2">
                            <input type="hidden" name="group_id" value="${currentGroupId}">
                            <input type="hidden" name="poll_id" value="${current_poll.poll_id}">
                            ${poll_options_html}
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Bầu chọn</button>
                        </form>
                    </div>`;
            } else {
                 message_html = "<p class='mb-0'><em>Đang tải bình chọn...</em></p>";
            }
        } 
        else {
            message_snippet = msg.message_content ? msg.message_content.substring(0, 50) : '';
            message_html = `<p class="mb-0">${msg.message_content ? msg.message_content.replace(/\n/g, '<br>') : ''}</p>`;
        }

        // Tạo snippet cho Reply
        let reply_html = '';
        if (msg.reply_to_message_id) {
            let reply_snippet = 'Tin nhắn đã bị xóa'; 
            if (msg.replied_file_name) {
                reply_snippet = 'Đã gửi file: ' + msg.replied_file_name;
            } else if (msg.replied_message_content) {
                reply_snippet = msg.replied_message_content.substring(0, 100) + '...';
            }
            reply_html = `
                <div class="replied-message-snippet">
                    <strong>Trả lời ${msg.replied_sender_name || '...'}:</strong>
                    <p class="mb-0">${reply_snippet}</p> 
                </div>`;
        }
        
        // Tạo snippet cho Reactions
        let reactions_html = '';
        current_reactions.forEach(react => {
            const user_has_reacted = (react.user_ids_list || '').split(',').includes(String(currentUserId));
            reactions_html += `
                <span class="reaction-emoji ${user_has_reacted ? 'user-reacted' : ''}" data-emoji="${react.emoji_char}">
                    ${react.emoji_char}
                    <small>${react.emoji_count}</small>
                </span>`;
        });

        // Định dạng thời gian
        const msgDate = new Date(msg.created_at);
        const timeString = `${String(msgDate.getHours()).padStart(2, '0')}:${String(msgDate.getMinutes()).padStart(2, '0')}`;

        // Tạo HTML cuối cùng
        const messageElement = document.createElement('div');
        messageElement.className = `message-container mb-2 ${isUser ? 'd-flex justify-content-end' : ''}`;
        messageElement.dataset.messageId = msg.message_id;
        messageElement.dataset.senderName = msg.sender_name;
        messageElement.dataset.messageContent = message_snippet;

        messageElement.innerHTML = `
            <div class="card shadow-sm ${isUserClass} ${isPollClass}" style="max-width: 85%; position: relative;">
                <div class="message-actions ${isUser ? 'is-user' : ''}">
                    <button class="btn btn-sm btn-light btn-reaction" title="Thả cảm xúc">🙂</button>
                    <button class="btn btn-sm btn-light btn-reply" title="Trả lời"><i class="fas fa-reply"></i></button>
                </div>
                <div class="reaction-picker shadow-sm">
                    <span data-emoji="👍">👍</span>
                    <span data-emoji="❤️">❤️</span>
                    <span data-emoji="😂">😂</span>
                    <span data-emoji="😮">😮</span>
                    <span data-emoji="😢">😢</span>
                    <span data-emoji="🙏">🙏</span>
                </div>
                <div class="card-body py-2 px-3">
                    ${reply_html}
                    <strong class="d-block">${msg.sender_name}:</strong>
                    ${message_html}
                    <small class="d-block text-right opacity-75 mt-1" style="font-size: 0.75rem;">${timeString}</small>
                </div>
                <div class="reactions-display ${isUser ? 'is-user' : ''}">
                    ${reactions_html}
                </div>
            </div>`;

        chatBox.insertBefore(messageElement, document.getElementById('chat-box-bottom'));
    }

});
</script>


<?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>