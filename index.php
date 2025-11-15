<?php
// index.php (Bản HOÀN CHỈNH - Đã GỠ logic đếm tổng chat)

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config/database.php'; // $db được tạo ở đây

// ===================================================
// (MỚI) LOGIC LẤY THÔNG BÁO TOÀN TRANG
// ===================================================
$upcoming_tasks = [];
$upcoming_meetings = [];
$notification_count = 0;
$pending_invitation_count = 0;
// $unread_chat_count = 0; // (ĐÃ XÓA)
$total_group_notifications = 0;

if (isset($_SESSION['user_id'])) {
    $current_user_id_for_header = $_SESSION['user_id'];
    
    // 1. Tải Model
    require_once 'app/models/Task.php';
    require_once 'app/models/Meeting.php';
    require_once 'app/models/Group.php';
    require_once 'app/models/Chat.php'; // Vẫn cần ChatModel cho GroupController
    
    // 2. Khởi tạo Model
    $taskModelForHeader = new Task($db); 
    $meetingModelForHeader = new Meeting($db);
    $groupModelForHeader = new Group($db);
    // $chatModelForHeader = new Chat($db); // (ĐÃ XÓA)

    // 3. Lấy dữ liệu cho Chuông (Task + Họp)
    $upcoming_tasks = $taskModelForHeader->getUpcomingDueTasks($current_user_id_for_header, 3); // 3 ngày
    $upcoming_meetings = $meetingModelForHeader->getUpcomingMeetings($current_user_id_for_header, 12); // 12 tiếng
    $notification_count = count($upcoming_tasks) + count($upcoming_meetings);

    // 4. Lấy dữ liệu cho Menu "Các nhóm của tôi" (Chỉ Lời mời)
    $pending_invitation_count = count($groupModelForHeader->getPendingInvitationsByUserId($current_user_id_for_header));
    // $unread_chat_count = $chatModelForHeader->getUnreadMessageCount($current_user_id_for_header); // (ĐÃ XÓA)
    $total_group_notifications = $pending_invitation_count; // (ĐÃ SỬA)
}
// ===================================================


$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : null;

