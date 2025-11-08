<?php
// app/models/Chat.php

class Chat {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function sendMessage($group_id, $sender_user_id, $message_content) {
        $sql = "INSERT INTO messages 
                     (group_id, sender_user_id, message_content)
                 VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$group_id, $sender_user_id, $message_content]);
        } catch (PDOException $e) { return false; }
    }

    public function saveFileMessage($group_id, $user_id, $file_name, $file_path, $file_size, $file_type) {
        try {
            $this->db->beginTransaction();

            // 1. Thêm vào bảng 'files'
            // (Chúng ta giả định file chat sẽ có task_id = NULL)
            $sql_file = "INSERT INTO files (group_id, uploaded_by_user_id, file_name, file_path, file_size, file_type, task_id)
                         VALUES (?, ?, ?, ?, ?, ?, NULL)";
            $stmt_file = $this->db->prepare($sql_file);
            $stmt_file->execute([$group_id, $user_id, $file_name, $file_path, $file_size, $file_type]);
            
            $file_id = $this->db->lastInsertId();

            // 2. Thêm vào bảng 'messages'
            $sql_msg = "INSERT INTO messages (group_id, sender_user_id, file_id)
                        VALUES (?, ?, ?)";
            $stmt_msg = $this->db->prepare($sql_msg);
            $stmt_msg->execute([$group_id, $user_id, $file_id]);
            
            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getMessagesByGroupId($group_id, $limit = 50) {
        $sql = "SELECT 
                    m.*, 
                    u.username AS sender_name,
                    f.file_name,
                    f.file_path
                FROM messages m
                JOIN users u ON m.sender_user_id = u.user_id
                LEFT JOIN files f ON m.file_id = f.file_id
                WHERE m.group_id = ?
                ORDER BY m.created_at DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $group_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function sendPollMessage($group_id, $user_id, $poll_id) {
        $sql = "INSERT INTO messages (group_id, sender_user_id, poll_id, created_at) 
                VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$group_id, $user_id, $poll_id]);
        } catch (PDOException $e) { 
            return false; 
        }
    }

    /**
     * (MỚI) Lấy tất cả các file đã gửi trong chat (không phải file task)
     * Dựa trên logic là file chat sẽ có task_id LÀ NULL
     */
    public function getChatFilesByGroupId($group_id) {
        $sql = "SELECT 
                    f.file_name, f.file_path, f.file_size, f.created_at,
                    u.username AS uploader_name
                FROM files f
                JOIN users u ON f.uploaded_by_user_id = u.user_id
                WHERE f.group_id = ? AND f.task_id IS NULL
                ORDER BY f.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>