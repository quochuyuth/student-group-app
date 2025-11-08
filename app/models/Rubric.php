<?php
// app/models/Rubric.php

class Rubric {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * HÀM ĐÃ SỬA: Gửi đánh giá (dùng criteria_id)
     */
    public function submitEvaluation($data, $scores_array, $total_score) {
        try {
            $this->db->beginTransaction();

            $sql_eval = "INSERT INTO evaluations 
                            (group_id, evaluator_user_id, evaluated_user_id, total_score, created_at)
                            VALUES (?, ?, ?, ?, NOW())";
            
            $stmt_eval = $this->db->prepare($sql_eval);
            
            $stmt_eval->execute([
                $data['group_id'],
                $data['evaluator_user_id'],
                $data['evaluated_user_id'],
                $total_score
            ]);

            $evaluation_id = $this->db->lastInsertId();

            // Vòng lặp đã thay đổi: $scores_array giờ là [criteria_id => score]
            foreach ($scores_array as $criteria_id => $score) {
                
                $sql_detail = "INSERT INTO evaluation_scores 
                                (evaluation_id, criteria_id, score)
                                VALUES (?, ?, ?)";
                                
                $stmt_detail = $this->db->prepare($sql_detail);
                $stmt_detail->execute([
                    $evaluation_id,
                    $criteria_id,
                    $score
                ]);
            }

            $this->db->commit();
            return true; // Trả về true nếu thành công

        } catch (PDOException $e) {
            $this->db->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * HÀM ĐÃ SỬA: Lấy điểm TB (dùng criteria_id)
     */
    public function getMemberAverageScores($group_id, $evaluated_user_id) {
        
        $sql = "SELECT 
                    es.criteria_id, 
                    AVG(es.score) as average_score,
                    grc.criteria_name,
                    grc.criteria_weight
                FROM evaluation_scores es
                JOIN evaluations e ON es.evaluation_id = e.evaluation_id
                JOIN group_rubric_criteria grc ON es.criteria_id = grc.criteria_id
                WHERE e.group_id = ? AND e.evaluated_user_id = ?
                GROUP BY es.criteria_id, grc.criteria_name, grc.criteria_weight";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id, $evaluated_user_id]);
        $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql_total = "SELECT AVG(total_score) as final_average 
                      FROM evaluations 
                      WHERE group_id = ? AND evaluated_user_id = ?";
        $stmt_total = $this->db->prepare($sql_total);
        $stmt_total->execute([$group_id, $evaluated_user_id]);
        $total = $stmt_total->fetch(PDO::FETCH_ASSOC);

        return [
            'criteria_scores' => $scores,
            'final_average' => $total['final_average']
        ];
    }

    public function submitFeedback($group_id, $user_id, $feedback_content) {
        $sql = "INSERT INTO rubric_feedback (group_id, user_id, feedback_content, created_at)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE feedback_content = ?, created_at = NOW()";
        
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$group_id, $user_id, $feedback_content, $feedback_content]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getMemberFeedback($group_id, $user_id) {
        $sql = "SELECT feedback_content FROM rubric_feedback 
                WHERE group_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id, $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['feedback_content'] : '';
    }

    // ===================================================
    // HÀM MỚI 1: Lấy tất cả tiêu chí của nhóm (CRUD)
    // ===================================================
    public function getCriteriaByGroup($group_id) {
        $sql = "SELECT * FROM group_rubric_criteria WHERE group_id = ? ORDER BY criteria_id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===================================================
    // HÀM MỚI 2: Thêm tiêu chí (CRUD)
    // ===================================================
    public function addCriteria($group_id, $name, $weight) {
        // Chuyển 30% -> 0.30
        $weight_decimal = $weight / 100.0;
        
        $sql = "INSERT INTO group_rubric_criteria (group_id, criteria_name, criteria_weight) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$group_id, $name, $weight_decimal]);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    // ===================================================
    // HÀM MỚI 3: Xóa tiêu chí (CRUD)
    // ===================================================
    public function deleteCriteria($criteria_id) {
        $sql = "DELETE FROM group_rubric_criteria WHERE criteria_id = ?";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$criteria_id]);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    // ===================================================
    // HÀM MỚI 4: Lấy tổng trọng số (để kiểm tra 100%)
    // ===================================================
    public function getCriteriaWeightSum($group_id) {
        $sql = "SELECT SUM(criteria_weight) as total_weight FROM group_rubric_criteria WHERE group_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        return $stmt->fetchColumn();
    }
}
?>