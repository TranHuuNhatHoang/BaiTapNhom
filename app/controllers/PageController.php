<?php
// Tải functions (để dùng flash message nếu cần)
require_once ROOT_PATH . '/config/functions.php';

class PageController {

    /**
     * Action: Hiển thị trang Giới thiệu
     * URL: index.php?controller=page&action=about
     */
    public function about() {
        // (Sau này bạn có thể lấy nội dung từ CSDL)
        
        // Tải View (Đầy đủ Header, Content, Footer)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/pages/about.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Action: Hiển thị trang Liên hệ
     * URL: index.php?controller=page&action=contact
     */
    public function contact() {
        // Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/pages/contact.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * Action: Xử lý Form Liên hệ
     * URL: (Form POST tới) index.php?controller=page&action=handleContact
     */
    public function handleContact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $message = $_POST['message'];
            
            // (GIẢ LẬP GỬI MAIL)
            // (Trong dự án thật, bạn sẽ dùng PHPMailer ở đây)
            // mail("admin@myweb.com", "Thư liên hệ từ $name", $message);
            
            set_flash_message("Gửi liên hệ thành công! Chúng tôi sẽ phản hồi sớm.", 'success');
            header("Location: " . BASE_URL . "index.php?controller=page&action=contact");
            exit;
        }
    }

    /**
     * Action: Hiển thị trang Chính sách & Điều khoản
     * URL: index.php?controller=page&action=terms
     */
    public function terms() {
        // Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/pages/terms.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
}
?>