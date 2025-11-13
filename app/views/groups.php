<?php
// Tệp: app/views/groups.php (Bản HOÀN THIỆN với SB Admin 2)

// 1. Gọi Header (tự động thêm menu, sidebar, CSS)
require 'app/views/layout/header.php'; 

// Các biến $groups và $invitations đã được GroupController tải
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Quản lý Nhóm</h1>
</div>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-success shadow-sm mb-4">
        <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>


<div class="row">

    <div class="col-lg-6">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-fw fa-envelope"></i> Lời mời đang chờ</h6>
            </div>
            <div class="card-body">
                <?php if (empty($invitations)): ?>
                    <p class="text-muted">Bạn không có lời mời nào đang chờ.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($invitations as $invite): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <strong><?php echo htmlspecialchars($invite['inviter_name']); ?></strong>
                                    mời bạn vào nhóm
                                    <strong><?php echo htmlspecialchars($invite['group_name']); ?></strong>.
                                </div>
                                <div class="mt-2 mt-md-0">
                                    <form action="index.php?action=accept_invitation" method="POST" class="d-inline">
                                        <input type="hidden" name="invitation_id" value="<?php echo $invite['invitation_id']; ?>">
                                        <input type="hidden" name="group_id" value="<?php echo $invite['group_id']; ?>">
                                        <button type="submit" class="btn btn-success btn-sm py-1 px-2">
                                            <i class="fas fa-check"></i> Chấp nhận
                                        </button>
                                    </form>
                                    <form action="index.php?action=reject_invitation" method="POST" class="d-inline">
                                        <input type="hidden" name="invitation_id" value="<?php echo $invite['invitation_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm py-1 px-2">
                                            <i class="fas fa-times"></i> Từ chối
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-fw fa-plus-circle"></i> Tạo nhóm mới</h6>
            </div>
            <div class="card-body">
                <form action="index.php?action=create_group" method="POST">
                    <div class="form-group">
                        <label for="group_name">Tên nhóm:</label>
                        <input type="text" class="form-control" id="group_name" name="group_name" required>
                    </div>
                    <div class="form-group">
                        <label for="group_description">Mô tả nhóm:</label>
                        <textarea class="form-control" id="group_description" name="group_description" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-icon-split">
                        <span class="icon text-white-50"><i class="fas fa-rocket"></i></span>
                        <span class="text">Tạo Nhóm</span>
                    </button>
                </form>
            </div>
        </div>

    </div> <div class="col-lg-6">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-fw fa-layer-group"></i> Các nhóm của bạn</h6>
            </div>
            <div class="card-body">
                <?php if (empty($groups)): ?>
                    <p class="text-muted">Bạn chưa tham gia nhóm nào. Hãy tạo một nhóm mới!</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($groups as $group): ?>
                            <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>" class="list-group-item list-group-item-action flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 text-primary"><?php echo htmlspecialchars($group['group_name']); ?></h5>
                                    <small>Vai trò: <?php echo htmlspecialchars($group['role']); ?></small>
                                </div>
                                <p class="mb-1 text-gray-700"><?php echo htmlspecialchars($group['group_description']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div> </div> <?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>