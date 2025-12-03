<?php
// Tải functions (để dùng flash message nếu cần)
require_once ROOT_PATH . '/config/functions.php';
// THÊM MODEL LIÊN HỆ
require_once ROOT_PATH . '/app/models/Contact.php';

class PageController {

    /**
     * Action: Hiển thị trang Giới thiệu
     */
    public function about() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/pages/about.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Action: Hiển thị trang Liên hệ
     */
    public function contact() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/pages/contact.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * Action: Xử lý Form Liên hệ (Lưu vào CSDL) - ĐÃ THÊM PHONE
     */
    public function handleContact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $contactModel = new Contact($conn);
            
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']); // <== LẤY PHONE
            $message = trim($_POST['message']);
            
            if (empty($name) || empty($email) || empty($phone) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                set_flash_message("Lỗi: Vui lòng điền đầy đủ và chính xác thông tin (Bao gồm SĐT).", 'error');
            } else {
                // LƯU VÀO CSDL
                if ($contactModel->saveContact($name, $email, $phone, $message)) { // <== TRUYỀN PHONE
                    set_flash_message("Gửi liên hệ thành công! Chúng tôi sẽ phản hồi sớm nhất.", 'success');
                } else {
                    set_flash_message("Lỗi khi lưu liên hệ. Vui lòng thử lại sau.", 'error');
                }
            }
            
            header("Location: " . BASE_URL . "index.php?controller=page&action=contact");
            exit;
        }
    }

    /**
     * Action: Hiển thị trang Chính sách & Điều khoản
     */
    public function terms() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/pages/terms.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
}