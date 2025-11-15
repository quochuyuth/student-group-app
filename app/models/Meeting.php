<?php
// app/models/Meeting.php

class Meeting {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // ... (Giữ nguyên các hàm: getMeetingsByGroupId, create) ...
    public function getMeetingsByGroupId($group_id) {
        $sql = "SELECT m.*, u.username AS creator_name
                FROM meetings m
                LEFT JOIN users u ON m.created_by_user_id = u.user_id
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


    /**
     * (SỬA LỖI) Đổi JOIN thành LEFT JOIN
     * Để tránh lỗi khi người tạo bị xóa
     */
    public function getMeetingById($meeting_id) {
        $sql = "SELECT m.*, u.username AS creator_name
                FROM meetings m
                LEFT JOIN users u ON m.created_by_user_id = u.user_id
                WHERE m.meeting_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$meeting_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // ... (Giữ nguyên các hàm: updateMinutes, submitRating, getUserRating, getUpcomingMeetings) ...
    public function updateMinutes($meeting_id, $minutes, $action_items) {
        $sql = "UPDATE meetings SET minutes = ?, action_items = ? WHERE meeting_id = ?";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$minutes, $action_items, $meeting_id]);
        } catch (PDOException $e) { return false; }
    }
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
    public function getUserRating($meeting_id, $user_id) {
        $sql = "SELECT satisfaction_rating FROM meeting_attendance
                WHERE meeting_id = ? AND user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$meeting_id, $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['satisfaction_rating'] : null;
    }
    public function getUpcomingMeetings($user_id, $hours_limit = 12) {
        $sql = "SELECT m.meeting_id, m.meeting_title, m.start_time, g.group_id, g.group_name
                FROM meetings m
                JOIN group_members gm ON m.group_id = gm.group_id
                JOIN groups g ON m.group_id = g.group_id
                WHERE gm.user_id = ?
                AND m.start_time > NOW() 
                AND m.start_time <= DATE_ADD(NOW(), INTERVAL ? HOUR)
                ORDER BY m.start_time ASC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $hours_limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>