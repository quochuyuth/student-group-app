<?php
// Tệp: app/views/view_profile.php (Bản HOÀN THIỆN với SB Admin 2)
// Trang này ứng với ?page=profile

// 1. Gọi Header
require 'app/views/layout/header.php'; 

// Biến $user_profile đã được UserController (hàm viewProfile) tải
// $user_profile là mảng chứa thông tin của người DÙNG ĐANG ĐƯỢC XEM
$user_avatar = $user_profile['avatar_url'] ?? 'public/img/undraw_profile.svg';

// Kiểm tra xem người đang xem có phải là CHÍNH MÌNH không
$is_viewing_self = ($user_profile['user_id'] == $_SESSION['user_id']);
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        Hồ sơ của: <?php echo htmlspecialchars($user_profile['username']); ?>
    </h1>
    <?php if ($is_viewing_self): ?>
        <a href="index.php?page=edit_profile" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-pen fa-sm text-white-50"></i> Chỉnh sửa hồ sơ này
        </a>
    <?php endif; ?>
</div>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-info shadow-sm mb-4">
        <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ảnh đại diện</h6>
            </div>
            <div class="card-body text-center">
                
                <img class="img-profile rounded-circle mb-3" 
                     src="<?php echo htmlspecialchars($user_avatar); ?>" 
                     alt="Avatar" 
                     style="max-width: 150px; height: 150px; object-fit: cover;">
                
                <h4 class="h5 text-gray-800"><?php echo htmlspecialchars($user_profile['username']); ?></h4>
                <p class="small text-muted"><?php echo htmlspecialchars($user_profile['email']); ?></p>

            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin hồ sơ</h6>
            </div>
            <div class="card-body">
                
                <?php
                function render_profile_field($label, $value) {
                    $display_value = (!empty($value)) ? nl2br(htmlspecialchars($value)) : '<em class="text-muted small">Chưa cập nhật</em>';
                    echo '
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold text-dark">' . $label . ':</label>
                        <div class="col-sm-9">
                            <div class="form-control-plaintext" style="padding-top: 0; padding-bottom: 0;">' . $display_value . '</div>
                        </div>
                    </div>
                    <hr class="my-2">';
                }
                ?>

                <?php render_profile_field('Ngành học', $user_profile['profile_major']); ?>
                <?php render_profile_field('Kỹ năng', $user_profile['profile_skills']); ?>
                <?php render_profile_field('Sở thích', $user_profile['profile_interests']); ?>
                <?php render_profile_field('Điểm mạnh', $user_profile['profile_strengths']); ?>
                <?php render_profile_field('Điểm yếu', $user_profile['profile_weaknesses']); ?>
                <?php render_profile_field('Vai trò mong muốn', $user_profile['profile_role_preference']); ?>

                <a href="javascript:history.back()" class="btn btn-secondary btn-icon-split mt-3">
                    <span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
                    <span class="text">Quay lại</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>