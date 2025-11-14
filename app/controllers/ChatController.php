<?php
// app/controllers/ChatController.php (ĐÃ SỬA LỖI)

require_once 'app/models/Chat.php';

class ChatController {
    private $db;
    private $chatModel;

    public function __construct($db) {
        $this->db = $db;
        $this->chatModel = new Chat($this->db);
    }

    public function sendMessage() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
        }
        $group_id = (int)$_POST['group_id'];
        $sender_user_id = $_SESSION['user_id'];
        $message_content = trim(strip_tags($_POST['message_content']));
        
        // *** (SỬA LỖI) ***
        // Đổi redirect_url từ 'group_details' sang 'group_chat'
        $redirect_url = "Location: index.php?page=group_chat&id=" . $group_id . "#chat-box";
        
        if (!empty($message_content)) {
            $this->chatModel->sendMessage($group_id, $sender_user_id, $message_content);
        }
        header($redirect_url); exit;
    }

    public function sendFile() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?page=login'); exit;
        }

        $group_id = (int)$_POST['group_id'];
        $user_id = $_SESSION['user_id'];
        
        // *** (SỬA LỖI) ***
        // Đổi redirect_url từ 'group_details' sang 'group_chat'
        $redirect_url = "Location: index.php?page=group_chat&id=" . $group_id . "#chat-box";

        if (!isset($_FILES['group_file']) || $_FILES['group_file']['error'] != UPLOAD_ERR_OK) {
            $_SESSION['flash_message'] = "Lỗi: Không có file nào được chọn hoặc file bị lỗi.";
            header($redirect_url); exit;
        }
        
        $file = $_FILES['group_file'];
        $upload_dir = 'public/uploads/';
        $original_name = basename($file['name']);
        $file_type = $file['type'];
        $file_size = $file['size'];
        
        // (Giữ nguyên) Đảm bảo có username trong session khi đặt tên file
        $safe_name = time() . "_" . ($_SESSION['username'] ?? 'user') . "_" . $original_name;
        $target_path = $upload_dir . $safe_name;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            
            // (Giữ nguyên) Truyền $file_size vào Model
            if ($this->chatModel->saveFileMessage($group_id, $user_id, $original_name, $target_path, $file_size, $file_type)) {
                // Thành công
            } else {
                $_SESSION['flash_message'] = "Lỗi: Lưu file vào CSDL thất bại.";
                unlink($target_path);
            }
        } else {
            $_SESSION['flash_message'] = "Lỗi: Không thể di chuyển file đã upload.";
        }
        
        header($redirect_url); exit;
    }
}
?>