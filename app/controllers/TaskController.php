<?php
// app/controllers/TaskController.php

require_once 'app/models/Task.php';

class TaskController {
    private $db;
    private $taskModel;

    public function __construct($db) {
        $this->db = $db;
        $this->taskModel = new Task($this->db);
    }

    // ... (Giữ nguyên hàm: create, updateStatus) ...
    public function create() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
        }
        $group_id = (int)$_POST['group_id'];
        $redirect_url = "Location: index.php?page=group_details&id=$group_id";
        $data = [
            'group_id' => $group_id, 'task_title' => strip_tags($_POST['task_title']),
            'task_description' => strip_tags($_POST['task_description']),
            'priority' => $_POST['priority'], 'created_by_user_id' => $_SESSION['user_id'],
            'assigned_to_user_id' => (int)$_POST['assigned_to_user_id'],
            'due_date' => $_POST['due_date'], 'points' => (int)$_POST['points']
        ];
        if (empty($data['task_title'])) {
            $_SESSION['flash_message'] = "Lỗi: Tiêu đề công việc không được để trống.";
            header($redirect_url); exit;
        }
        if ($this->taskModel->create($data)) {
            $_SESSION['flash_message'] = "Tạo công việc mới thành công!";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể tạo công việc.";
        }
        header($redirect_url); exit;
    }
    public function updateStatus() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập.']); exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $task_id = (int)($data['task_id'] ?? 0);
        $new_status = $data['new_status'] ?? '';
        $result = $this->taskModel->updateStatus($task_id, $new_status);
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật CSDL.']);
        }
        exit;
    }

    // ===================================================
    // HÀM MỚI 1: LẤY CHI TIẾT TASK (CHO AJAX)
    // ===================================================
    public function getDetails() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập.']); exit;
        }

        $task_id = (int)($_GET['task_id'] ?? 0);
        
        $task = $this->taskModel->getTaskById($task_id);
        $comments = $this->taskModel->getCommentsByTaskId($task_id);

        header('Content-Type: application/json');
        if ($task) {
            echo json_encode([
                'success' => true,
                'task' => $task,
                'comments' => $comments
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy task.']);
        }
        exit;
    }

    // ===================================================
    // HÀM MỚI 2: THÊM BÌNH LUẬN (CHO AJAX)
    // ===================================================
    public function addComment() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']); exit;
        }

        $task_id = (int)($_POST['task_id'] ?? 0);
        $comment_text = trim(strip_tags($_POST['comment_text'] ?? ''));
        $user_id = $_SESSION['user_id'];

        if (empty($comment_text) || $task_id == 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']); exit;
        }

        $result = $this->taskModel->addComment($task_id, $user_id, $comment_text);

        header('Content-Type: application/json');
        if ($result) {
            // Trả về thông tin user để JS hiển thị ngay lập tức
            echo json_encode([
                'success' => true,
                'comment' => [
                    'comment_text' => $comment_text,
                    'commenter_name' => $_SESSION['username'], // Lấy tên từ session
                    'created_at' => date('Y-m-d H:i:s') // Lấy giờ hiện tại
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi CSDL.']);
        }
        exit;
    }
}
?>