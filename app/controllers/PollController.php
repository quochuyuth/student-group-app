<?php
// app/controllers/PollController.php

require_once 'app/models/Poll.php';

class PollController {
    private $db;
    private $pollModel;

    public function __construct($db) {
        $this->db = $db;
        $this->pollModel = new Poll($this->db);
    }

    /**
     * Xử lý logic tạo Poll mới
     * SỬA LỖI: Dùng 'poll_question'
     */
    public function create() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        $group_id = (int)$_POST['group_id'];
        $user_id = $_SESSION['user_id'];
        $poll_question = strip_tags($_POST['poll_question']); // SỬA TÊN BIẾN
        $options = $_POST['options'];
        
        $redirect_url = "Location: index.php?page=group_details&id=" . $group_id . "#polls";

        if (empty($poll_question) || empty(trim($options[0]))) { // SỬA BIẾN KIỂM TRA
            $_SESSION['flash_message'] = "Lỗi: Vui lòng nhập Câu hỏi và ít nhất 1 Lựa chọn.";
            header($redirect_url);
            exit;
        }

        $result = $this->pollModel->create($group_id, $user_id, $poll_question, $options); // SỬA BIẾN GỬI ĐI

        if ($result === true) {
            $_SESSION['flash_message'] = "Tạo bình chọn thành công!";
        } else if (is_string($result)) {
            $_SESSION['flash_message'] = "Lỗi CSDL: " . $result;
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể tạo bình chọn.";
        }
        
        header($redirect_url);
        exit;
    }

    /**
     * Xử lý logic Vote
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

        $redirect_url = "Location: index.php?page=group_details&id=" . $group_id . "#poll-" . $poll_id;
        
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