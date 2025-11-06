<?php
// test_db.php
// File này chỉ dùng để kiểm tra, bạn nên XÓA SAU KHI TEST XONG.

echo "<h2>Kiểm tra kết nối CSDL...</h2>";

try {
    // 1. Gọi file cấu hình database
    // Nó sẽ thử tạo biến $db
    require_once 'config/database.php';

    // 2. Nếu dòng require ở trên chạy xong mà không "die"
    // có nghĩa là kết nối đã thành công.
    
    echo "<p style='color: green; font-weight: bold;'>
        Kết nối thành công đến database '" . DB_NAME . "'!
    </p>";

    // 3. Chạy thử một truy vấn đơn giản
    $stmt = $db->query("SELECT VERSION()");
    $version = $stmt->fetchColumn();
    echo "<p>Phiên bản MySQL Server: " . $version . "</p>";

} catch (PDOException $e) {
    // Lỗi này được bắt nếu file config/database.php bị "die"
    echo "<p style='color: red; font-weight: bold;'>
        LỖI KẾT NỐI: " . $e->getMessage() . "
    </p>";
} catch (Exception $e) {
    // Bắt các lỗi khác (ví dụ: không tìm thấy file)
    echo "<p style='color: red; font-weight: bold;'>
        LỖI KHÁC: " . $e->getMessage() . "
    </p>";
}
?>