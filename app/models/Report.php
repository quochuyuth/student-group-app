<?php
// app/models/Report.php

class Report {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * 1. LẤY DỮ LIỆU TIẾN ĐỘ TASK (cho Biểu đồ)
     * Đếm số lượng task trong mỗi trạng thái
     */
    public function getTaskProgress($group_id) {
        $sql = "SELECT status, COUNT(task_id) as count
                FROM tasks
                WHERE group_id = ?
                GROUP BY status";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        
        // Chuyển đổi kết quả thành mảng ['backlog' => 5, 'done' => 2, ...]
        $progress = [
            'backlog' => 0,
            'in_progress' => 0,
            'review' => 0,
            'done' => 0
        ];
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $progress[$row['status']] = $row['count'];
        }
        return $progress;
    }

    /**
     * 2. LẤY BẢNG ĐIỂM ĐÓNG GÓP (cho Bảng)
     * Lấy 2 loại điểm cho mỗi thành viên:
     * - Điểm Task: SUM(points) từ các task 'done'
     * - Điểm Rubric: AVG(total_score) từ các đánh giá
     */
    public function getContributionScores($group_id) {
        // Lấy tất cả thành viên trong nhóm
        $sql = "SELECT 
                    u.user_id, 
                    u.username,
                    
                    -- Lấy tổng điểm từ task (chỉ tính task 'done')
                    (SELECT SUM(t.points) 
                     FROM tasks t 
                     WHERE t.assigned_to_user_id = u.user_id 
                       AND t.status = 'done' 
                       AND t.group_id = gm.group_id) AS total_task_points,
                    
                    -- Lấy điểm trung bình từ Rubric
                    (SELECT AVG(e.total_score) 
                     FROM evaluations e 
                     WHERE e.evaluated_user_id = u.user_id 
                       AND e.group_id = gm.group_id) AS avg_rubric_score
                
                FROM group_members gm
                JOIN users u ON gm.user_id = u.user_id
                WHERE gm.group_id = ?
                GROUP BY u.user_id, u.username";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>