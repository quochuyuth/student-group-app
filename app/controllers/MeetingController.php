<?php
// app/controllers/MeetingController.php

require_once 'app/models/Meeting.php';
require_once 'app/models/Group.php'; 

class MeetingController {
    private $db;
    private $meetingModel;
    private $groupModel;

    public function __construct($db) {
        $this->db = $db;
        $this->meetingModel = new Meeting($this->db);
        $this->groupModel = new Group($this->db);
    }

    // ... (Giữ nguyên hàm: index, create, saveMinutes) ...
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login'); exit;
        }
        $group_id = (int)$_GET['group_id'];
        $group = $this->groupModel->getGroupById($group_id);
        if (!$group) {
            header('Location: index.php?page=groups'); exit;
        }
        $meetings = $this->meetingModel->getMeetingsByGroupId($group_id);
        require 'app/views/group_meetings.php';
    }
    public function create() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
        }
        $group_id = (int)$_POST['group_id'];
        $redirect_url = "Location: index.php?page=group_meetings&group_id=" . $group_id;
        $data = [
            'group_id' => $group_id, 'meeting_title' => strip_tags($_POST['meeting_title']),
            'start_time' => $_POST['start_time'], 'agenda' => strip_tags($_POST['agenda']),
            'created_by_user_id' => $_SESSION['user_id']
        ];
        if (empty($data['meeting_title']) || empty($data['start_time'])) {
            $_SESSION['flash_message'] = "Lỗi: Vui lòng nhập Tiêu đề và Thời gian bắt đầu.";
            header($redirect_url); exit;
        }
        if ($this->meetingModel->create($data)) {
            $_SESSION['flash_message'] = "Đặt lịch họp thành công!";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể đặt lịch họp.";
        }
        header($redirect_url); exit;
    }
    public function saveMinutes() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
        }
        $meeting_id = (int)$_POST['meeting_id'];
        $minutes = strip_tags($_POST['minutes'], '<br><p><ul><ol><li><strong><em>');
        $action_items = strip_tags($_POST['action_items'], '<br><p><ul><ol><li><strong><em>');
        $redirect_url = "Location: index.php?page=meeting_details&id=" . $meeting_id;
        if ($this->meetingModel->updateMinutes($meeting_id, $minutes, $action_items)) {
            $_SESSION['flash_message'] = "Đã lưu biên bản họp!";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể lưu biên bản.";
        }
        header($redirect_url); exit;
    }

    /**
     * CẬP NHẬT HÀM NÀY:
     * Hiển thị trang chi tiết (thêm logic lấy rating)
     */
    public function showDetails() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $meeting_id = (int)$_GET['id'];
        $user_id = $_SESSION['user_id'];
        
        $meeting = $this->meetingModel->getMeetingById($meeting_id);
        
        // LẤY RATING CỦA USER NÀY (MỚI)
        $user_rating = $this->meetingModel->getUserRating($meeting_id, $user_id);
        
        if (!$meeting) {
            $_SESSION['flash_message'] = "Không tìm thấy cuộc họp.";
            header('Location: index.php?page=groups');
            exit;
        }

        // Truyền cả $meeting và $user_rating
        require 'app/views/meeting_details.php';
    }

    // ===================================================
    // HÀM MỚI: LƯU ĐÁNH GIÁ 1-5 SAO
    // ===================================================
    public function submitRating() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        $meeting_id = (int)$_POST['meeting_id'];
        $user_id = $_SESSION['user_id'];
        $rating = (int)$_POST['satisfaction_rating'];
        
        $redirect_url = "Location: index.php?page=meeting_details&id=" . $meeting_id;

        if ($rating < 1 || $rating > 5) {
            $_SESSION['flash_message'] = "Lỗi: Vui lòng chọn điểm từ 1 đến 5.";
            header($redirect_url);
            exit;
        }

        if ($this->meetingModel->submitRating($meeting_id, $user_id, $rating)) {
            $_SESSION['flash_message'] = "Đã lưu đánh giá của bạn!";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể lưu đánh giá.";
        }
        
        header($redirect_url);
        exit;
    }
}
?>