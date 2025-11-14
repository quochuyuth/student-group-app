<?php
// app/models/Report.php (ĐÃ SỬA LỖI KÝ TỰ VÔ HÌNH)

class Report {
    // --- ĐÃ SỬA LỖI THỤT ĐẦU DÒNG Ở ĐÂY ---
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * 1. (SỬA ĐỔI) LẤY DỮ LIỆU TIẾN ĐỘ TASK
     * Thêm bộ lọc user_id, date_from, date_to
     */
    public function getTaskProgress($group_id, $user_id = null, $date_from = null, $date_to = null) {
        
        $sql = "SELECT status, COUNT(task_id) as count
                FROM tasks
                WHERE group_id = ?";
        
        $params = [$group_id];
        
        // (MỚI) Thêm điều kiện lọc
        if (!empty($user_id)) {
            $sql .= " AND assigned_to_user_id = ?";
            $params[] = $user_id;
        }
        if (!empty($date_from)) {
            // Lọc theo ngày TẠO task
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $date_from;
        }
        if (!empty($date_to)) {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $date_to;
        }
        
        $sql .= " GROUP BY status";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        // (Giữ nguyên)
        $progress = [
            'backlog' => 0, 'in_progress' => 0,
            'review' => 0, 'done' => 0
        ];
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            if (isset($progress[$row['status']])) {
                $progress[$row['status']] = $row['count'];
            }
        }
        return $progress;
    }

    /**
     * 2. (SỬA ĐỔI) LẤY BẢNG ĐIỂM ĐÓNG GÓP
     * Thêm bộ lọc user_id, date_from, date_to
     * (THAY ĐỔI LOGIC) Đếm tổng task và task hoàn thành
     */
    public function getContributionScores($group_id, $user_id = null, $date_from = null, $date_to = null) {
        
        // (SỬA) Chỉ cần 1 bộ params cho filter ngày
        $task_params = []; // Mảng params cho sub-query
        $sql_task_date_filter = "";
        // $sql_rubric_date_filter = ""; // (XÓA)

        if (!empty($date_from)) {
            $sql_task_date_filter .= " AND DATE(t.created_at) >= ?";
            // $sql_rubric_date_filter .= " AND DATE(e.created_at) >= ?"; // (XÓA)
            $task_params[] = $date_from; // (SỬA) Chỉ 1 lần
            // $params[] = $date_from; // Cho rubric (XÓA)
        }
        if (!empty($date_to)) {
            $sql_task_date_filter .= " AND DATE(t.created_at) <= ?";
            // $sql_rubric_date_filter .= " AND DATE(e.created_at) <= ?"; // (XÓA)
            $task_params[] = $date_to; // (SỬA) Chỉ 1 lần
            // $params[] = $date_to; // Cho rubric (XÓA)
        }

        // (MỚI) Chuẩn bị params cho query chính
        $main_params = [$group_id];
        $sql_user_filter = "";
        if (!empty($user_id)) {
            $sql_user_filter = " AND gm.user_id = ?";
            $main_params[] = $user_id;
        }

        // (SỬA ĐỔI) Chèn các biến filter vào câu SQL
        $sql = "SELECT 
                    u.user_id, 
                    u.username,
                    
                    -- (MỚI) Đếm TỔNG SỐ task được giao
                    (SELECT COUNT(t.task_id) 
                     FROM tasks t 
                     WHERE t.assigned_to_user_id = u.user_id 
                       AND t.group_id = gm.group_id 
                       {$sql_task_date_filter}
                    ) AS total_tasks,
                    
                    -- (MỚI) Đếm số task đã HOÀN THÀNH
                    (SELECT COUNT(t.task_id) 
                     FROM tasks t 
                     WHERE t.assigned_to_user_id = u.user_id 
                       AND t.group_id = gm.group_id 
                       AND t.status = 'done'
                       {$sql_task_date_filter}
                    ) AS completed_tasks
                
                FROM group_members gm
                JOIN users u ON gm.user_id = u.user_id
                WHERE gm.group_id = ? 
                {$sql_user_filter}
                GROUP BY u.user_id, u.username";
        
        // (SỬA) Gộp params
        // Cần gộp 2 lần cho 2 subquery, CỘNG với main query
        $final_params = array_merge($task_params, $task_params, $main_params); 
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($final_params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>