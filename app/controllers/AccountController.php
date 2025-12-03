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
    public function index() {
        global $conn;
        $userModel = new User($conn);
        $user = $userModel->getUserById($_SESSION['user_id']); 

        // --- LOGIC ĐỊA CHỈ ---
        $addressModel = new Address($conn);
        $provinces = $addressModel->getProvinces();
        $districts = [];
        if (!empty($user['province_id'])) {
            $districts = $addressModel->getDistrictsByProvince($user['province_id']);
        }
        $wards = [];
        if (!empty($user['district_id'])) {
            $wards = $addressModel->getWardsByDistrict($user['district_id']);
        }
        // --- KẾT THÚC LOGIC ĐỊA CHỈ ---

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
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $userModel = new User($conn);
            
            $user_id = $_SESSION['user_id'];
            $full_name = $_POST['full_name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            
            $province_id = !empty($_POST['province_id']) ? (int)$_POST['province_id'] : null;
            $district_id = !empty($_POST['district_id']) ? (int)$_POST['district_id'] : null;
            $ward_code   = !empty($_POST['ward_code']) ? $_POST['ward_code'] : null;
            
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
            
            if (!password_verify($old_password, $current_hash)) {
                set_flash_message("Lỗi: Mật khẩu cũ không đúng.", 'error');
                header("Location: " . BASE_URL . "index.php?controller=account&action=changePassword");
                exit;
            }
            
            if ($new_password !== $confirm_password) {
                set_flash_message("Lỗi: Mật khẩu mới không khớp.", 'error');
                header("Location: " . BASE_URL . "index.php?controller=account&action=changePassword");
                exit;
            }
            
            if ($userModel->updatePassword($user_id, $new_password)) {
                set_flash_message("Đổi mật khẩu thành công!", 'success');
                header("Location: " . BASE_URL . "index.php?controller=account&action=index");
                exit;
            } else {
                set_flash_message("Lỗi khi cập nhật mật khẩu.", 'error');
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
            set_flash_message("Không tìm thấy đơn hàng hoặc bạn không có quyền xem.", 'error');
            header("Location: " . BASE_URL . "index.php?controller=account&action=history");
            exit;
        }
        
        $order_details = $orderModel->getOrderDetailsByOrderId($order_id);
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/account/order_detail.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * HÀM HELPER: Hàm Hỗ trợ Xử lý Upload File
     * ĐÃ SỬA: Kiểm tra quyền ghi và tạo thư mục.
     */
    private function handleUpload($file_input_name, $upload_dir) {
        // Kiểm tra lỗi upload PHP
        if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
            // Lỗi 4: Không có file nào được tải lên (user chưa chọn file)
            if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_NO_FILE) {
                 return ['status' => 'error', 'message' => 'Vui lòng chọn file ảnh để tải lên.'];
            }
            return ['status' => 'error', 'message' => 'Lỗi upload PHP không xác định. Mã lỗi: ' . ($_FILES[$file_input_name]['error'] ?? 'Không có file')];
        }
        
        $file_tmp_path = $_FILES[$file_input_name]['tmp_name'];
        $file_name = basename($_FILES[$file_input_name]['name']);
        
        // Chỉ cho phép định dạng ảnh
        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES[$file_input_name]['type'];

        if (!in_array($file_type, $allowed_mime_types)) {
            return ['status' => 'error', 'message' => 'Upload ảnh thất bại. Chỉ chấp nhận file JPG, PNG, GIF, WEBP.']; 
        }
        
        // ----------------------------------------------------
        // LOGIC SỬA LỖI: KIỂM TRA QUYỀN GHI VÀ TẠO THƯ MỤC
        // ----------------------------------------------------
        $absolute_upload_dir = ROOT_PATH . $upload_dir;
        
        // 1. Kiểm tra và tạo thư mục nếu chưa tồn tại
        if (!is_dir($absolute_upload_dir)) {
            // mode 0777 cho phép quyền tối đa, cần thiết trên localhost/môi trường dev
            if (!mkdir($absolute_upload_dir, 0777, true)) {
                 return ['status' => 'error', 'message' => 'Không thể tạo thư mục upload. Vui lòng kiểm tra quyền.'];
            }
        }

        // 2. Kiểm tra quyền ghi (thường lỗi ở đây trên hosting)
        if (!is_writable($absolute_upload_dir)) {
            return ['status' => 'error', 'message' => 'Lỗi quyền truy cập: Thư mục upload không thể ghi (chmod 777).'];
        }
        // ----------------------------------------------------

        // Tạo tên file duy nhất (để tránh ghi đè)
        $new_file_name = uniqid() . '-' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file_name);
        
        // Đường dẫn đích
        $dest_path = $absolute_upload_dir . $new_file_name;

        // Di chuyển file vào thư mục đích
        if (move_uploaded_file($file_tmp_path, $dest_path)) {
            return ['status' => 'success', 'filename' => $new_file_name]; // Trả về TÊN FILE MỚI
        }
        
        return ['status' => 'error', 'message' => 'Lỗi di chuyển file. Vui lòng kiểm tra dung lượng file hoặc quyền truy cập.'];
    }
    
    /**
     * HÀM Xử lý Upload Avatar
     */
    public function updateAvatar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $userModel = new User($conn);
            $user_id = $_SESSION['user_id'];
            
            $user = $userModel->getUserById($user_id);
            $current_avatar = $user['avatar'];
            
            $upload_dir = '/public/uploads/avatars/';
            $upload_result = $this->handleUpload('avatar_file', $upload_dir);
            
            if ($upload_result['status'] === 'success') {
                $new_avatar_name = $upload_result['filename'];
                
                if ($userModel->updateAvatar($user_id, $new_avatar_name)) {
                    
                    $_SESSION['user_avatar'] = $new_avatar_name;
                
                    // Xóa avatar cũ (chỉ xóa nếu không phải 'default.png' và không phải file vừa upload)
                    if (!empty($current_avatar) && $current_avatar !== 'default.png' && $current_avatar !== $new_avatar_name) {
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
                // Hiển thị thông báo lỗi chi tiết từ handleUpload
                set_flash_message($upload_result['message'], 'error');
            }
            
            header("Location: " . BASE_URL . "index.php?controller=account&action=index");
            exit;
        }
    }
    
    /**
     * HÀM Xử lý Xóa Avatar (đặt về default)
     */
    public function deleteAvatar() {
        global $conn;
        $userModel = new User($conn);
        $user_id = $_SESSION['user_id'];
        $upload_dir = '/public/uploads/avatars/';
        $default_avatar = 'default.png';
        
        $user = $userModel->getUserById($user_id);
        $current_avatar = $user['avatar'];

        if ($userModel->updateAvatar($user_id, $default_avatar)) {
            
            $_SESSION['user_avatar'] = $default_avatar;
            
            // Xóa file avatar cũ (chỉ xóa nếu không phải là default)
            if (!empty($current_avatar) && $current_avatar !== $default_avatar) {
                $old_avatar_path = ROOT_PATH . $upload_dir . $current_avatar;
                if (file_exists($old_avatar_path)) {
                    unlink($old_avatar_path);
                }
            }
            
            set_flash_message("Đã xóa và đặt lại ảnh đại diện mặc định.", 'success');
        } else {
            set_flash_message("Lỗi khi xóa ảnh đại diện.", 'error');
        }
        
        header("Location: " . BASE_URL . "index.php?controller=account&action=index");
        exit;
    }
}