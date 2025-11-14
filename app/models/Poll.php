<?php
// app/models/Poll.php (ĐÃ CẬP NHẬT)

class Poll {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Tạo một bình chọn mới
     */
    public function create($group_id, $user_id, $poll_question, $options) {
        try {
            $this->db->beginTransaction();

            $sql_poll = "INSERT INTO polls (group_id, created_by_user_id, poll_question)
                         VALUES (?, ?, ?)";
            $stmt_poll = $this->db->prepare($sql_poll);
            $stmt_poll->execute([$group_id, $user_id, $poll_question]);
            
            $poll_id = $this->db->lastInsertId();

            $sql_option = "INSERT INTO poll_options (poll_id, option_text) VALUES (?, ?)";
            $stmt_option = $this->db->prepare($sql_option);
            
            foreach ($options as $option_text) {
                if (!empty(trim($option_text))) {
                    $stmt_option->execute([$poll_id, $option_text]);
                }
            }

            $this->db->commit();
            
            return $poll_id; 

        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Lấy tất cả bình chọn của nhóm
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
        if (!empty($polls)) {
            $poll_ids = array_column($polls, 'poll_id');
            $options = $this->getOptionsForPolls($poll_ids);
            
            foreach ($polls as $key => $poll) {
                $polls[$key]['options'] = $options[$poll['poll_id']] ?? [];
            }
        }
        
        return $polls;
    }

    /**
     * *** (HÀM MỚI) ***
     * Lấy poll theo danh sách ID (để tối ưu cho AJAX)
     */
    public function getPollsByIds($poll_ids) {
        if (empty($poll_ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($poll_ids), '?'));
        
        $sql = "SELECT p.poll_id, p.group_id, p.created_by_user_id, p.poll_question, p.created_at, 
                       u.username AS creator_name
                FROM polls p
                JOIN users u ON p.created_by_user_id = u.user_id
                WHERE p.poll_id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($poll_ids);
        $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy các options cho từng poll
        if (!empty($polls)) {
            $options = $this->getOptionsForPolls($poll_ids); // Dùng lại $poll_ids
            
            // Gán options vào polls
            foreach ($polls as $key => $poll) {
                $polls[$key]['options'] = $options[$poll['poll_id']] ?? [];
            }
        }
        
        return $polls;
    }

    /**
     * *** (HÀM MỚI - Tách ra từ getPollsByGroupId) ***
     * Lấy options và vote count cho một danh sách polls
     */
    private function getOptionsForPolls($poll_ids) {
        if (empty($poll_ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($poll_ids), '?'));

        $sql_opts = "SELECT 
                        po.*, 
                        COUNT(pv.vote_id) AS vote_count
                     FROM poll_options po
                     LEFT JOIN poll_votes pv ON po.option_id = pv.option_id
                     WHERE po.poll_id IN ($placeholders)
                     GROUP BY po.option_id
                     ORDER BY po.option_id ASC";
        
        $stmt_opts = $this->db->prepare($sql_opts);
        $stmt_opts->execute($poll_ids);
        
        $options_flat = $stmt_opts->fetchAll(PDO::FETCH_ASSOC);
        
        // Sắp xếp lại options theo poll_id
        $options_by_poll = [];
        foreach ($options_flat as $opt) {
            $options_by_poll[$opt['poll_id']][] = $opt;
        }
        
        return $options_by_poll;
    }


    /**
     * Xử lý việc vote
     */
    public function vote($poll_id, $user_id, $option_id) {
        try {
            $this->db->beginTransaction();

            $sql_delete = "DELETE FROM poll_votes 
                           WHERE user_id = ? AND poll_id = ?";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->execute([$user_id, $poll_id]);

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
        $sql = "SELECT option_id FROM poll_votes 
                WHERE user_id = ? AND poll_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $poll_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['option_id'] : null;
    }
}
?>