<?php
// app/models/Group.php

class Group {
    private $db;
    // ... (Giữ nguyên các hàm cũ: __construct, getGroupsByUserId, create, getGroupById, isUserInGroup, hasPendingInvitation, createInvitation, getPendingInvitationsByUserId, acceptInvitation, rejectInvitation) ...
    
    public function __construct($db) {
        $this->db = $db;
    }

    public function getGroupsByUserId($user_id) {
        $sql = "SELECT g.group_id, g.group_name, g.group_description, gm.role 
                FROM groups g
                JOIN group_members gm ON g.group_id = gm.group_id
                WHERE gm.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($group_name, $group_description, $admin_user_id) {
        try {
            $this->db->beginTransaction();
            $sql_group = "INSERT INTO groups (group_name, group_description, created_by_user_id) VALUES (?, ?, ?)";
            $stmt_group = $this->db->prepare($sql_group);
            $stmt_group->execute([$group_name, $group_description, $admin_user_id]);
            $group_id = $this->db->lastInsertId();
            $sql_member = "INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, ?)";
            $stmt_member = $this->db->prepare($sql_member);
            $stmt_member->execute([$group_id, $admin_user_id, 'admin']);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getGroupById($group_id) {
        $sql = "SELECT * FROM groups WHERE group_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isUserInGroup($group_id, $user_id) {
        $sql = "SELECT * FROM group_members WHERE group_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id, $user_id]);
        return $stmt->fetch() !== false;
    }

    public function hasPendingInvitation($group_id, $invitee_user_id) {
        $sql = "SELECT * FROM group_invitations WHERE group_id = ? AND invited_user_id = ? AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id, $invitee_user_id]);
        return $stmt->fetch() !== false;
    }

    public function createInvitation($group_id, $inviter_user_id, $invitee_user_id) {
        $sql = "INSERT INTO group_invitations (group_id, invited_by_user_id, invited_user_id, status) VALUES (?, ?, ?, 'pending')";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$group_id, $inviter_user_id, $invitee_user_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getPendingInvitationsByUserId($user_id) {
        $sql = "SELECT inv.invitation_id, g.group_id, g.group_name, u.username AS inviter_name
                FROM group_invitations inv
                JOIN groups g ON inv.group_id = g.group_id
                JOIN users u ON inv.invited_by_user_id = u.user_id
                WHERE inv.invited_user_id = ? AND inv.status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function acceptInvitation($invitation_id, $group_id, $user_id) {
        try {
            $this->db->beginTransaction();
            $sql_member = "INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'member')";
            $stmt_member = $this->db->prepare($sql_member);
            $stmt_member->execute([$group_id, $user_id]);
            $sql_delete = "DELETE FROM group_invitations WHERE invitation_id = ?";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->execute([$invitation_id]);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function rejectInvitation($invitation_id) {
        $sql_delete = "DELETE FROM group_invitations WHERE invitation_id = ?";
        $stmt_delete = $this->db->prepare($sql_delete);
        try {
            return $stmt_delete->execute([$invitation_id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // ===================================================
    // HÀM MỚI: LẤY DANH SÁCH THÀNH VIÊN CỦA NHÓM
    // ===================================================
    public function getMembersByGroupId($group_id) {
        $sql = "SELECT u.user_id, u.username 
                FROM users u
                JOIN group_members gm ON u.user_id = gm.user_id
                WHERE gm.group_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>