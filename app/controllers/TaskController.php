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

    // ... (Các hàm cũ: create, updateStatus, getDetails, addComment, attachFile) ...
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
            echo json_encode([
                'success' => true,
                'comment' => [
                    'comment_text' => $comment_text,
                    'commenter_name' => $_SESSION['username'],
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi CSDL.']);
        }
        exit;
    }
    public function getDetails() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập.']); exit;
        }
        $task_id = (int)($_GET['task_id'] ?? 0);
        
        $task = $this->taskModel->getTaskById($task_id);
        $comments = $this->taskModel->getCommentsByTaskId($task_id);
        $files = $this->taskModel->getFilesByTaskId($task_id); 

        header('Content-Type: application/json');
        if ($task) {
            echo json_encode([
                'success' => true,
                'task' => $task,
                'comments' => $comments,
                'files' => $files 
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy task.']);
        }
        exit;
    }
    public function attachFile() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $task_id = (int)$_POST['task_id'];
        $group_id = (int)$_POST['group_id'];
        $user_id = $_SESSION['user_id'];
        
        $redirect_url = "Location: index.php?page=group_details&id=" . $group_id;
        
        if (!isset($_FILES['task_file']) || $_FILES['task_file']['error'] != UPLOAD_ERR_OK) {
            $_SESSION['flash_message'] = "Lỗi: Không có file nào được chọn hoặc file bị lỗi.";
            header($redirect_url); exit;
        }
        
        $file = $_FILES['task_file'];
        $upload_dir = 'public/uploads/';
        $original_name = basename($file['name']);
        $file_type = $file['type'];
        $file_size = $file['size'];
        
        $safe_name = time() . "_" . $_SESSION['username'] . "_" . $original_name;
        $target_path = $upload_dir . $safe_name;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            if ($this->taskModel->attachFile($task_id, $group_id, $user_id, $original_name, $target_path, $file_size, $file_type)) {
                $_SESSION['flash_message'] = "Đã đính kèm file thành công!";
            } else {
                $_SESSION['flash_message'] = "Lỗi: Lưu file vào CSDL thất bại.";
                unlink($target_path);
            }
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể di chuyển file đã upload.";
        }
        
        header($redirect_url); exit;
    }

    // ===================================================
    // HÀM MỚI (CHO TRANG all_tasks): HIỂN THỊ TẤT CẢ TASK
    // ===================================================
    public function showAllTasks($filter_status = null) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        $tasks = $this->taskModel->getAllTasksByUserId($user_id, $filter_status);
        
        if ($filter_status == 'pending') {
            $page_title = "Các Task Cần Làm (Chưa Hoàn Thành)";
        } else {
            $page_title = "Tất Cả Task Của Bạn";
        }
        
        require 'app/views/all_tasks.php';
    }
}
?>