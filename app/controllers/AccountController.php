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
}
?>