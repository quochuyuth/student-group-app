<?php
// app/controllers/PollController.php

require_once 'app/models/Poll.php';
require_once 'app/models/Chat.php'; // <-- THÊM DÒNG NÀY

class PollController {
    private $db;
    private $pollModel;
    private $chatModel; // <-- THÊM BIẾN NÀY

    public function __construct($db) {
        $this->db = $db;
        $this->pollModel = new Poll($this->db);
        $this->chatModel = new Chat($this->db); // <-- THÊM DÒNG NÀY
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
        
        // Sửa URL redirect về #chat-box
        $redirect_url = "Location: index.php?page=group_details&id=" . $group_id . "#chat-box";

        if (empty($poll_question) || empty(trim($options[0]))) {
            $_SESSION['flash_message'] = "Lỗi: Vui lòng nhập Câu hỏi và ít nhất 1 Lựa chọn.";
            header($redirect_url);
            exit;
        }

        // Yêu cầu Model trả về ID của poll mới tạo (Bạn cần sửa Model)
        $new_poll_id = $this->pollModel->create($group_id, $user_id, $poll_question, $options); 

        if ($new_poll_id) { // Nếu Model trả về ID (thành công)
            // *** PHẦN NÂNG CẤP QUAN TRỌNG NHẤT ***
            // Tự động tạo một tin nhắn trỏ tới Poll ID này (Bạn cần sửa Model)
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

        // Sửa URL redirect về #chat-box (vì poll giờ nằm trong chat)
        $redirect_url = "Location: index.php?page=group_details&id=" . $group_id . "#chat-box";
        
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