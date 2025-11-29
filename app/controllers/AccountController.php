<?php
// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Order.php';
// Tải file functions (để dùng set_flash_message)
require_once ROOT_PATH . '/config/functions.php';
require_once ROOT_PATH . '/app/models/Address.php';
class AccountController {

    // Bắt buộc đăng nhập cho tất cả các action trong controller này
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            // Đặt thông báo lỗi TRƯỚC KHI chuyển hướng
            set_flash_message("Bạn phải đăng nhập để xem trang này.", 'error');
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
    }

    /**
     * Action: Hiển thị trang Thông tin tài khoản
     */
    /**
     * CẬP NHẬT: Hiển thị trang Tài khoản (Có chuẩn bị dữ liệu Dropdown)
     */
    public function index() {
        global $conn;
        $userModel = new User($conn);
        $user = $userModel->getUserById($_SESSION['user_id']); 

        // --- LOGIC ĐỊA CHỈ MỚI ---
        $addressModel = new Address($conn);
        
        // 1. Luôn lấy danh sách Tỉnh/Thành để hiển thị dropdown đầu tiên
        $provinces = $addressModel->getProvinces();
        
        // 2. Nếu user đã có Tỉnh (trong CSDL), lấy danh sách Quận của Tỉnh đó
        $districts = [];
        if (!empty($user['province_id'])) {
            $districts = $addressModel->getDistrictsByProvince($user['province_id']);
        }
        
        // 3. Nếu user đã có Quận (trong CSDL), lấy danh sách Phường của Quận đó
        $wards = [];
        if (!empty($user['district_id'])) {
            $wards = $addressModel->getWardsByDistrict($user['district_id']);
        }
        // --- KẾT THÚC LOGIC MỚI ---

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/account/index.php';
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
        require_once ROOT_PATH . '/app/views/account/history.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Action: Xử lý Cập nhật thông tin
     */
    /**
     * CẬP NHẬT: Xử lý Cập nhật thông tin (Lưu cả ID địa chỉ)
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $userModel = new User($conn);
            
            $user_id = $_SESSION['user_id'];
            $full_name = $_POST['full_name'];
            $phone = $_POST['phone'];
            $address = $_POST['address']; // Đây là Số nhà/Tên đường
            
            // Lấy 3 ID Địa chỉ từ Form (nếu không chọn thì là null)
            $province_id = !empty($_POST['province_id']) ? (int)$_POST['province_id'] : null;
            $district_id = !empty($_POST['district_id']) ? (int)$_POST['district_id'] : null;
            $ward_code   = !empty($_POST['ward_code']) ? $_POST['ward_code'] : null;
            
            // Gọi hàm updateProfile (phiên bản mới trong Model)
            if ($userModel->updateProfile($user_id, $full_name, $phone, $address, $province_id, $district_id, $ward_code)) {
                $_SESSION['user_full_name'] = $full_name;
                set_flash_message("Cập nhật thông tin thành công!", 'success');
                header("Location: " . BASE_URL . "index.php?controller=account&action=index");
                exit;
            } else {
                set_flash_message("Lỗi cập nhật thông tin.", 'error');
                header("Location: " . BASE_URL . "index.php?controller=account&action=index");
                exit;
            }
        }
    }

    
    /**
     * HÀM Hiển thị form Đổi mật khẩu
     */
    public function changePassword() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/account/change_password.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    
    /**
     * HÀM Xử lý Đổi mật khẩu
     */
    public function handleChangePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $userModel = new User($conn);
            
            $user_id = $_SESSION['user_id'];
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            $current_hash = $userModel->getPasswordHashById($user_id);
            
            // 2. So sánh mật khẩu cũ
            if (!password_verify($old_password, $current_hash)) {
                // die("Lỗi: Mật khẩu cũ không đúng."); // CŨ
                set_flash_message("Lỗi: Mật khẩu cũ không đúng.", 'error'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=account&action=changePassword");
                exit;
            }
            
            // 3. So sánh mật khẩu mới
            if ($new_password !== $confirm_password) {
                // die("Lỗi: Mật khẩu mới không khớp."); // CŨ
                set_flash_message("Lỗi: Mật khẩu mới không khớp.", 'error'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=account&action=changePassword");
                exit;
            }
            
            // 4. Cập nhật mật khẩu mới
            if ($userModel->updatePassword($user_id, $new_password)) {
                // echo "Đổi mật khẩu thành công!"; // CŨ
                set_flash_message("Đổi mật khẩu thành công!", 'success'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=account&action=index");
                exit;
            } else {
                // die("Lỗi khi cập nhật mật khẩu."); // CŨ
                set_flash_message("Lỗi khi cập nhật mật khẩu.", 'error'); // MỚI
                header("Location: " . BASE_URL . "index.php?controller=account&action=changePassword");
                exit;
            }
        }
    }
    
    /**
     * HÀM MỚI: Hiển thị Chi tiết 1 Đơn hàng
     */
    public function orderDetail() {
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $user_id = $_SESSION['user_id'];
        
        global $conn;
        $orderModel = new Order($conn);
        
        $order = $orderModel->getOrderByIdAndUserId($order_id, $user_id);
        
        if (!$order) {
            // die("Không tìm thấy đơn hàng..."); // CŨ
            set_flash_message("Không tìm thấy đơn hàng hoặc bạn không có quyền xem.", 'error'); // MỚI
            header("Location: " . BASE_URL . "index.php?controller=account&action=history");
            exit;
        }
        
        // 2. Lấy chi tiết các sản phẩm trong đơn
        $order_details = $orderModel->getOrderDetailsByOrderId($order_id);
        
        // 3. Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/account/order_detail.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * HÀM HELPER: Hàm Hỗ trợ Xử lý Upload File
     * (Giữ nguyên)
     */
    private function handleUpload($file_input_name, $upload_dir) {
        // Kiểm tra xem có file được tải lên không và không có lỗi
        if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
            
            $file_tmp_path = $_FILES[$file_input_name]['tmp_name'];
            $file_name = basename($_FILES[$file_input_name]['name']);
            
            // Chỉ cho phép định dạng ảnh
            $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            // Dùng $_FILES['...']['type'] thay vì mime_content_type()
            $file_type = $_FILES[$file_input_name]['type'];

            if (!in_array($file_type, $allowed_mime_types)) {
                return null; // Không phải file ảnh
            }
            
            // Tạo tên file duy nhất (để tránh ghi đè)
            $new_file_name = uniqid() . '-' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file_name);
            
            // Đường dẫn đích
            $dest_path = ROOT_PATH . $upload_dir . $new_file_name;

            // Di chuyển file vào thư mục đích
            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                return $new_file_name; // Trả về TÊN FILE MỚI
            }
        }
        return null; // Thất bại
    }
    
    /**
     * HÀM Xử lý Upload Avatar
     * ĐÃ SỬA LỖI: Cập nhật $_SESSION['user_avatar'] đúng cách
     */
    public function updateAvatar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $userModel = new User($conn);
            $user_id = $_SESSION['user_id'];
            
            // Lấy avatar cũ (để xóa)
            $user = $userModel->getUserById($user_id);
            $current_avatar = $user['avatar'];
            
            // 1. Xử lý upload ảnh
            $upload_dir = '/public/uploads/avatars/';
            $new_avatar_name = $this->handleUpload('avatar_file', $upload_dir);
            
            if ($new_avatar_name) {
                // 2. Cập nhật CSDL
                if ($userModel->updateAvatar($user_id, $new_avatar_name)) {
                    
                    // 3. Cập nhật Session
                    // CHỈ GÁN VÀO SESSION KHI CẬP NHẬT CSDL THÀNH CÔNG
                    $_SESSION['user_avatar'] = $new_avatar_name;
                
                    // 4. Xóa avatar cũ (nếu có)
                    // Kiểm tra cả $current_avatar có tồn tại trong CSDL VÀ file có tồn tại
                    if (!empty($current_avatar) && $current_avatar !== $new_avatar_name) {
                        $old_avatar_path = ROOT_PATH . $upload_dir . $current_avatar;
                        if (file_exists($old_avatar_path)) {
                            unlink($old_avatar_path);
                        }
                    }
                    
                    set_flash_message("Cập nhật ảnh đại diện thành công!", 'success');
                } else {
                    // Lỗi DB: Xóa file vừa upload để tránh rác
                    $uploaded_file_path = ROOT_PATH . $upload_dir . $new_avatar_name;
                    if (file_exists($uploaded_file_path)) {
                        unlink($uploaded_file_path);
                    }
                    set_flash_message("Lỗi CSDL khi cập nhật ảnh đại diện.", 'error');
                }
            } else {
                // Lỗi này xảy ra do hàm handleUpload trả về null (lỗi file/format)
                set_flash_message("Upload ảnh thất bại. Chỉ chấp nhận file JPG, PNG, GIF.", 'error');
            }
            
            header("Location: " . BASE_URL . "index.php?controller=account&action=index");
            exit;
        }
    }
}
?>