if ($action) {
    // PHẦN ACTION
    switch ($action) {
        case 'register': case 'login': case 'logout':
            require_once 'app/controllers/AuthController.php';
            $authController = new AuthController($db);
            if ($action == 'register') $authController->register();
            if ($action == 'login') $authController->login();
            if ($action == 'logout') $authController->logout();
            break;
        
        case 'update_profile':
        case 'upload_avatar':
            require_once 'app/controllers/UserController.php';
            $userController = new UserController($db);
            if ($action == 'update_profile') $userController->updateProfile();
            if ($action == 'upload_avatar') $userController->uploadAvatar();
            break;

        case 'create_group': 
        case 'invite_member': 
        case 'accept_invitation': 
        case 'reject_invitation':
        case 'remove_member':
            require_once 'app/controllers/GroupController.php';
            $groupController = new GroupController($db);
            if ($action == 'create_group') $groupController->create();
            if ($action == 'invite_member') $groupController->inviteMember();
            if ($action == 'accept_invitation') $groupController->acceptInvitation();
            if ($action == 'reject_invitation') $groupController->rejectInvitation();
            if ($action == 'remove_member') $groupController->removeMember();
            break;
            
        case 'create_task': case 'update_task_status': case 'get_task_details': case 'add_task_comment': case 'attach_file_to_task':
            require_once 'app/controllers/TaskController.php';
            $taskController = new TaskController($db);
            if ($action == 'create_task') $taskController->create();
            if ($action == 'update_task_status') $taskController->updateStatus();
            if ($action == 'get_task_details') $taskController->getDetails();
            if ($action == 'add_task_comment') $taskController->addComment();
            if ($action == 'attach_file_to_task') $taskController->attachFile();
            break;

        case 'submit_rubric':
        case 'submit_feedback':
        case 'get_member_feedback':
        case 'add_criteria':
        case 'delete_criteria':
            require_once 'app/controllers/RubricController.php';
            $rubricController = new RubricController($db);
            if ($action == 'submit_rubric') $rubricController->submit();
            if ($action == 'submit_feedback') $rubricController->submitFeedback();
            if ($action == 'get_member_feedback') $rubricController->getMemberFeedbackAjax();
            if ($action == 'add_criteria') $rubricController->addCriteria();
            if ($action == 'delete_criteria') $rubricController->deleteCriteria();
            break;

        case 'create_meeting': 
        case 'save_minutes': 
        case 'submit_meeting_rating':
            require_once 'app/controllers/MeetingController.php';
            $meetingController = new MeetingController($db);
            if ($action == 'create_meeting') $meetingController->create();
            if ($action == 'save_minutes') $meetingController->saveMinutes();
            if ($action == 'submit_meeting_rating') $meetingController->submitRating();
            break;
            
        case 'send_message': 
        case 'send_file':
        case 'handleReaction':
        case 'getNewMessages': 
            require_once 'app/controllers/ChatController.php';
            $chatController = new ChatController($db);
            if ($action == 'send_message') $chatController->sendMessage();
            if ($action == 'send_file') $chatController->sendFile();
            if ($action == 'handleReaction') $chatController->handleReaction();
            if ($action == 'getNewMessages') $chatController->getNewMessages(); 
            break;
            
        case 'create_poll': case 'submit_vote':
            require_once 'app/controllers/PollController.php';
            $pollController = new PollController($db);
            if ($action == 'create_poll') $pollController->create();
            if ($action == 'submit_vote') $pollController->vote();
            break;
    }
} 
else {
    // PHẦN PAGE
    switch ($page) {
        case 'register': require 'app/views/auth/register.php'; break;
        case 'login': case 'home': default: require 'app/views/auth/login.php'; break;
        
        case 'dashboard':
            // (Đã dời logic lấy Model ra ngoài top-level)
            // (Chỉ cần lấy $pieChartData)
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                // Lấy dữ liệu cho 4 thẻ (biến đã có sẵn từ top-level)
                $card1_title = "Tổng số Nhóm";
                $card1_value = count($groupModelForHeader->getGroupsByUserId($user_id));
                $card1_icon = "fa-layer-group";
                $card1_color = "primary";

                $card2_title = "Tổng số Task";
                $card2_value = $taskModelForHeader->getTotalTasksByUserId($user_id); 
                $card2_icon = "fa-tasks";
                $card2_color = "success";

                $card3_title = "Task Cần Làm";
                $card3_value = $taskModelForHeader->getPendingTasksByUserId($user_id); 
                $card3_icon = "fa-clipboard-list";
                $card3_color = "info";

                $card4_title = "Lời mời chờ";
                $card4_value = $pending_invitation_count; // Dùng biến global
                $card4_icon = "fa-envelope";
                $card4_color = "warning";
                
                // Lấy dữ liệu cho biểu đồ tròn
                $pieChartData = $taskModelForHeader->getTaskProgressByUserId($user_id);

            } else {
                header('Location: index.php?page=login');
                exit;
            }
            require 'app/views/dashboard.php'; 
            break;
        
        case 'profile':
            require_once 'app/controllers/UserController.php';
            $userController = new UserController($db);
            $userController->viewProfile();
            break;
        case 'edit_profile': 
            require_once 'app/controllers/UserController.php';
            $userController = new UserController($db);
            $userController->showEditProfile();
            break;

        case 'groups':
            require_once 'app/controllers/GroupController.php';
            $groupController = new GroupController($db);
            $groupController->index();
            break;
        case 'group_details':
            require_once 'app/controllers/GroupController.php';
            $groupController = new GroupController($db);
            $groupController->show();
            break;
        
        case 'group_chat':
            require_once 'app/controllers/GroupController.php';
            $groupController = new GroupController($db);
            $groupController->showChat();
            break;
        
        case 'group_rubric':
            require_once 'app/controllers/RubricController.php';
            $rubricController = new RubricController($db);
            $rubricController->showForm();
            break;
        
        case 'manage_rubric':
            require_once 'app/controllers/RubricController.php';
            $rubricController = new RubricController($db);
            $rubricController->showManager();
            break;
            
        case 'group_meetings':
            require_once 'app/controllers/MeetingController.php';
            $meetingController = new MeetingController($db);
            $meetingController->index();
            break;
        case 'meeting_details':
            require_once 'app/controllers/MeetingController.php';
            $meetingController = new MeetingController($db);
            $meetingController->showDetails();
            break;
        case 'join_meeting':
    require_once 'app/controllers/MeetingController.php';
    $meetingController = new MeetingController($db);
    $meetingController->joinMeeting();
    break;

        case 'group_report':
            require_once 'app/controllers/ReportController.php';
            $reportController = new ReportController($db);
            $reportController->show();
            break;

        case 'anonymous_feedback':
            require 'app/views/anonymous_feedback.php';
            break;
            
        case 'all_tasks':
            require_once 'app/controllers/TaskController.php';
            $taskController = new TaskController($db);
            $taskController->showAllTasks(); // Lấy tất cả
            break;
        case 'pending_tasks':
            require_once 'app/controllers/TaskController.php';
            $taskController = new TaskController($db);
            $taskController->showAllTasks('pending'); // Chỉ lấy task Cần làm
            break;
    }
}
?>