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
    // Action: Hiển thị form Đăng nhập 
    public function login() {
        // Tải view form đăng nhập
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/auth/login.php'; // Sẽ tạo ở bước 4
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    // Action: Xử lý thông tin đăng nhập (từ form)
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            global $conn;
            $userModel = new User($conn);

            // Gọi hàm loginUser từ Model
            $user = $userModel->loginUser($email, $password);

            if ($user) {
                // Đăng nhập THÀNH CÔNG
                // Lưu thông tin user vào SESSION
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];

                // Chuyển hướng về trang chủ
                header("Location: " . BASE_URL);
                exit;
            } else {
                // Đăng nhập THẤT BẠI
                die("Email hoặc mật khẩu không đúng.");
            }
        }
    }
    //Action: Đăng xuất
    public function logout() {
        session_unset(); // Xóa tất cả biến session
        session_destroy(); // Hủy session
        
        // Chuyển hướng về trang chủ
        header("Location: " . BASE_URL);
        exit;
    }

     // HÀM Hiển thị form Quên mật khẩu
     
    public function forgotPassword() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/auth/forgot_password.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
     // HÀM Xử lý Gửi link Reset
    public function handleForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            global $conn;
            $userModel = new User($conn);
            $token = $userModel->generatePasswordResetToken($email);
            
            if ($token) {
                // GIẢ LẬP GỬI EMAIL
                // Trong dự án thật, bạn sẽ dùng thư viện PHPMailer
                $reset_link = BASE_URL . "index.php?controller=auth&action=resetPassword&token=" . $token;
                
                echo "GỬI MAIL (Giả lập): Một link reset đã được gửi đến $email. <br>";
                echo "Vui lòng nhấn vào link này để reset: <a href='$reset_link'>$reset_link</a>";
            } else {
                echo "Không tìm thấy email hoặc có lỗi. Vui lòng thử lại.";
            }
        }
    }
    
    
     // HÀM Hiển thị form Reset Mật khẩu
     
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        global $conn;
        $userModel = new User($conn);
        $user = $userModel->findUserByResetToken($token);
        
        if (!$user) {
            die("Token không hợp lệ hoặc đã hết hạn.");
        }
        
        // Truyền $token cho view
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/auth/reset_password.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
     // HÀM xử lý Đặt lại Mật khẩu
     
    public function handleResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            if ($new_password !== $confirm_password) {
                die("Mật khẩu không khớp.");
            } 
            global $conn;
            $userModel = new User($conn);
            if ($userModel->updatePasswordByToken($token, $new_password)) {
                echo "Cập nhật mật khẩu thành công!";
                header("Refresh: 2; URL=" . BASE_URL . "index.php?controller=auth&action=login");
                exit;
            } else {
                die("Lỗi: Token không hợp lệ hoặc đã hết hạn.");
            }
        }
    }
}
?>