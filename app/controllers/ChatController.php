<?php
// app/controllers/ChatController.php (ĐÃ NÂNG CẤP)

require_once 'app/models/Chat.php';
require_once 'app/models/Poll.php'; 

class ChatController {
    private $db;
    private $chatModel;
    private $pollModel; 

    public function __construct($db) {
        $this->db = $db;
        $this->chatModel = new Chat($this->db);
        $this->pollModel = new Poll($this->db); 
    }

    /**
     * (SỬA ĐỔI) Hỗ trợ Reply
     */
    public function sendMessage() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
        }
        $group_id = (int)$_POST['group_id'];
        $sender_user_id = $_SESSION['user_id'];
        $message_content = trim(strip_tags($_POST['message_content']));
        
        $reply_to_message_id = isset($_POST['reply_to_message_id']) && !empty($_POST['reply_to_message_id']) 
                                ? (int)$_POST['reply_to_message_id'] 
                                : null;

        $redirect_url = "Location: index.php?page=group_chat&id=" . $group_id . "#chat-box-bottom";
        
        if (!empty($message_content)) {
            $this->chatModel->sendMessage($group_id, $sender_user_id, $message_content, $reply_to_message_id);
        }
        header($redirect_url); exit;
    }

    /**
     * (SỬA ĐỔI) Hỗ trợ Reply
     */
    public function sendFile() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
        }

        $group_id = (int)$_POST['group_id'];
        $user_id = $_SESSION['user_id'];
        
        $reply_to_message_id = isset($_POST['reply_to_message_id']) && !empty($_POST['reply_to_message_id']) 
                                ? (int)$_POST['reply_to_message_id'] 
                                : null;

        $redirect_url = "Location: index.php?page=group_chat&id=" . $group_id . "#chat-box-bottom";

        if (!isset($_FILES['group_file']) || $_FILES['group_file']['error'] != UPLOAD_ERR_OK) {
            $_SESSION['flash_message'] = "Lỗi: Không có file nào được chọn hoặc file bị lỗi.";
            header($redirect_url); exit;
        }
        
        $file = $_FILES['group_file'];
        $upload_dir = 'public/uploads/';
        $original_name = basename($file['name']);
        $file_type = $file['type'];
        $file_size = $file['size'];
        
        $safe_name = time() . "_" . ($_SESSION['username'] ?? 'user') . "_" . $original_name;
        $target_path = $upload_dir . $safe_name;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            
            if ($this->chatModel->saveFileMessage($group_id, $user_id, $original_name, $target_path, $file_size, $file_type, $reply_to_message_id)) {
                // Thành công
            } else {
                $_SESSION['flash_message'] = "Lỗi: Lưu file vào CSDL thất bại.";
                unlink($target_path);
            }
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể di chuyển file đã upload.";
        }
        
        header($redirect_url); exit;
    }

    /**
     * (GIỮ NGUYÊN) Xử lý Reaction
     */
    public function handleReaction() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        $message_id = $input['message_id'] ?? 0;
        $emoji = $input['emoji'] ?? '';
        $user_id = $_SESSION['user_id'];

        if (empty($message_id) || empty($emoji)) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu thiếu']);
            exit;
        }

        try {
            $result = $this->chatModel->handleReaction($message_id, $user_id, $emoji);
            echo json_encode([
                'success' => true,
                'action' => $result['action'], 
                'emoji' => $result['emoji']
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * (SỬA ĐỔI) Lấy tin nhắn mới VÀ ĐÁNH DẤU ĐÃ XEM
     */
    public function getNewMessages() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id']) || !isset($_GET['group_id']) || !isset($_GET['last_message_id'])) {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
            exit;
        }

        $group_id = (int)$_GET['group_id'];
        $last_message_id = (int)$_GET['last_message_id'];
        $user_id = $_SESSION['user_id'];

        // (Bảo mật: Bạn nên thêm hàm kiểm tra user có trong nhóm không)
        
        // Lấy tin nhắn mới
        $new_messages = $this->chatModel->getNewMessages($group_id, $last_message_id);
        
        // (MỚI) Nếu có tin nhắn mới, đánh dấu là đã xem (vì user đang bật chat)
        if (count($new_messages) > 0) {
            $this->chatModel->markGroupAsSeen($user_id, $group_id);
        }

        // Lấy reactions và polls cho các tin nhắn mới này
        $message_ids = array_column($new_messages, 'message_id');
        $reactions = $this->chatModel->getReactionsForMessages($message_ids);
        
        $poll_ids = array_filter(array_column($new_messages, 'poll_id'));
        $polls = [];
        if (!empty($poll_ids)) {
            $polls_data = $this->pollModel->getPollsByIds($poll_ids); 
            // Sắp xếp lại poll theo ID để JS dễ tìm
            foreach($polls_data as $poll) {
                $polls[$poll['poll_id']] = $poll;
            }
        }
        
        // Lấy phiếu bầu của user cho các poll này
        $user_votes = [];
        if (!empty($polls)) {
            foreach ($polls as $poll) {
                $user_vote = $this->pollModel->getUserVote($poll['poll_id'], $user_id);
                if ($user_vote) {
                    $user_votes[$poll['poll_id']] = $user_vote;
                }
            }
        }
        
        echo json_encode([
            'success' => true, 
            'messages' => $new_messages,
            'reactions' => $reactions,
            'polls' => $polls, // Gửi poll đã được sắp xếp
            'user_votes' => $user_votes,
            'current_user_id' => $user_id 
        ]);
        exit;
    }
}
?>