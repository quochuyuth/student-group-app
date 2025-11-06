<?php
// app/controllers/GroupController.php

require_once 'app/models/Group.php';
require_once 'app/models/User.php';
require_once 'app/models/Task.php'; // THÊM DÒNG NÀY

class GroupController {
    private $db;
    private $groupModel;
    private $userModel;
    private $taskModel; // THÊM BIẾN NÀY

    public function __construct($db) {
        $this->db = $db;
        $this->groupModel = new Group($this->db);
        $this->userModel = new User($this->db);
        $this->taskModel = new Task($this->db); // THÊM DÒNG NÀY
    }

    // ... (Giữ nguyên các hàm: index, create, inviteMember, acceptInvitation, rejectInvitation) ...
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        $user_id = $_SESSION['user_id'];
        $groups = $this->groupModel->getGroupsByUserId($user_id);
        $invitations = $this->groupModel->getPendingInvitationsByUserId($user_id);
        require 'app/views/groups.php';
    }
    public function create() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }
        $group_name = strip_tags($_POST['group_name']);
        $group_description = strip_tags($_POST['group_description']);
        $admin_user_id = $_SESSION['user_id'];
        if (empty($group_name)) {
            $_SESSION['flash_message'] = "Tên nhóm không được để trống!";
        } else {
            if ($this->groupModel->create($group_name, $group_description, $admin_user_id)) {
                $_SESSION['flash_message'] = "Tạo nhóm thành công!";
            } else {
                $_SESSION['flash_message'] = "Tạo nhóm thất bại. Vui lòng thử lại.";
            }
        }
        header('Location: index.php?page=groups');
        exit;
    }
    public function inviteMember() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }
        $group_id = (int)$_POST['group_id'];
        $email_or_username = trim(strip_tags($_POST['email_or_username']));
        $inviter_user_id = $_SESSION['user_id'];
        $redirect_url = "Location: index.php?page=group_details&id=$group_id";
        $invitee = $this->userModel->findByEmail($email_or_username);
        if (!$invitee) { $invitee = $this->userModel->findByUsername($email_or_username); }
        if (!$invitee) {
            $_SESSION['flash_message'] = "Lỗi: Không tìm thấy người dùng với email/username này.";
            header($redirect_url); exit;
        }
        $invitee_user_id = $invitee['user_id'];
        if ($invitee_user_id == $inviter_user_id) {
            $_SESSION['flash_message'] = "Lỗi: Bạn không thể tự mời chính mình.";
            header($redirect_url); exit;
        }
        if ($this->groupModel->isUserInGroup($group_id, $invitee_user_id)) {
            $_SESSION['flash_message'] = "Lỗi: Người dùng này đã ở trong nhóm.";
            header($redirect_url); exit;
        }
        if ($this->groupModel->hasPendingInvitation($group_id, $invitee_user_id)) {
            $_SESSION['flash_message'] = "Lỗi: Bạn đã mời người này rồi, đang chờ họ đồng ý.";
            header($redirect_url); exit;
        }
        if ($this->groupModel->createInvitation($group_id, $inviter_user_id, $invitee_user_id)) {
            $_SESSION['flash_message'] = "Gửi lời mời thành công!";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể gửi lời mời. Vui lòng thử lại.";
        }
        header($redirect_url);
        exit;
    }
    public function acceptInvitation() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }
        $invitation_id = (int)$_POST['invitation_id'];
        $group_id = (int)$_POST['group_id'];
        $user_id = $_SESSION['user_id'];
        if ($this->groupModel->acceptInvitation($invitation_id, $group_id, $user_id)) {
            $_SESSION['flash_message'] = "Bạn đã tham gia nhóm thành công!";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể chấp nhận lời mời.";
        }
        header('Location: index.php?page=groups');
        exit;
    }
    public function rejectInvitation() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login');
            exit;
        }
        $invitation_id = (int)$_POST['invitation_id'];
        if ($this->groupModel->rejectInvitation($invitation_id)) {
            $_SESSION['flash_message'] = "Bạn đã từ chối lời mời.";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể từ chối lời mời.";
        }
        header('Location: index.php?page=groups');
        exit;
    }

    /**
     * CẬP NHẬT HÀM NÀY:
     * Hiển thị trang chi tiết nhóm (gồm cả tasks và members)
     */
    public function show() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        $group_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($group_id == 0) {
            header('Location: index.php?page=groups');
            exit;
        }
        
        // 1. Lấy thông tin nhóm
        $group = $this->groupModel->getGroupById($group_id);
        if (!$group) {
            $_SESSION['flash_message'] = "Không tìm thấy nhóm này.";
            header('Location: index.php?page=groups');
            exit;
        }
        
        // 2. LẤY DANH SÁCH TASK (MỚI)
        $tasks = $this->taskModel->getTasksByGroupId($group_id);

        // 3. LẤY DANH SÁCH THÀNH VIÊN (MỚI)
        $members = $this->groupModel->getMembersByGroupId($group_id);

        // 4. Tải View và truyền cả 3 biến
        require 'app/views/group_details.php';
    }
}
?>