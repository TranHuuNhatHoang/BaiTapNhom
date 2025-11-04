<?php
// 1. Tải file Model User
require_once ROOT_PATH . '/app/models/User.php';

class AuthController {

    /**
     * Action: Hiển thị form đăng ký (khi người dùng truy cập)
     * URL: index.php?controller=auth&action=register
     */
    public function register() {
        // Chỉ cần tải View lên
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/auth/register.php'; // Sẽ tạo file này ở bước 4
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Action: Xử lý dữ liệu từ form (khi người dùng nhấn nút Đăng ký)
     * URL: (Form sẽ POST về) index.php?controller=auth&action=handleRegister
     */
    public function handleRegister() {
        // Chỉ xử lý nếu đây là yêu cầu POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Lấy dữ liệu (từ CSDL của bạn, chúng ta cần full_name)
            $full_name = $_POST['full_name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            // 2. Validate (xác thực) dữ liệu đơn giản
            if (empty($full_name) || empty($email) || empty($password)) {
                die("Lỗi: Vui lòng nhập đầy đủ thông tin.");
            }

            if ($password !== $password_confirm) {
                die("Lỗi: Mật khẩu xác nhận không khớp.");
            }

            // 3. Gọi Model
            global $conn; // Lấy kết nối CSDL ($conn) từ index.php
            $userModel = new User($conn);

            // 4. Kiểm tra email đã tồn tại chưa
            if ($userModel->findUserByEmail($email)) {
                die("Lỗi: Email này đã được đăng ký. Vui lòng sử dụng email khác.");
            }
            
            // 5. Nếu email chưa tồn tại, tạo user mới
            if ($userModel->createUser($full_name, $email, $password)) {
                // Đăng ký thành công, chuyển hướng về trang chủ
                // (Sau này có thể chuyển về trang đăng nhập)
                echo "Đăng ký thành công! Đang chuyển về trang chủ...";
                header("Refresh: 3; URL=" . BASE_URL);
                exit;
            } else {
                die("Đã có lỗi xảy ra trong quá trình đăng ký.");
            }
        }
    }
}
?>