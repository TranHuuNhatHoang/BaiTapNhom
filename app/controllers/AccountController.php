<?php
// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Order.php';

class AccountController {

    // Bắt buộc đăng nhập cho tất cả các action trong controller này
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
    }

    /**
     * Action: Hiển thị trang Thông tin tài khoản
     */
    public function index() {
        global $conn;
        $userModel = new User($conn);
        $user = $userModel->getUserById($_SESSION['user_id']); // Dùng hàm từ GĐ trước

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/account/index.php'; // Sẽ tạo
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Action: Hiển thị Lịch sử đơn hàng
     */
    public function history() {
        global $conn;
        $orderModel = new Order($conn);
        $orders = $orderModel->getOrdersByUserId($_SESSION['user_id']);

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/account/history.php'; // Sẽ tạo
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Action: Xử lý Cập nhật thông tin
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $userModel = new User($conn);
            
            $user_id = $_SESSION['user_id'];
            $full_name = $_POST['full_name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $province = $_POST['province'];
            
            if ($userModel->updateProfile($user_id, $full_name, $phone, $address, $province)) {
                // Cập nhật lại tên trong Session
                $_SESSION['user_full_name'] = $full_name;
                header("Location: " . BASE_URL . "index.php?controller=account&action=index");
                exit;
            } else {
                die("Lỗi cập nhật thông tin.");
            }
        }
    }

     
     // HÀM Hiển thị form Đổi mật khẩu
     
    public function changePassword() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/account/change_password.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    
     // HÀM Xử lý Đổi mật khẩu
    
    
    public function handleChangePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $userModel = new User($conn);
            
            $user_id = $_SESSION['user_id'];
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            // 1. Lấy mật khẩu cũ (đã băm)
            $current_hash = $userModel->getPasswordHashById($user_id);
            
            // 2. So sánh mật khẩu cũ
            if (!password_verify($old_password, $current_hash)) {
                die("Lỗi: Mật khẩu cũ không đúng.");
            }
            
            // 3. So sánh mật khẩu mới
            if ($new_password !== $confirm_password) {
                die("Lỗi: Mật khẩu mới không khớp.");
            }
            
            // 4. Cập nhật mật khẩu mới
            if ($userModel->updatePassword($user_id, $new_password)) {
                // Đổi thành công, đá về trang tài khoản
                echo "Đổi mật khẩu thành công!";
                header("Refresh: 2; URL=" . BASE_URL . "index.php?controller=account&action=index");
                exit;
            } else {
                die("Lỗi khi cập nhật mật khẩu.");
            }
        }
    }
}
?>