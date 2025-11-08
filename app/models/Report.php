<?php
// app/models/Report.php

class Report {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * 1. LẤY DỮ LIỆU TIẾN ĐỘ TASK (cho Biểu đồ)
     * (Giữ nguyên)
     */
    public function getTaskProgress($group_id) {
        $sql = "SELECT status, COUNT(task_id) as count
                FROM tasks
                WHERE group_id = ?
                GROUP BY status";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        
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
     * 2. LẤY BẢNG ĐIỂM ĐÓNG GÓP (ĐÃ NÂNG CẤP)
     * Tính điểm Task theo % tiến độ:
     * - Done: 100%
     * - Review: 60%
     * - In Progress: 30%
     * - Backlog: 0%
     */
    public function getContributionScores($group_id) {
        
        $sql = "SELECT 
                    u.user_id, 
                    u.username,
                    
                    -- NÂNG CẤP LOGIC TÍNH ĐIỂM TASK
                    (SELECT SUM(
                        CASE 
                            WHEN t.status = 'done' THEN t.points * 1.0
                            WHEN t.status = 'review' THEN t.points * 0.6 
                            WHEN t.status = 'in_progress' THEN t.points * 0.3
                            ELSE 0 
                        END
                     ) 
                     FROM tasks t 
                     WHERE t.assigned_to_user_id = u.user_id 
                       AND t.group_id = gm.group_id) AS total_task_points,
                    
                    -- Lấy điểm trung bình từ Rubric (Giữ nguyên)
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