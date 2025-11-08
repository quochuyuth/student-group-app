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

    /**
     * SỬA LỖI: Thêm $file_size
     */
    public function saveFileMessage($group_id, $user_id, $file_name, $file_path, $file_size, $file_type) {
        try {
            $this->db->beginTransaction();

            // 1. Thêm vào bảng 'files' (ĐÃ THÊM file_size)
            $sql_file = "INSERT INTO files (group_id, uploaded_by_user_id, file_name, file_path, file_size, file_type)
                         VALUES (?, ?, ?, ?, ?, ?)";
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

    /**
     * Cập nhật hàm lấy tin nhắn (Đã có từ trước)
     */
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

    // *** (SỬA ĐỔI 1) ***
    // Thêm hàm mới để gửi tin nhắn chỉ chứa poll
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
}
?>