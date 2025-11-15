<?php
// app/controllers/GroupController.php (ĐÃ CẬP NHẬT)

require_once 'app/models/Group.php';
require_once 'app/models/User.php';
require_once 'app/models/Task.php';
require_once 'app/models/Chat.php';
require_once 'app/models/Poll.php';

class GroupController {
    private $db;
    private $groupModel;
    private $userModel;
    private $taskModel;
    private $chatModel;
    private $pollModel; 

    public function __construct($db) {
        $this->db = $db;
        $this->groupModel = new Group($this->db);
        $this->userModel = new User($this->db);
        $this->taskModel = new Task($this->db);
        $this->chatModel = new Chat($this->db);
        $this->pollModel = new Poll($this->db);
    }

    /**
     * (SỬA ĐỔI) Lấy thêm $unread_counts
     */
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login'); exit;
        }
        $user_id = $_SESSION['user_id'];
        $groups = $this->groupModel->getGroupsByUserId($user_id);
        $invitations = $this->groupModel->getPendingInvitationsByUserId($user_id);
        
        // *** (DÒNG MỚI) ***
        // Lấy số tin nhắn chưa đọc cho TỪNG nhóm
        $unread_counts_raw = $this->chatModel->getUnreadCountsByGroup($user_id);
        
        // (MỚI) Chuyển mảng đếm [ {group_id: 1, unread_count: 5}, ... ]
        // thành mảng [ 1 => 5, ... ] để View dễ tra cứu
        $unread_counts = [];
        foreach ($unread_counts_raw as $item) {
            $unread_counts[$item['group_id']] = $item['unread_count'];
        }

        require 'app/views/groups.php'; // Truyền $unread_counts vào view
    }
    
    // ... (Giữ nguyên các hàm: create, inviteMember, acceptInvitation, rejectInvitation) ...
    public function create() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
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
        header('Location: index.php?page=groups'); exit;
    }
    public function inviteMember() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
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
        header($redirect_url); exit;
    }
    public function acceptInvitation() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
        }
        $invitation_id = (int)$_POST['invitation_id'];
        $group_id = (int)$_POST['group_id'];
        $user_id = $_SESSION['user_id'];
        if ($this->groupModel->acceptInvitation($invitation_id, $group_id, $user_id)) {
            $_SESSION['flash_message'] = "Bạn đã tham gia nhóm thành công!";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể chấp nhận lời mời.";
        }
        header('Location: index.php?page=groups'); exit;
    }
    public function rejectInvitation() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
        }
        $invitation_id = (int)$_POST['invitation_id'];
        if ($this->groupModel->rejectInvitation($invitation_id)) {
            $_SESSION['flash_message'] = "Bạn đã từ chối lời mời.";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể từ chối lời mời.";
        }
        header('Location: index.php?page=groups'); exit;
    }

    /**
     * (GIỮ NGUYÊN)
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
        
        $group = $this->groupModel->getGroupById($group_id);
        if (!$group) {
            $_SESSION['flash_message'] = "Không tìm thấy nhóm này.";
            header('Location: index.php?page=groups');
            exit;
        }
        
        $tasks = $this->taskModel->getTasksByGroupId($group_id);
        $members = $this->groupModel->getMembersByGroupId($group_id);
        
        $current_user_role = $this->groupModel->getUserRoleInGroup($group_id, $_SESSION['user_id']);

        require 'app/views/group_details.php';
    }

    /**
     * (GIỮ NGUYÊN) ĐÁNH DẤU ĐÃ XEM KHI VÀO CHAT
     */
    public function showChat() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        $group_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($group_id == 0) {
            header('Location: index.php?page=groups');
            exit;
        }
        
        $group = $this->groupModel->getGroupById($group_id);
        if (!$group) {
            $_SESSION['flash_message'] = "Không tìm thấy nhóm này.";
            header('Location: index.php?page=groups');
            exit;
        }
        
        // Đánh dấu nhóm này là "đã xem" khi người dùng tải trang
        $this->chatModel->markGroupAsSeen($_SESSION['user_id'], $group_id);

        $filter_user_id = $_GET['filter_user'] ?? null;
        $filter_date_from = $_GET['filter_date_from'] ?? null;
        $filter_date_to = $_GET['filter_date_to'] ?? null;

        $members = $this->groupModel->getMembersByGroupId($group_id);
        $messages = $this->chatModel->getMessagesByGroupId($group_id);
        $polls = $this->pollModel->getPollsByGroupId($group_id);
        
        $chat_files = $this->chatModel->getChatFilesByGroupId($group_id, $filter_user_id, $filter_date_from, $filter_date_to);
        
        $message_ids = array_column($messages, 'message_id');
        $reactions = $this->chatModel->getReactionsForMessages($message_ids);

        $user_votes = [];
        foreach ($polls as $poll) {
            $user_vote = $this->pollModel->getUserVote($poll['poll_id'], $_SESSION['user_id']);
            if ($user_vote) {
                $user_votes[$poll['poll_id']] = $user_vote;
            }
        }
        
        $current_user_role = $this->groupModel->getUserRoleInGroup($group_id, $_SESSION['user_id']);
        
        require 'app/views/group_chat.php';
    }

    /**
     * (GIỮ NGUYÊN)
     */
    public function removeMember() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login'); exit;
        }

        $group_id = (int)$_POST['group_id'];
        $user_id_to_remove = (int)$_POST['user_id'];
        $current_user_id = $_SESSION['user_id'];
        
        $redirect_page = $_POST['redirect_page'] ?? 'group_details';
        $redirect_url = "Location: index.php?page=" . $redirect_page . "&id=" . $group_id;

        $role = $this->groupModel->getUserRoleInGroup($group_id, $current_user_id);
        if ($role !== 'admin') {
            $_SESSION['flash_message'] = "Lỗi: Bạn không có quyền thực hiện hành động này.";
            header($redirect_url); exit;
        }
        if ($user_id_to_remove == $current_user_id) {
            $_SESSION['flash_message'] = "Lỗi: Bạn không thể tự xóa chính mình.";
            header($redirect_url); exit;
        }
        $role_to_remove = $this->groupModel->getUserRoleInGroup($group_id, $user_id_to_remove);
        if ($role_to_remove === 'admin') {
             $_SESSION['flash_message'] = "Lỗi: Bạn không thể xóa một trưởng nhóm khác.";
             header($redirect_url); exit;
        }

        if ($this->groupModel->removeMember($group_id, $user_id_to_remove)) {
            $_SESSION['flash_message'] = "Đã xóa thành viên khỏi nhóm.";
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể xóa thành viên.";
        }
        header($redirect_url); exit;
    }
}
?>