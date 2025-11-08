<?php
// app/controllers/RubricController.php

require_once 'app/models/Rubric.php';
require_once 'app/models/Group.php';
require_once 'app/models/Task.php';

class RubricController {
    private $db;
    private $rubricModel;
    private $groupModel;
    private $taskModel;

    // XÓA MẢNG $criteria_weights ĐÃ CODE CỨNG

    public function __construct($db) {
        $this->db = $db;
        $this->rubricModel = new Rubric($this->db);
        $this->groupModel = new Group($this->db);
        $this->taskModel = new Task($this->db);
    }

    /**
     * HÀM ĐÃ SỬA: Hiển thị form tùy theo vai trò (Admin / Member)
     * Giờ sẽ lấy tiêu chí từ CSDL
     */
    public function showForm() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $group_id = (int)$_GET['group_id'];
        $current_user_id = $_SESSION['user_id'];
        
        $group = $this->groupModel->getGroupById($group_id);
        
        // 1. Kiểm tra vai trò
        $user_role = $this->groupModel->getUserRoleInGroup($group_id, $current_user_id);
        
        // 2. Lấy tiêu chí động từ CSDL
        $criteria = $this->rubricModel->getCriteriaByGroup($group_id);

        if ($user_role == 'admin') {
            // --- NẾU LÀ ADMIN: Tải dữ liệu để chấm điểm ---
            $members = $this->groupModel->getMembersByGroupId($group_id);
            // Tải view của Admin
            require 'app/views/group_rubric_admin.php';

        } else {
            // --- NẾU LÀ MEMBER: Tải dữ liệu thống kê của chính họ ---
            $my_stats = $this->rubricModel->getMemberAverageScores($group_id, $current_user_id);
            $my_feedback = $this->rubricModel->getMemberFeedback($group_id, $current_user_id);
            // Tải view của Member
            require 'app/views/group_rubric_member.php';
        }
    }

    /**
     * HÀM ĐÃ SỬA: Xử lý khi Admin chấm điểm
     * Giờ sẽ tính điểm dựa trên tiêu chí động
     */
    public function submit() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        $data = [
            'group_id' => (int)$_POST['group_id'],
            'evaluated_user_id' => (int)$_POST['evaluated_user_id'],
            'evaluator_user_id' => $_SESSION['user_id']
        ];
        
        // $scores giờ là [criteria_id => score]
        $scores = $_POST['scores'] ?? []; 
        $redirect_url = "Location: index.php?page=group_rubric&group_id=" . $data['group_id'];

        // Validation
        if (empty($data['evaluated_user_id'])) {
            $_SESSION['flash_message'] = "Lỗi: Bạn chưa chọn thành viên để đánh giá.";
            header($redirect_url); exit;
        }
        if (empty($scores)) {
            $_SESSION['flash_message'] = "Lỗi: Bạn chưa cho điểm tiêu chí nào.";
            header($redirect_url); exit;
        }

        // Lấy các tiêu chí (và trọng số) từ CSDL
        $criteria_list = $this->rubricModel->getCriteriaByGroup($data['group_id']);
        if (empty($criteria_list)) {
            $_SESSION['flash_message'] = "Lỗi: Nhóm này chưa thiết lập tiêu chí đánh giá.";
            header($redirect_url); exit;
        }
        
        // Kiểm tra tổng trọng số (phải = 1.00)
        $total_weight_sum = $this->rubricModel->getCriteriaWeightSum($data['group_id']);
        if (abs($total_weight_sum - 1.00) > 0.001) { // So sánh số thực
             $_SESSION['flash_message'] = "Lỗi: Tổng trọng số các tiêu chí (".($total_weight_sum * 100)."%) không bằng 100%. Vui lòng yêu cầu Admin thiết lập lại.";
             header($redirect_url); exit;
        }

        $total_score = 0;
        
        // Tạo 1 mảng [criteria_id => weight] để tra cứu
        $weights_map = [];
        foreach ($criteria_list as $c) {
            $weights_map[$c['criteria_id']] = $c['criteria_weight'];
        }

        // Tính điểm
        foreach ($scores as $criteria_id => $score) {
            if (!isset($weights_map[$criteria_id])) {
                $_SESSION['flash_message'] = "Lỗi: Tồn tại tiêu chí không hợp lệ.";
                header($redirect_url); exit;
            }
            $weight = $weights_map[$criteria_id];
            $total_score += ($score * $weight);
        }

        $result = $this->rubricModel->submitEvaluation($data, $scores, $total_score);

        if ($result === true) {
            $_SESSION['flash_message'] = "Đánh giá thành công! Điểm tổng: " . round($total_score, 2) . "/4.0";
        } else if (is_string($result)) {
            $_SESSION['flash_message'] = "Lỗi CSDL: " . $result;
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể gửi đánh giá.";
        }

        header($redirect_url);
        exit;
    }

    public function submitFeedback() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        $group_id = (int)$_POST['group_id'];
        $user_id = $_SESSION['user_id'];
        $feedback_content = strip_tags($_POST['feedback_content']);
        $redirect_url = "Location: index.php?page=group_rubric&group_id=" . $group_id;

        if ($this->rubricModel->submitFeedback($group_id, $user_id, $feedback_content)) {
            $_SESSION['flash_message'] = "Gửi phản hồi thành công!";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể gửi phản hồi.";
        }
        header($redirect_url);
        exit;
    }
    
    public function getMemberFeedbackAjax() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'GET') {
            echo json_encode(['error' => 'Unauthorized or invalid request method.']);
            exit;
        }
        $group_id = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
        $member_id = isset($_GET['member_id']) ? (int)$_GET['member_id'] : 0;
        if ($group_id == 0 || $member_id == 0) {
            echo json_encode(['error' => 'Missing group_id or member_id.']);
            exit;
        }
        $current_user_id = $_SESSION['user_id'];
        $user_role = $this->groupModel->getUserRoleInGroup($group_id, $current_user_id);
        if ($user_role != 'admin') {
            echo json_encode(['error' => 'Access denied. Only group admins can view feedback.']);
            exit;
        }
        $feedback_content = $this->rubricModel->getMemberFeedback($group_id, $member_id);
        echo json_encode(['feedback_content' => $feedback_content]);
        exit;
    }

    // ===================================================
    // HÀM MỚI 1: Hiển thị trang quản lý tiêu chí (CRUD)
    // ===================================================
    public function showManager() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login'); exit;
        }
        
        $group_id = (int)$_GET['group_id'];
        
        // Bảo vệ: Chỉ admin mới được vào đây
        $user_role = $this->groupModel->getUserRoleInGroup($group_id, $_SESSION['user_id']);
        if ($user_role != 'admin') {
             $_SESSION['flash_message'] = "Lỗi: Chỉ trưởng nhóm mới có quyền thiết lập tiêu chí.";
             header('Location: index.php?page=group_details&id=' . $group_id); exit;
        }

        $group = $this->groupModel->getGroupById($group_id);
        $criteria = $this->rubricModel->getCriteriaByGroup($group_id);
        $total_weight_sum = $this->rubricModel->getCriteriaWeightSum($group_id);

        require 'app/views/group_rubric_manager.php';
    }

    // ===================================================
    // HÀM MỚI 2: Xử lý thêm tiêu chí (CRUD)
    // ===================================================
    public function addCriteria() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
        }

        $group_id = (int)$_POST['group_id'];
        $criteria_name = strip_tags($_POST['criteria_name']);
        $criteria_weight = (int)$_POST['criteria_weight']; // Nhập là 30 (%)
        
        $redirect_url = "Location: index.php?page=manage_rubric&group_id=" . $group_id;

        // Bảo vệ: Chỉ admin mới được thêm
        $user_role = $this->groupModel->getUserRoleInGroup($group_id, $_SESSION['user_id']);
        if ($user_role != 'admin') {
             $_SESSION['flash_message'] = "Lỗi: Không có quyền.";
             header($redirect_url); exit;
        }
        
        // Kiểm tra tổng trọng số
        $current_sum = $this->rubricModel->getCriteriaWeightSum($group_id);
        if (($current_sum + ($criteria_weight / 100.0)) > 1.001) { // 1.001 để tránh lỗi float
            $_SESSION['flash_message'] = "Lỗi: Tổng trọng số vượt quá 100%.";
            header($redirect_url); exit;
        }

        $result = $this->rubricModel->addCriteria($group_id, $criteria_name, $criteria_weight);
        if ($result === true) {
            $_SESSION['flash_message'] = "Thêm tiêu chí thành công!";
        } else {
            $_SESSION['flash_message'] = "Lỗi CSDL: " . $result;
        }
        header($redirect_url); exit;
    }

    // ===================================================
    // HÀM MỚI 3: Xử lý xóa tiêu chí (CRUD)
    // ===================================================
    public function deleteCriteria() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
        }

        $group_id = (int)$_POST['group_id'];
        $criteria_id = (int)$_POST['criteria_id'];
        $redirect_url = "Location: index.php?page=manage_rubric&group_id=" . $group_id;

        // Bảo vệ: Chỉ admin mới được xóa
        $user_role = $this->groupModel->getUserRoleInGroup($group_id, $_SESSION['user_id']);
        if ($user_role != 'admin') {
             $_SESSION['flash_message'] = "Lỗi: Không có quyền.";
             header($redirect_url); exit;
        }
        
        $result = $this->rubricModel->deleteCriteria($criteria_id);
        if ($result === true) {
            $_SESSION['flash_message'] = "Xóa tiêu chí thành công!";
        } else {
            $_SESSION['flash_message'] = "Lỗi CSDL: " . $result;
        }
        header($redirect_url); exit;
    }
}
?>