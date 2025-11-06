<?php
// app/models/Poll.php

class Poll {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Tạo một bình chọn mới
     * SỬA LỖI: Dùng 'poll_question'
     */
    public function create($group_id, $user_id, $poll_question, $options) {
        try {
            $this->db->beginTransaction();

            // 1. Thêm vào bảng 'polls'
            $sql_poll = "INSERT INTO polls (group_id, created_by_user_id, poll_question)
                         VALUES (?, ?, ?)";
            $stmt_poll = $this->db->prepare($sql_poll);
            $stmt_poll->execute([$group_id, $user_id, $poll_question]);
            
            $poll_id = $this->db->lastInsertId();

            // 2. Lặp qua các lựa chọn và thêm vào 'poll_options'
            $sql_option = "INSERT INTO poll_options (poll_id, option_text) VALUES (?, ?)";
            $stmt_option = $this->db->prepare($sql_option);
            
            foreach ($options as $option_text) {
                if (!empty(trim($option_text))) {
                    $stmt_option->execute([$poll_id, $option_text]);
                }
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Lấy tất cả bình chọn của nhóm
     * SỬA LỖI: Dùng 'poll_question' (Đây là dòng 57 đang báo lỗi)
     */
    public function getPollsByGroupId($group_id) {
        $sql = "SELECT p.poll_id, p.group_id, p.created_by_user_id, p.poll_question, p.created_at, 
                       u.username AS creator_name
                FROM polls p
                JOIN users u ON p.created_by_user_id = u.user_id
                WHERE p.group_id = ? ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy các options cho từng poll
        $sql_opts = "SELECT 
                        po.*, 
                        COUNT(pv.vote_id) AS vote_count
                     FROM poll_options po
                     LEFT JOIN poll_votes pv ON po.option_id = pv.option_id
                     WHERE po.poll_id = ?
                     GROUP BY po.option_id";
        $stmt_opts = $this->db->prepare($sql_opts);

        foreach ($polls as $key => $poll) {
            $stmt_opts->execute([$poll['poll_id']]);
            $polls[$key]['options'] = $stmt_opts->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $polls;
    }

    /**
     * Xử lý việc vote
     */
    public function vote($poll_id, $user_id, $option_id) {
        try {
            $this->db->beginTransaction();

            // SỬA LỖI: Bảng poll_votes của bạn có cột poll_id
            $sql_delete = "DELETE FROM poll_votes 
                           WHERE user_id = ? AND poll_id = ?";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->execute([$user_id, $poll_id]);

            // SỬA LỖI: Bảng poll_votes của bạn có cột poll_id
            $sql_insert = "INSERT INTO poll_votes (poll_id, option_id, user_id) VALUES (?, ?, ?)";
            $stmt_insert = $this->db->prepare($sql_insert);
            $stmt_insert->execute([$poll_id, $option_id, $user_id]);
            
            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Lấy vote hiện tại của user
     */
    public function getUserVote($poll_id, $user_id) {
        // SỬA LỖI: Bảng poll_votes của bạn có cột poll_id
        $sql = "SELECT option_id FROM poll_votes 
                WHERE user_id = ? AND poll_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $poll_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['option_id'] : null;
    }
}
?>