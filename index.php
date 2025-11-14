<?php
// index.php (ĐÃ CẬP NHẬT)

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config/database.php';

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

        case 'create_meeting': case 'save_minutes': case 'submit_meeting_rating':
            require_once 'app/controllers/MeetingController.php';
            $meetingController = new MeetingController($db);
            if ($action == 'create_meeting') $meetingController->create();
            if ($action == 'save_minutes') $meetingController->saveMinutes();
            if ($action == 'submit_meeting_rating') $meetingController->submitRating();
            break;
            
        case 'send_message': 
        case 'send_file':
        case 'handleReaction':
        case 'getNewMessages': // *** (THÊM MỚI) ***
            require_once 'app/controllers/ChatController.php';
            $chatController = new ChatController($db);
            if ($action == 'send_message') $chatController->sendMessage();
            if ($action == 'send_file') $chatController->sendFile();
            if ($action == 'handleReaction') $chatController->handleReaction();
            if ($action == 'getNewMessages') $chatController->getNewMessages(); // *** (THÊM MỚI) ***
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
    // PHẦN PAGE (Không thay đổi)
    switch ($page) {
        case 'register': require 'app/views/auth/register.php'; break;
        case 'login': case 'home': default: require 'app/views/auth/login.php'; break;
        case 'dashboard': require 'app/views/dashboard.php'; break;
        
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

        case 'group_report':
            require_once 'app/controllers/ReportController.php';
            $reportController = new ReportController($db);
            $reportController->show();
            break;

        case 'anonymous_feedback':
            require 'app/views/anonymous_feedback.php';
            break;
    }
}
?>