<?php
// app/models/Meeting.php

class Meeting {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // ... (Giữ nguyên các hàm: getMeetingsByGroupId, create, getMeetingById, updateMinutes) ...
    public function getMeetingsByGroupId($group_id) {
        $sql = "SELECT m.*, u.username AS creator_name
                FROM meetings m
                JOIN users u ON m.created_by_user_id = u.user_id
                WHERE m.group_id = ?
                ORDER BY m.start_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = "INSERT INTO meetings (group_id, meeting_title, start_time, agenda, created_by_user_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([
                $data['group_id'], $data['meeting_title'],
                $data['start_time'], $data['agenda'], $data['created_by_user_id']
            ]);
        } catch (PDOException $e) { return false; }
    }
    public function getMeetingById($meeting_id) {
        $sql = "SELECT m.*, u.username AS creator_name
                FROM meetings m
                JOIN users u ON m.created_by_user_id = u.user_id
                WHERE m.meeting_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$meeting_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateMinutes($meeting_id, $minutes, $action_items) {
        $sql = "UPDATE meetings SET minutes = ?, action_items = ? WHERE meeting_id = ?";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$minutes, $action_items, $meeting_id]);
        } catch (PDOException $e) { return false; }
    }

    // ===================================================
    // HÀM MỚI 1: LƯU ĐÁNH GIÁ (1-5 SAO)
    // ===================================================
    /**
     * Dùng ON DUPLICATE KEY UPDATE:
     * Nếu user chưa đánh giá -> INSERT
     * Nếu user đã đánh giá rồi -> UPDATE (thay đổi điểm)
     */
    public function submitRating($meeting_id, $user_id, $rating) {
        $sql = "INSERT INTO meeting_attendance (meeting_id, user_id, satisfaction_rating)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE satisfaction_rating = ?";
        
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$meeting_id, $user_id, $rating, $rating]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // ===================================================
    // HÀM MỚI 2: LẤY ĐÁNH GIÁ CỦA USER HIỆN TẠI
    // ===================================================
    public function getUserRating($meeting_id, $user_id) {
        $sql = "SELECT satisfaction_rating FROM meeting_attendance
                WHERE meeting_id = ? AND user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$meeting_id, $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['satisfaction_rating'] : null; // Trả về điểm (1-5) hoặc null
    }
}
?>