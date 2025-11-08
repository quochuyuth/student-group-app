<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Nhóm</title>
    <link rel="stylesheet" href="public/css/groups.css">
</head>
<body>
    <div class="background"></div>

    <header class="main-header">
        <div class="header-left">
            <h1>Quản lý <span>Nhóm</span></h1>
        </div>
        <nav class="header-nav">
            <a href="index.php?page=dashboard">Trang Chủ</a>
            <a href="index.php?page=profile">Hồ sơ</a>
            <a href="index.php?action=logout" class="logout">Đăng Xuất</a>
        </nav>
    </header>

    <main class="container">

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="flash-message"><?= $_SESSION['flash_message']; ?></div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <section class="card invitations-container">
            <h2>📩 Lời mời đang chờ</h2>
            <?php if (empty($invitations)): ?>
                <p class="empty">Bạn không có lời mời nào đang chờ.</p>
            <?php else: ?>
                <ul class="invitation-list">
                    <?php foreach ($invitations as $invite): ?>
                        <li class="invitation-item">
                            <p><strong><?= htmlspecialchars($invite['inviter_name']); ?></strong> mời bạn vào nhóm <strong><?= htmlspecialchars($invite['group_name']); ?></strong>.</p>
                            <div class="invite-actions">
                                <form action="index.php?action=accept_invitation" method="POST">
                                    <input type="hidden" name="invitation_id" value="<?= $invite['invitation_id']; ?>">
                                    <input type="hidden" name="group_id" value="<?= $invite['group_id']; ?>">
                                    <button class="btn accept">✅ Chấp Nhận</button>
                                </form>
                                <form action="index.php?action=reject_invitation" method="POST">
                                    <input type="hidden" name="invitation_id" value="<?= $invite['invitation_id']; ?>">
                                    <button class="btn reject">❌ Từ Chối</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section class="card form-container">
            <h2>🆕 Tạo nhóm mới</h2>
            <form action="index.php?action=create_group" method="POST">
                <div class="form-group">
                    <label for="group_name">Tên nhóm:</label>
                    <input type="text" id="group_name" name="group_name" required>
                </div>
                <div class="form-group">
                    <label for="group_description">Mô tả nhóm:</label>
                    <textarea id="group_description" name="group_description"></textarea>
                </div>
                <button type="submit" class="btn create">🚀 Tạo Nhóm</button>
            </form>
        </section>

        <section class="card list-container">
            <h2>👥 Các nhóm của bạn</h2>
            <?php if (empty($groups)): ?>
                <p class="empty">Bạn chưa tham gia nhóm nào. Hãy tạo một nhóm mới!</p>
            <?php else: ?>
                <ul class="group-list">
                    <?php foreach ($groups as $group): ?>
                        <li class="group-card">
                            <h3><a href="index.php?page=group_details&id=<?= $group['group_id']; ?>"><?= htmlspecialchars($group['group_name']); ?></a></h3>
                            <p><?= htmlspecialchars($group['group_description']); ?></p>
                            <span class="role">Vai trò: <strong><?= htmlspecialchars($group['role']); ?></strong></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

    </main>
</body>
</html>
