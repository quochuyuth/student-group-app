<?php
// app/models/Task.php

class Task {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // ... (Giữ nguyên các hàm: create, getTasksByGroupId, updateStatus, getTaskById, getCommentsByTaskId, addComment) ...
    public function create($data) {
        $sql = "INSERT INTO tasks (group_id, task_title, task_description, priority, created_by_user_id, assigned_to_user_id, due_date, points)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        try {
            $due_date = !empty($data['due_date']) ? $data['due_date'] : null;
            $assigned_to = !empty($data['assigned_to_user_id']) ? $data['assigned_to_user_id'] : null;
            return $stmt->execute([
                $data['group_id'], $data['task_title'], $data['task_description'],
                $data['priority'], $data['created_by_user_id'], $assigned_to,
                $due_date, $data['points']
            ]);
        } catch (PDOException $e) { return false; }
    }
    public function getTasksByGroupId($group_id) {
        $sql = "SELECT t.*, u_assign.username AS assignee_name, u_create.username AS creator_name
                FROM tasks t
                LEFT JOIN users u_assign ON t.assigned_to_user_id = u_assign.user_id
                LEFT JOIN users u_create ON t.created_by_user_id = u_create.user_id
                WHERE t.group_id = ? ORDER BY t.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateStatus($task_id, $new_status) {
        $allowed_statuses = ['backlog', 'in_progress', 'review', 'done'];
        if (!in_array($new_status, $allowed_statuses)) { return false; }
        $sql = "UPDATE tasks SET status = ? WHERE task_id = ?";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$new_status, $task_id]);
        } catch (PDOException $e) { return false; }
    }
    public function getTaskById($task_id) {
        $sql = "SELECT t.*, u_assign.username AS assignee_name, u_create.username AS creator_name
                FROM tasks t
                LEFT JOIN users u_assign ON t.assigned_to_user_id = u_assign.user_id
                LEFT JOIN users u_create ON t.created_by_user_id = u_create.user_id
                WHERE t.task_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$task_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getCommentsByTaskId($task_id) {
        $sql = "SELECT tc.*, u.username AS commenter_name
                FROM task_comments tc
                JOIN users u ON tc.user_id = u.user_id
                WHERE tc.task_id = ? ORDER BY tc.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$task_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addComment($task_id, $user_id, $comment_text) {
        $sql = "INSERT INTO task_comments (task_id, user_id, comment_text)
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$task_id, $user_id, $comment_text]);
        } catch (PDOException $e) { return false; }
    }

    // ===================================================
    // HÀM MỚI 1: GẮN FILE VÀO TASK
    // ===================================================
    public function attachFile($task_id, $group_id, $user_id, $file_name, $file_path, $file_size, $file_type) {
        // Chỉ INSERT vào bảng 'files', trỏ đến 'task_id'
        $sql = "INSERT INTO files (group_id, uploaded_by_user_id, file_name, file_path, file_size, file_type, task_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$group_id, $user_id, $file_name, $file_path, $file_size, $file_type, $task_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // ===================================================
    // HÀM MỚI 2: LẤY CÁC FILE CỦA TASK
    // ===================================================
    public function getFilesByTaskId($task_id) {
        $sql = "SELECT file_id, file_name, file_path, file_type
                FROM files
                WHERE task_id = ?
                ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$task_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>