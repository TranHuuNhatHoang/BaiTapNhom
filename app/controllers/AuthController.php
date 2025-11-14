<?php
// 1. Tải file Model User
require_once ROOT_PATH . '/app/models/User.php';
// 2. Tải file functions (để dùng set_flash_message)
require_once ROOT_PATH . '/config/functions.php';

class AuthController {

    /**
     * Action: Hiển thị form đăng ký
     */
    public function register() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/auth/register.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Action: Xử lý dữ liệu từ form đăng ký
     */
    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $full_name = $_POST['full_name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            // 2. Validate (xác thực)
            if (empty($full_name) || empty($email) || empty($password)) {
                // die("Lỗi: Vui lòng nhập đầy đủ thông tin."); // CŨ
                set_flash_message("Lỗi: Vui lòng nhập đầy đủ thông tin.", 'error'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=auth&action=register");
                exit;
            }

            if ($password !== $password_confirm) {
                // die("Lỗi: Mật khẩu xác nhận không khớp."); // CŨ
                set_flash_message("Lỗi: Mật khẩu xác nhận không khớp.", 'error'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=auth&action=register");
                exit;
            }

            // 3. Gọi Model
            global $conn;
            $userModel = new User($conn);

            // 4. Kiểm tra email đã tồn tại chưa
            if ($userModel->findUserByEmail($email)) {
                // die("Lỗi: Email này đã được đăng ký..."); // CŨ
                set_flash_message("Lỗi: Email này đã được đăng ký. Vui lòng sử dụng email khác.", 'error'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=auth&action=register");
                exit;
            }
            
            // 5. Tạo user mới
            if ($userModel->createUser($full_name, $email, $password)) {
                // echo "Đăng ký thành công!..."; // CŨ
                set_flash_message("Đăng ký thành công! Vui lòng đăng nhập.", 'success'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
                exit;
            } else {
                // die("Đã có lỗi xảy ra..."); // CŨ
                set_flash_message("Đã có lỗi xảy ra trong quá trình đăng ký.", 'error'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=auth&action=register");
                exit;
            }
        }
    }
    
    /**
     * Action: Hiển thị form Đăng nhập 
     */
    public function login() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/auth/login.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * Action: Xử lý thông tin đăng nhập
     */
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            global $conn;
            $userModel = new User($conn);
            $user = $userModel->loginUser($email, $password);

            if ($user) {
                // Đăng nhập THÀNH CÔNG
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                // (Sau này Người 3 - GĐ 19 sẽ thêm $_SESSION['user_avatar'] ở đây)

                set_flash_message("Đăng nhập thành công! Chào mừng " . htmlspecialchars($user['full_name']), 'success'); // MỚI
                header("Location: " . BASE_URL);
                exit;
            } else {
                // Đăng nhập THẤT BẠI
                // die("Email hoặc mật khẩu không đúng."); // CŨ
                set_flash_message("Email hoặc mật khẩu không đúng.", 'error'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
                exit;
            }
        }
    }
    
    /**
     * Action: Đăng xuất
     */
    public function logout() {
        session_unset(); // Xóa tất cả biến session
        session_destroy(); // Hủy session
        
        session_start(); // Bắt đầu lại session để lưu flash message
        set_flash_message("Bạn đã đăng xuất.", 'info'); // MỚI
        
        header("Location: " . BASE_URL);
        exit;
    }

    /**
     * HÀM Hiển thị form Quên mật khẩu
     */
    public function forgotPassword() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/auth/forgot_password.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * HÀM Xử lý Gửi link Reset
     */
    /**
     * HÀM Xử lý Gửi link Reset
     * (ĐÃ SỬA LỖI: Thêm $reset_link vào Flash Message)
     */
    public function handleForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            global $conn;
            $userModel = new User($conn);
            $token = $userModel->generatePasswordResetToken($email);
            
            if ($token) {
                // 1. Tạo lại đường link đầy đủ
                $reset_link = BASE_URL . "index.php?controller=auth&action=resetPassword&token=" . $token;
                
                // 2. Tạo tin nhắn (bao gồm cả link HTML)
                // (Vì hàm display_flash_message của chúng ta in HTML, 
                // thẻ <br> và <a> sẽ hoạt động)
                $message = "Yêu cầu thành công. (Đây là Giả lập Gửi Mail): <br>" .
                           "Vui lòng nhấn vào link sau để reset: <br>" .
                           "<a href='$reset_link' style='font-weight: bold;'>Nhấn vào đây để đặt lại mật khẩu</a>";

                // 3. Đặt tin nhắn flash
                set_flash_message($message, 'success'); // Dùng 'success' cho nổi bật
                
            } else {
                // Tin nhắn lỗi (không đổi)
                set_flash_message("Không tìm thấy email hoặc có lỗi. Vui lòng thử lại.", 'error');
            }
            
            // Chuyển hướng về trang quên mật khẩu (để hiển thị tin nhắn)
            header("Location: " . BASE_URL . "index.php?controller=auth&action=forgotPassword");
            exit;
        }
    }
    
    
    /**
     * HÀM Hiển thị form Reset Mật khẩu
     */
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        global $conn;
        $userModel = new User($conn);
        $user = $userModel->findUserByResetToken($token);
        
        if (!$user) {
            // die("Token không hợp lệ hoặc đã hết hạn."); // CŨ
            set_flash_message("Token không hợp lệ hoặc đã hết hạn.", 'error'); // MỚI
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
        
        // Truyền $token cho view
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/auth/reset_password.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * HÀM xử lý Đặt lại Mật khẩu
     */
    public function handleResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password !== $confirm_password) {
                // die("Mật khẩu không khớp."); // CŨ
                set_flash_message("Mật khẩu không khớp.", 'error'); // MỚI
                // Chuyển hướng lại trang reset (vẫn kèm token)
                header("Location: " . BASE_URL . "index.php?controller=auth&action=resetPassword&token=" . urlencode($token));
                exit;
            } 
            
            global $conn;
            $userModel = new User($conn);
            
            if ($userModel->updatePasswordByToken($token, $new_password)) {
                // echo "Cập nhật mật khẩu thành công!"; // CŨ
                set_flash_message("Cập nhật mật khẩu thành công! Vui lòng đăng nhập.", 'success'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
                exit;
            } else {
                // die("Lỗi: Token không hợp lệ hoặc đã hết hạn."); // CŨ
                set_flash_message("Lỗi: Token không hợp lệ hoặc đã hết hạn.", 'error'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
                exit;
            }
        }
    }
}
?>