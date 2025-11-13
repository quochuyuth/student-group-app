<?php
// Tệp: app/views/layout/header.php (Bản SỬA LỖI cho SB Admin 2)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
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
            <li class="nav-item <?php echo (in_array($current_page, ['groups', 'group_details', 'group_meetings', 'meeting_details', 'group_report', 'group_rubric', 'manage_rubric'])) ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=groups">
                    <i class="fas fa-fw fa-layer-group"></i><span>Các nhóm của tôi</span>
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
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($username); ?></span>
                                <img class="img-profile rounded-circle" src="<?php echo htmlspecialchars($user_avatar); ?>">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="index.php?page=edit_profile">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Hồ sơ
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