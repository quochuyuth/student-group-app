<?php
// index.php

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config/database.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : null;

if ($action) {
    // PHẦN ACTION (XỬ LÝ FORM) - ĐÃ ĐÚNG
    switch ($action) {
        case 'register':
        case 'login':
        case 'logout':
            require_once 'app/controllers/AuthController.php';
            $authController = new AuthController($db);
            if ($action == 'register') $authController->register();
            if ($action == 'login') $authController->login();
            if ($action == 'logout') $authController->logout();
            break;
        case 'update_profile':
            require_once 'app/controllers/UserController.php';
            $userController = new UserController($db);
            $userController->updateProfile();
            break;
        case 'create_group':
        case 'invite_member':
        case 'accept_invitation':
        case 'reject_invitation':
            require_once 'app/controllers/GroupController.php';
            $groupController = new GroupController($db);
            if ($action == 'create_group') $groupController->create();
            if ($action == 'invite_member') $groupController->inviteMember();
            if ($action == 'accept_invitation') $groupController->acceptInvitation();
            if ($action == 'reject_invitation') $groupController->rejectInvitation();
            break;
        case 'create_task':
        case 'update_task_status':
        case 'get_task_details':
        case 'add_task_comment':
            require_once 'app/controllers/TaskController.php';
            $taskController = new TaskController($db);
            if ($action == 'create_task') $taskController->create();
            if ($action == 'update_task_status') $taskController->updateStatus();
            if ($action == 'get_task_details') $taskController->getDetails();
            if ($action == 'add_task_comment') $taskController->addComment();
            break;
        case 'submit_rubric':
            require_once 'app/controllers/RubricController.php';
            $rubricController = new RubricController($db);
            $rubricController->submit();
            break;
        case 'create_meeting':
            require_once 'app/controllers/MeetingController.php';
            $meetingController = new MeetingController($db);
            $meetingController->create();
            break;
        case 'save_minutes':
            require_once 'app/controllers/MeetingController.php';
            $meetingController = new MeetingController($db);
            $meetingController->saveMinutes();
            break;
        case 'submit_meeting_rating':
            require_once 'app/controllers/MeetingController.php';
            $meetingController = new MeetingController($db);
            $meetingController->submitRating();
            break;
    }
} 
else {
    // PHẦN PAGE (HIỂN THỊ GIAO DIỆN) - ĐÃ SỬA LỖI
    switch ($page) {

        // ===========================================
        // SỬA LỖI: TÁCH 'register' RA KHỎI 'login'
        // ===========================================
        case 'register':
            require 'app/views/auth/register.php';
            break;

        case 'login':
        case 'home':
        default:
            require 'app/views/auth/login.php';
            break;
        // ===========================================

        case 'dashboard':
            require 'app/views/dashboard.php';
            break;
        case 'profile':
            require_once 'app/controllers/UserController.php';
            $userController = new UserController($db);
            $userController->showProfile();
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
        case 'group_rubric':
            require_once 'app/controllers/RubricController.php';
            $rubricController = new RubricController($db);
            $rubricController->showForm();
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
    }
}
?>