<?php
// Tệp: app/views/layout/header.php (Bản HOÀN CHỈNH - Đã GỠ logic đếm tổng chat)
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// (SỬA LỖI) Thêm 'global' để lấy tất cả biến từ index.php
// ngay cả khi được gọi từ bên trong một Controller
global $upcoming_tasks, $upcoming_meetings, $notification_count;
// (SỬA) Gỡ bỏ $unread_chat_count
global $pending_invitation_count, $total_group_notifications; 

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Các biến này được $SESSION hoặc $_GET cung cấp, nên an toàn
$user_avatar = $_SESSION['user_avatar'] ?? 'http://localhost/StudentGroupApp/public/img/undraw_profile.svg'; 
$username = $_SESSION['username'] ?? 'User';
$current_page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>StudentGroupApp</title>

    <link href="http://localhost/StudentGroupApp/public/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="http://localhost/StudentGroupApp/public/css/sb-admin-2.min.css" rel="stylesheet">
    
    <link href="http://localhost/StudentGroupApp/public/css/custom.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?page=dashboard">
                <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-users"></i></div>
                <div class="sidebar-brand-text mx-3">StudentGroupApp</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i><span>Bảng điều khiển</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Quản lý</div>
            <li class="nav-item <?php echo (in_array($current_page, ['groups', 'group_details', 'group_chat', 'group_meetings', 'meeting_details', 'group_report', 'group_rubric', 'manage_rubric'])) ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=groups">
                    <i class="fas fa-fw fa-layer-group"></i><span>Các nhóm của tôi</span>
                    
                    <!-- (SỬA) Giờ chỉ hiển thị số lời mời -->
                    <?php if (($total_group_notifications ?? 0) > 0): ?>
                        <span class="badge badge-danger ml-2"><?php echo $total_group_notifications; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- (MỚI) Link đến Task Cần Làm -->
            <li class="nav-item <?php echo (in_array($current_page, ['all_tasks', 'pending_tasks'])) ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=pending_tasks">
                    <i class="fas fa-fw fa-tasks"></i><span>Task của tôi</span>
                </a>
            </li>
            
            <li class="nav-item <?php echo ($current_page == 'edit_profile' || $current_page == 'profile') ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=edit_profile">
                    <i class="fas fa-fw fa-user-cog"></i><span>Hồ sơ của tôi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-fw fa-sign-out-alt"></i><span>Đăng xuất</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- (SỬA) Hiển thị số lượng thông báo -->
                                <?php if (($notification_count ?? 0) > 0): ?>
                                    <span class="badge badge-danger badge-counter"><?php echo $notification_count; ?></span>
                                <?php endif; ?>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Trung tâm Thông báo
                                </h6>
                                
                                <!-- (SỬA) Dùng vòng lặp PHP để hiển thị thông báo -->
                                <?php if (($notification_count ?? 0) > 0): ?>
                                
                                    <!-- (MỚI) Vòng lặp cho HỌP -->
                                    <?php if (!empty($upcoming_meetings)): ?>
                                        <?php foreach ($upcoming_meetings as $meeting): ?>
                                            <a class="dropdown-item d-flex align-items-center" href="index.php?page=meeting_details&id=<?php echo $meeting['meeting_id']; ?>">
                                                <div class="mr-3">
                                                    <div class="icon-circle bg-primary">
                                                        <i class="fas fa-calendar-alt text-white"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="small text-gray-500">Họp: <?php echo date('d/m H:i', strtotime($meeting['start_time'])); ?></div>
                                                    <span class="font-weight-bold">
                                                        (<?php echo htmlspecialchars($meeting['group_name']); ?>) - <?php echo htmlspecialchars($meeting['meeting_title']); ?>
                                                    </span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <!-- Vòng lặp cho TASK -->
                                    <?php if (!empty($upcoming_tasks)): ?>
                                        <?php foreach ($upcoming_tasks as $task): ?>
                                            <a class="dropdown-item d-flex align-items-center" href="index.php?page=group_details&id=<?php echo $task['group_id']; ?>">
                                                <div class="mr-3">
                                                    <div class="icon-circle bg-warning">
                                                        <i class="fas fa-exclamation-triangle text-white"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="small text-gray-500">Task hết hạn: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?></div>
                                                    <span class="font-weight-bold">
                                                        (<?php echo htmlspecialchars($task['group_name']); ?>) - <?php echo htmlspecialchars($task['task_title']); ?>
                                                    </span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-success">
                                                <i class="fas fa-check text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="small text-gray-500">Tuyệt vời!</div>
                                            <span class="font-weight-bold">Bạn không có thông báo mới.</span>
                                        </div>
                                    </a>
                                <?php endif; ?>
                                
                                <a class="dropdown-item text-center small text-gray-500" href="index.php?page=pending_tasks">
                                    Hiển thị tất cả Task cần làm
                                </a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($username); ?></span>
                                <img class="img-profile rounded-circle" src="<?php echo htmlspecialchars($user_avatar); ?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="index.php?page=profile">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Xem Hồ sơ
                                </a>
                                <a class="dropdown-item" href="index.php?page=edit_profile">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i> Chỉnh sửa Hồ sơ
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Đăng xuất
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">