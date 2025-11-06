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

    private $criteria_weights = [
        'completion' => 0.30,
        'deadline' => 0.20,
        'quality' => 0.20,
        'communication' => 0.15,
        'initiative' => 0.15
    ];

    public function __construct($db) {
        $this->db = $db;
        $this->rubricModel = new Rubric($this->db);
        $this->groupModel = new Group($this->db);
        $this->taskModel = new Task($this->db);
    }

    public function showForm() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        $group_id = (int)$_GET['group_id'];
        
        $group = $this->groupModel->getGroupById($group_id);
        $members = $this->groupModel->getMembersByGroupId($group_id);
        $tasks = $this->taskModel->getTasksByGroupId($group_id);
        $criteria = $this->criteria_weights;
        
        require 'app/views/group_rubric.php';
    }

    public function submit() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        $data = [
            'group_id' => (int)$_POST['group_id'],
            'evaluated_user_id' => (int)$_POST['evaluated_user_id'],
            'task_id' => (int)$_POST['task_id'], // Vẫn lấy task_id, nhưng Model sẽ bỏ qua
            'evaluator_user_id' => $_SESSION['user_id']
        ];
        
        $scores = $_POST['scores'] ?? [];
        $redirect_url = "Location: index.php?page=group_rubric&group_id=" . $data['group_id'];

        // Validation
        if (empty($data['evaluated_user_id'])) {
            $_SESSION['flash_message'] = "Lỗi: Bạn chưa chọn thành viên để đánh giá.";
            header($redirect_url);
            exit;
        }
        if (count($scores) < 5) {
            $_SESSION['flash_message'] = "Lỗi: Bạn phải cho điểm tất cả 5 tiêu chí.";
            header($redirect_url);
            exit;
        }

        $total_score = 0;
        $scores_with_weights = []; 

        foreach ($this->criteria_weights as $criteria_name => $weight) {
            $score = $scores[$criteria_name];
            $total_score += ($score * $weight);
            $scores_with_weights[$criteria_name] = [
                'score' => $score,
                'weight' => $weight
            ];
        }

        // Xử lý lỗi CSDL
        $result = $this->rubricModel->submitEvaluation($data, $scores_with_weights, $total_score);

        if ($result === true) {
            $_SESSION['flash_message'] = "Đánh giá thành công! Điểm tổng: " . round($total_score, 2) . "/4.0";
        } else if (is_string($result)) {
            // Hiển thị lỗi CSDL (nếu có)
            $_SESSION['flash_message'] = "Lỗi CSDL: " . $result;
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể gửi đánh giá.";
        }

        header($redirect_url);
        exit;
    }
}
?>