<?php
// Tệp: app/views/auth/login.php (PHIÊN BẢN CỰC ĐẸP)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Đăng nhập - StudentGroupApp</title>
    <link href="public/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="public/css/sb-admin-2.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
             background: url('public/img/login-bg.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated background particles */
        body::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            background-image: 
                radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px),
                radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            background-position: 0 0, 25px 25px;
            animation: moveBackground 20s linear infinite;
        }
        
        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        .container {
            position: relative;
            z-index: 1;
        }
        
        .auth-card {
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
            background: white;
            position: relative;
        }
        
        .auth-card::before {
            content: "";
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #667eea);
            background-size: 300% 300%;
            border-radius: 25px;
            z-index: -1;
            animation: gradientBorder 3s ease infinite;
        }
        
        @keyframes gradientBorder {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .auth-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 80px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        /* CHỖ GÁN ẢNH */
        .auth-left::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* GÁN ẢNH TẠI ĐÂY: */
             background: url('public/img/login-bg.jpg') center/cover; 
            opacity: 0.15;
            z-index: 0;
        }
        
        /* Floating shapes */
        .auth-left::after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            top: -150px;
            right: -150px;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }
        
        .shape-1 {
            width: 150px;
            height: 150px;
            bottom: -75px;
            left: -75px;
            animation: float 8s ease-in-out infinite;
        }
        
        .shape-2 {
            width: 100px;
            height: 100px;
            top: 50%;
            right: 20px;
            animation: float 7s ease-in-out infinite 1s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }
        
        .auth-left > * {
            position: relative;
            z-index: 1;
        }
        
        .logo-container {
            position: relative;
            margin-bottom: 30px;
        }
        
        .logo-circle {
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255,255,255,0.3);
            animation: pulse 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(255,255,255,0); }
        }
        
        .logo-circle i {
            font-size: 60px;
            color: white;
        }
        
        .auth-left h1 {
            color: white;
            font-weight: 800;
            margin-bottom: 15px;
            font-size: 2.5rem;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.2);
            letter-spacing: 1px;
        }
        
        .auth-left p {
            color: rgba(255,255,255,0.95);
            text-align: center;
            line-height: 1.8;
            font-size: 1.1rem;
            max-width: 350px;
        }
        
        .features {
            margin-top: 40px;
            display: flex;
            gap: 30px;
        }
        
        .feature-item {
            text-align: center;
        }
        
        .feature-item i {
            font-size: 30px;
            margin-bottom: 10px;
            display: block;
        }
        
        .feature-item span {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .auth-right {
            padding: 80px 60px;
            position: relative;
        }
        
        .auth-right h2 {
            color: #2c3e50;
            font-weight: 800;
            margin-bottom: 10px;
            font-size: 2.2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-right .subtitle {
            color: #7f8c8d;
            margin-bottom: 50px;
            font-size: 1.1rem;
        }
        
        .form-group {
            margin-bottom: 30px;
            position: relative;
        }
        
        .form-label {
            display: block;
            margin-bottom: 10px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 25px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 18px;
            transition: all 0.3s;
        }
        
        .form-control-modern {
            width: 100%;
            padding: 18px 25px 18px 60px;
            border: 2px solid #e3e6f0;
            border-radius: 15px;
            font-size: 16px;
            transition: all 0.3s;
            background: #f8f9fc;
        }
        
        .form-control-modern:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }
        
        .form-control-modern:focus + .input-icon {
            color: #764ba2;
            transform: translateY(-50%) scale(1.1);
        }
        
        .btn-modern {
            width: 100%;
            padding: 18px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .btn-modern::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }
        
        .btn-modern:hover::before {
            left: 100%;
        }
        
        .btn-modern:active {
            transform: translateY(-1px);
        }
        
        .divider {
            margin: 40px 0;
            text-align: center;
            position: relative;
        }
        
        .divider:before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #e3e6f0, transparent);
        }
        
        .divider span {
            background: white;
            padding: 0 20px;
            position: relative;
            color: #858796;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .switch-link {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: 15px;
        }
        
        .switch-link p {
            margin: 0;
            color: #5a6c7d;
            font-size: 15px;
        }
        
        .switch-link a {
            color: #667eea;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
        }
        
        .switch-link a::after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s;
        }
        
        .switch-link a:hover::after {
            width: 100%;
        }
        
        .alert {
            border-radius: 15px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 30px;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Password strength indicator */
        .password-strength {
            height: 4px;
            background: #e3e6f0;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, #ff4757, #ffa502, #2ed573);
            transition: width 0.3s;
        }
        
        @media (max-width: 991px) {
            .auth-left {
                display: none;
            }
            
            .auth-right {
                padding: 60px 40px;
            }
        }
        
        @media (max-width: 575px) {
            .auth-right {
                padding: 40px 25px;
            }
            
            .auth-right h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="card auth-card border-0">
                    <div class="row no-gutters">
                        <!-- PHẦN BÊN TRÁI -->
                        <div class="col-lg-5 d-none d-lg-block auth-left">
                            <div class="shape shape-1"></div>
                            <div class="shape shape-2"></div>
                            
                            <div class="logo-container">
                                <div class="logo-circle">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                            </div>
                            
                            <h1>StudentGroupApp</h1>
                            <p>Nền tảng quản lý nhóm học tập thông minh, kết nối và chia sẻ kiến thức hiệu quả</p>
                            
                            <div class="features">
                                <div class="feature-item">
                                    <i class="fas fa-tasks"></i>
                                    <span>Quản lý<br>Task</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-comments"></i>
                                    <span>Chat<br>Realtime</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-video"></i>
                                    <span>Meeting<br>Online</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PHẦN BÊN PHẢI -->
                        <div class="col-lg-7">
                            <div class="auth-right">
                                <h2>Đăng nhập</h2>
                                <p class="subtitle">Chào mừng bạn quay trở lại! 👋</p>

                                <?php
                                if (isset($_SESSION['flash_message'])) {
                                    echo '<div class="alert alert-info">' . htmlspecialchars($_SESSION['flash_message']) . '</div>';
                                    unset($_SESSION['flash_message']);
                                }
                                ?>

                                <form action="index.php?action=login" method="POST" id="loginForm">
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <div class="input-wrapper">
                                            <input type="email" class="form-control-modern"
                                                id="email" name="email" placeholder="Nhập email của bạn" required>
                                            <i class="fas fa-envelope input-icon"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Mật khẩu</label>
                                        <div class="input-wrapper">
                                            <input type="password" class="form-control-modern"
                                                id="password" name="password" placeholder="Nhập mật khẩu" required>
                                            <i class="fas fa-lock input-icon"></i>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-modern">
                                        <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập ngay
                                    </button>
                                </form>
                                
                                <div class="divider">
                                    <span>hoặc</span>
                                </div>
                                
                                <div class="switch-link">
                                    <p>Chưa có tài khoản? <a href="index.php?page=register">Đăng ký miễn phí ngay →</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="public/vendor/jquery/jquery.min.js"></script>
    <script src="public/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="public/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="public/js/sb-admin-2.min.js"></script>
    
    <script>
        // Add subtle animation on input focus
        document.querySelectorAll('.form-control-modern').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>