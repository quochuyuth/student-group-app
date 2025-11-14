<?php
// app/controllers/PollController.php (ĐÃ SỬA LỖI)

require_once 'app/models/Poll.php';
require_once 'app/models/Chat.php'; 

class PollController {
    private $db;
    private $pollModel;
    private $chatModel; 

    public function __construct($db) {
        $this->db = $db;
        $this->pollModel = new Poll($this->db);
        $this->chatModel = new Chat($this->db); 
    }

    /**
     * Sửa hàm create
     */
    public function create() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        $group_id = (int)$_POST['group_id'];
        $user_id = $_SESSION['user_id'];
        $poll_question = strip_tags($_POST['poll_question']);
        $options = $_POST['options'];
        
        // *** (SỬA LỖI) ***
        // Đổi redirect_url từ 'group_details' sang 'group_chat'
        $redirect_url = "Location: index.php?page=group_chat&id=" . $group_id . "#chat-box-bottom";

        if (empty($poll_question) || empty(trim($options[0]))) {
            $_SESSION['flash_message'] = "Lỗi: Vui lòng nhập Câu hỏi và ít nhất 1 Lựa chọn.";
            header($redirect_url);
            exit;
        }

        // Yêu cầu Model trả về ID của poll mới tạo
        $new_poll_id = $this->pollModel->create($group_id, $user_id, $poll_question, $options); 

        if ($new_poll_id) { // Nếu Model trả về ID (thành công)
            // Tự động tạo một tin nhắn trỏ tới Poll ID này
            $this->chatModel->sendPollMessage($group_id, $user_id, $new_poll_id);
            
            $_SESSION['flash_message'] = "Tạo bình chọn thành công!";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể tạo bình chọn.";
        }
        
        header($redirect_url);
        exit;
    }

    /**
     * Sửa hàm vote
     */
    public function vote() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        $group_id = (int)$_POST['group_id'];
        $poll_id = (int)$_POST['poll_id'];
        $user_id = $_SESSION['user_id'];
        $option_id = (int)$_POST['option_id'];

        // *** (SỬA LỖI) ***
        // Đổi redirect_url từ 'group_details' sang 'group_chat'
        $redirect_url = "Location: index.php?page=group_chat&id=" . $group_id . "#chat-box-bottom";
        
        if (empty($option_id)) {
            $_SESSION['flash_message'] = "Lỗi: Bạn chưa chọn Lựa chọn.";
            header($redirect_url);
            exit;
        }

        $result = $this->pollModel->vote($poll_id, $user_id, $option_id);
        
        if ($result === true) {
            $_SESSION['flash_message'] = "Đã ghi nhận phiếu bầu của bạn!";
        } else if (is_string($result)) {
            $_SESSION['flash_message'] = "Lỗi CSDL: " . $result;
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể gửi phiếu bầu.";
        }
        
        header($redirect_url);
        exit;
    }
}
?>