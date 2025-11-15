<?php
// app/models/Chat.php (ĐÃ NÂNG CẤP)

class Chat {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // ... (Giữ nguyên các hàm: sendMessage, saveFileMessage, sendPollMessage, getMessagesByGroupId, getNewMessages, getChatFilesByGroupId, handleReaction, getReactionsForMessages) ...
    public function sendMessage($group_id, $sender_user_id, $message_content, $reply_to_message_id = null) {
        if ($reply_to_message_id) {
            $stmt_check = $this->db->prepare("SELECT message_id FROM messages WHERE message_id = ? AND group_id = ?");
            $stmt_check->execute([$reply_to_message_id, $group_id]);
            if ($stmt_check->fetch() == false) {
                $reply_to_message_id = null; 
            }
        }
        $sql = "INSERT INTO messages 
                    (group_id, sender_user_id, message_content, reply_to_message_id)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$group_id, $sender_user_id, $message_content, $reply_to_message_id]);
        } catch (PDOException $e) { return false; }
    }
    public function saveFileMessage($group_id, $user_id, $file_name, $file_path, $file_size, $file_type, $reply_to_message_id = null) {
        try {
            if ($reply_to_message_id) {
                $stmt_check = $this->db->prepare("SELECT message_id FROM messages WHERE message_id = ? AND group_id = ?");
                $stmt_check->execute([$reply_to_message_id, $group_id]);
                if ($stmt_check->fetch() == false) {
                    $reply_to_message_id = null;
                }
            }
            $this->db->beginTransaction();
            $sql_file = "INSERT INTO files (group_id, uploaded_by_user_id, file_name, file_path, file_size, file_type, task_id)
                         VALUES (?, ?, ?, ?, ?, ?, NULL)";
            $stmt_file = $this->db->prepare($sql_file);
            $stmt_file->execute([$group_id, $user_id, $file_name, $file_path, $file_size, $file_type]);
            $file_id = $this->db->lastInsertId();
            $sql_msg = "INSERT INTO messages (group_id, sender_user_id, file_id, reply_to_message_id)
                        VALUES (?, ?, ?, ?)";
            $stmt_msg = $this->db->prepare($sql_msg);
            $stmt_msg->execute([$group_id, $user_id, $file_id, $reply_to_message_id]);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
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
    public function getMessagesByGroupId($group_id, $limit = 50) {
        $sql = "SELECT 
                    m.*, 
                    u.username AS sender_name,
                    f.file_name,
                    f.file_path,
                    replied.message_content AS replied_message_content,
                    replied_user.username AS replied_sender_name,
                    replied_file.file_name AS replied_file_name
                FROM messages m
                JOIN users u ON m.sender_user_id = u.user_id
                LEFT JOIN files f ON m.file_id = f.file_id
                LEFT JOIN messages replied ON m.reply_to_message_id = replied.message_id
                LEFT JOIN users replied_user ON replied.sender_user_id = replied_user.user_id 
                LEFT JOIN files replied_file ON replied.file_id = replied_file.file_id 
                WHERE m.group_id = ?
                ORDER BY m.created_at DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $group_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    public function getNewMessages($group_id, $last_message_id) {
         $sql = "SELECT 
                    m.*, 
                    u.username AS sender_name,
                    f.file_name,
                    f.file_path,
                    replied.message_content AS replied_message_content,
                    replied_user.username AS replied_sender_name,
                    replied_file.file_name AS replied_file_name
                FROM messages m
                JOIN users u ON m.sender_user_id = u.user_id
                LEFT JOIN files f ON m.file_id = f.file_id
                LEFT JOIN messages replied ON m.reply_to_message_id = replied.message_id
                LEFT JOIN users replied_user ON replied.sender_user_id = replied_user.user_id 
                LEFT JOIN files replied_file ON replied.file_id = replied_file.file_id 
                WHERE m.group_id = ? AND m.message_id > ?
                ORDER BY m.created_at ASC"; 
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id, $last_message_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getChatFilesByGroupId($group_id, $filter_user_id = null, $filter_date_from = null, $filter_date_to = null) {
        $sql = "SELECT 
                    f.file_name, f.file_path, f.file_size, f.created_at,
                    u.username AS uploader_name
                FROM files f
                JOIN users u ON f.uploaded_by_user_id = u.user_id
                WHERE f.group_id = ? AND f.task_id IS NULL";
        $params = [$group_id];
        if (!empty($filter_user_id)) {
            $sql .= " AND f.uploaded_by_user_id = ?";
            $params[] = $filter_user_id;
        }
        if (!empty($filter_date_from)) {
            $sql .= " AND DATE(f.created_at) >= ?";
            $params[] = $filter_date_from;
        }
        if (!empty($filter_date_to)) {
            $sql .= " AND DATE(f.created_at) <= ?";
            $params[] = $filter_date_to;
        }
        $sql .= " ORDER BY f.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function handleReaction($message_id, $user_id, $emoji_char) {
        $stmt_check = $this->db->prepare("SELECT reaction_id, emoji_char FROM message_reactions WHERE message_id = ? AND user_id = ?");
        $stmt_check->execute([$message_id, $user_id]);
        $existing = $stmt_check->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            if ($existing['emoji_char'] == $emoji_char) {
                $stmt_delete = $this->db->prepare("DELETE FROM message_reactions WHERE reaction_id = ?");
                $stmt_delete->execute([$existing['reaction_id']]);
                return ['action' => 'removed', 'emoji' => $emoji_char];
            } else {
                $stmt_update = $this->db->prepare("UPDATE message_reactions SET emoji_char = ? WHERE reaction_id = ?");
                $stmt_update->execute([$emoji_char, $existing['reaction_id']]);
                return ['action' => 'updated', 'emoji' => $emoji_char];
            }
        } else {
            $stmt_insert = $this->db->prepare("INSERT INTO message_reactions (message_id, user_id, emoji_char) VALUES (?, ?, ?)");
            $stmt_insert->execute([$message_id, $user_id, $emoji_char]);
            return ['action' => 'added', 'emoji' => $emoji_char];
        }
    }
    public function getReactionsForMessages($message_ids) {
        if (empty($message_ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($message_ids), '?'));
        $sql = "SELECT 
                    message_id, 
                    emoji_char, 
                    COUNT(reaction_id) AS emoji_count,
                    GROUP_CONCAT(user_id) AS user_ids_list 
                FROM message_reactions 
                WHERE message_id IN ($placeholders)
                GROUP BY message_id, emoji_char
                ORDER BY emoji_count DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($message_ids);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $reactions_by_message = [];
        foreach ($results as $row) {
            $reactions_by_message[$row['message_id']][] = $row;
        }
        return $reactions_by_message;
    }

    // ===================================================
    // HÀM MỚI 1: ĐÁNH DẤU NHÓM LÀ "ĐÃ XEM" (GIỮ NGUYÊN)
    // ===================================================
    public function markGroupAsSeen($user_id, $group_id) {
        $sql = "INSERT INTO user_group_last_seen (user_id, group_id, last_seen_timestamp)
                VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE last_seen_timestamp = NOW()";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $group_id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // ===================================================
    // (HÀM SỬA ĐỔI) ĐẾM TIN NHẮN CHƯA ĐỌC CHO TỪNG NHÓM
    // ===================================================
    public function getUnreadCountsByGroup($user_id) {
        $sql = "SELECT m.group_id, COUNT(DISTINCT m.message_id) as unread_count
                FROM messages m
                JOIN group_members gm ON m.group_id = gm.group_id
                LEFT JOIN user_group_last_seen ugls ON m.group_id = ugls.group_id AND gm.user_id = ugls.user_id
                WHERE gm.user_id = ?
                AND m.sender_user_id != ? 
                AND (ugls.last_seen_timestamp IS NULL OR m.created_at > ugls.last_seen_timestamp)
                GROUP BY m.group_id"; // Thêm GROUP BY
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về danh sách
        } catch (PDOException $e) {
            return []; // Trả về mảng rỗng nếu lỗi
        }
    }
}
?>