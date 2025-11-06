<?php
// app/views/groups.php

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
// Biến $groups và $invitations được truyền từ GroupController
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Nhóm</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <h1>Quản lý Nhóm</h1>
        <nav>
            <a href="index.php?page=dashboard">Quay lại Bảng tin</a>
            <a href="index.php?action=logout">Đăng Xuất</a>
        </nav>
    </header>

    <main class="container">
        
        <?php
        if (isset($_SESSION['flash_message'])) {
            echo '<div class="flash-message">' . $_SESSION['flash_message'] . '</div>';
            unset($_SESSION['flash_message']);
        }
        ?>

        <section class="invitations-container">
            <h2>Lời mời đang chờ</h2>
            <?php if (empty($invitations)): ?>
                <p>Bạn không có lời mời nào đang chờ.</p>
            <?php else: ?>
                <ul class="invitation-list">
                    <?php foreach ($invitations as $invite): ?>
                        <li>
                            <p>
                                <strong><?php echo htmlspecialchars($invite['inviter_name']); ?></strong>
                                đã mời bạn tham gia nhóm
                                <strong><?php echo htmlspecialchars($invite['group_name']); ?></strong>.
                            </p>
                            <div class="invite-actions">
                                <form action="index.php?action=accept_invitation" method="POST" style="display: inline;">
                                    <input type="hidden" name="invitation_id" value="<?php echo $invite['invitation_id']; ?>">
                                    <input type="hidden" name="group_id" value="<?php echo $invite['group_id']; ?>">
                                    <button type="submit" class="btn btn-accept">Chấp Nhận</button>
                                </form>
                                <form action="index.php?action=reject_invitation" method="POST" style="display: inline;">
                                    <input type="hidden" name="invitation_id" value="<?php echo $invite['invitation_id']; ?>">
                                    <button type="submit" class="btn btn-reject">Từ Chối</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <hr>

        <section class="form-container">
            <h2>Tạo nhóm mới</h2>
            <form action="index.php?action=create_group" method="POST">
                <div class="form-group">
                    <label for="group_name">Tên nhóm:</label>
                    <input type="text" id="group_name" name="group_name" required>
                </div>
                <div class="form-group">
                    <label for="group_description">Mô tả nhóm:</label>
                    <textarea id="group_description" name="group_description"></textarea>
                </div>
                <button type="submit" class="btn">Tạo Nhóm</button>
            </form>
        </section>

        <hr>

        <section class="list-container">
            <h2>Các nhóm của bạn</h2>
            <?php if (empty($groups)): ?>
                <p>Bạn chưa tham gia nhóm nào. Hãy tạo một nhóm mới!</p>
            <?php else: ?>
                <ul class="group-list">
                    <?php foreach ($groups as $group): ?>
                        <li>
                            <h3>
                                <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>">
                                    <?php echo htmlspecialchars($group['group_name']); ?>
                                </a>
                            </h3>
                            <p><?php echo htmlspecialchars($group['group_description']); ?></p>
                            <span>Vai trò của bạn: <strong><?php echo htmlspecialchars($group['role']); ?></strong></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>