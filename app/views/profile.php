<?php
// Tệp: app/views/profile.php (Bản HOÀN THIỆN với SB Admin 2)
// Trang này ứng với ?page=edit_profile

// 1. Gọi Header
require 'app/views/layout/header.php'; 

// Biến $user đã được UserController (hàm showEditProfile) tải
$user_avatar = $_SESSION['user_avatar'] ?? $user['avatar_url'] ?? 'public/img/undraw_profile.svg';
?>

<h1 class="h3 mb-4 text-gray-800">Chỉnh sửa Hồ sơ</h1>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-success shadow-sm mb-4">
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
                
                <form id="avatarForm" action="index.php?action=upload_avatar" method="POST" enctype="multipart/form-data" class="d-none">
                    <input type="file" id="avatarInput" name="avatar" accept="image/*">
                </form>

                <button type="button" class="btn btn-secondary btn-sm" 
                        onclick="document.getElementById('avatarInput').click();">
                    <i class="fas fa-upload fa-sm"></i> Thay đổi ảnh
                </button>
                <p class="small mt-2">Chọn ảnh để tự động tải lên.</p>

            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin của bạn</h6>
            </div>
            <div class="card-body">
                
                <form action="index.php?action=update_profile" method="POST">
                    
                    <h6 class="text-secondary">Thông tin cơ bản</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Tên người dùng:</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email:</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-secondary">Thông tin hồ sơ (Dùng để ghép nhóm)</h6>

                    <div class="form-group">
                        <label for="major">Ngành học:</label>
                        <input type="text" class="form-control" id="major" name="profile_major" 
                               value="<?php echo htmlspecialchars($user['profile_major'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="skills">Các kỹ năng (vd: Lập trình, Thuyết trình...):</label>
                        <textarea class="form-control" id="skills" name="profile_skills" rows="3"><?php echo htmlspecialchars($user['profile_skills'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="interests">Sở thích:</label>
                        <textarea class="form-control" id="interests" name="profile_interests" rows="2"><?php echo htmlspecialchars($user['profile_interests'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="strengths">Điểm mạnh:</label>
                        <textarea class="form-control" id="strengths" name="profile_strengths" rows="2"><?php echo htmlspecialchars($user['profile_strengths'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="weaknesses">Điểm yếu:</label>
                        <textarea class="form-control" id="weaknesses" name="profile_weaknesses" rows="2"><?php echo htmlspecialchars($user['profile_weaknesses'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="role">Vai trò mong muốn trong nhóm (vd: Leader, Coder, Designer...):</label>
                        <input type="text" class="form-control" id="role" name="profile_role_preference" 
                               value="<?php echo htmlspecialchars($user['profile_role_preference'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary btn-icon-split mt-3">
                        <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                        <span class="text">Lưu thay đổi</span>
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatarInput');
    if(avatarInput) {
        avatarInput.addEventListener('change', () => {
            if (avatarInput.files && avatarInput.files[0]) {
                // Tự động submit form #avatarForm khi có file được chọn
                document.getElementById('avatarForm').submit();
            }
        });
    }
});
</script>

<?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>