<?php
// app/models/Rubric.php

class Rubric {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Gửi (submit) một bài đánh giá - Dùng schema của BẠN
     */
    public function submitEvaluation($data, $scores_with_weights, $total_score) {
        try {
            $this->db->beginTransaction();

            // ===================================================
            // SỬA LỖI Ở ĐÂY: Xóa 'task_id' khỏi câu lệnh INSERT
            // ===================================================
            $sql_eval = "INSERT INTO evaluations 
                            (group_id, evaluator_user_id, evaluated_user_id, total_score, created_at)
                         VALUES (?, ?, ?, ?, NOW())";
            
            $stmt_eval = $this->db->prepare($sql_eval);
            
            // SỬA LỖI Ở ĐÂY: Xóa $data['task_id'] khỏi execute()
            $stmt_eval->execute([
                $data['group_id'],
                $data['evaluator_user_id'],
                $data['evaluated_user_id'],
                $total_score
            ]);
            // ===================================================

            $evaluation_id = $this->db->lastInsertId();

            // (Phần lưu `evaluation_scores` đã đúng, giữ nguyên)
            foreach ($scores_with_weights as $criteria_name => $values) {
                
                $sql_detail = "INSERT INTO evaluation_scores 
                                (evaluation_id, criteria, score, weight)
                             VALUES (?, ?, ?, ?)";
                             
                $stmt_detail = $this->db->prepare($sql_detail);
                $stmt_detail->execute([
                    $evaluation_id,
                    $criteria_name,
                    $values['score'],
                    $values['weight']
                ]);
            }

            $this->db->commit();
            return true; // Trả về true nếu thành công

        } catch (PDOException $e) {
            $this->db->rollBack();
            // Trả về chuỗi lỗi CSDL
            return $e->getMessage();
        }
    }
}
?>