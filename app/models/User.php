<?php
class User {
    private $conn; // Biến giữ kết nối CSDL

    // Hàm khởi tạo, nhận kết nối CSDL
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Hàm kiểm tra Email đã tồn tại trong CSDL chưa
     */
    public function findUserByEmail($email) {
        // 1. Dùng prepared statement để chống SQL Injection
        $sql = "SELECT * FROM users WHERE email = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email); // 's' nghĩa là 'string'
        $stmt->execute();
        
        // 2. Lấy kết quả
        $result = $stmt->get_result();
        
        // 3. Trả về user (dưới dạng mảng) nếu tìm thấy, hoặc null nếu không
        return $result->fetch_assoc();
    }

    /**
     * Hàm tạo người dùng mới
     * (Sử dụng các cột CSDL của bạn: full_name, email, password_hash)
     */
    public function createUser($full_name, $email, $password) {
        // 1. Băm mật khẩu (Rất quan trọng)
        // Dùng cột 'password_hash' trong CSDL của bạn
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 2. Chuẩn bị câu SQL (chú ý tên cột của bạn)
        $sql = "INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, 'user')";
        
        $stmt = $this->conn->prepare($sql);
        
        // 3. 'sss' = string, string, string
        $stmt->bind_param("sss", $full_name, $email, $hashed_password);
        
        // 4. Thực thi và trả về true/false
        return $stmt->execute();
    }
     /*Hàm kiểm tra đăng nhập */
     
    public function loginUser($email, $password) {
        // 1. Tìm user bằng email (dùng hàm đã có)
        $user = $this->findUserByEmail($email);

        // 2. Nếu tìm thấy user
        if ($user) {
            // 3. So sánh mật khẩu
            // Dùng password_verify để so sánh mật khẩu người dùng nhập
            // với mật khẩu đã băm (password_hash) trong CSDL
            if (password_verify($password, $user['password_hash'])) {
                // Khớp mật khẩu -> Trả về thông tin user
                return $user;
            }
        }
        // Không tìm thấy user hoặc sai mật khẩu
        return false;
    }

    
        //HÀM MỚI: Lấy thông tin user bằng ID
     
    /**
     * 4. CẬP NHẬT: Lấy thông tin user (Thêm 3 cột địa chỉ mới vào SELECT)
     */
public function getUserById($id) {
        $sql = "SELECT user_id, full_name, email, phone, address, province, role, avatar, 
                       province_id, district_id, ward_code 
                FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
     // HÀM MỚI CỦA BẠN: Cập nhật thông tin (không đổi mật khẩu)
     
    
/**
     * 5. CẬP NHẬT: Lưu thông tin user (Lưu thêm 3 cột ID địa chỉ)
     */
    public function updateProfile($user_id, $full_name, $phone, $address, $province_id, $district_id, $ward_code) {
        // Lưu ý: $address bây giờ chỉ chứa "Số nhà, Tên đường"
        
        $sql = "UPDATE users SET 
                    full_name = ?, 
                    phone = ?, 
                    address = ?, 
                    province_id = ?, 
                    district_id = ?, 
                    ward_code = ?
                WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        // "sssiisi" nghĩa là: string, string, string, int, int, string, int
        $stmt->bind_param("sssiisi", $full_name, $phone, $address, $province_id, $district_id, $ward_code, $user_id);
        
        return $stmt->execute();
    }

    
     // HÀM: Lấy Mật khẩu HASH để so sánh
    public function getPasswordHashById($id) {
        $sql = "SELECT password_hash FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc()['password_hash'];
        }
        return null;
    }

    // HÀM : Cập nhật mật khẩu
    public function updatePassword($id, $new_password) {
        // Băm mật khẩu mới
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET password_hash = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $new_hashed_password, $id);
        
        return $stmt->execute();
    }

    
     // HÀM Lấy TẤT CẢ user (cho Admin)
     // (Không lấy password_hash)
     
    public function getAllUsers() {
        $sql = "SELECT user_id, full_name, email, phone, address, role, created_at FROM users
                ORDER BY created_at DESC";
        
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    
     // HÀM MỚI (Người 3): Cập nhật vai trò (Role)
   
    public function updateUserRole($user_id, $role) {
        // Chỉ cho phép 2 vai trò 'user' hoặc 'admin'
        if ($role !== 'user' && $role !== 'admin') {
            return false;
        }
        $sql = "UPDATE users SET role = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $role, $user_id);
        return $stmt->execute();
    }

     // HÀM Xóa user
    
    /**
     * CẬP NHẬT (Hard Delete): Xóa User và TOÀN BỘ dữ liệu liên quan
     */
    public function deleteUser($user_id) {
        // Bắt đầu giao dịch (Transaction) để đảm bảo an toàn dữ liệu
        $this->conn->begin_transaction();

        try {
            // 1. Xóa Đánh giá (Reviews) của User này
            $sql = "DELETE FROM reviews WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            // 2. Xóa Giỏ hàng (Cart & Cart Items)
            // (Giả sử bảng carts liên kết user_id, cart_items liên kết cart_id)
            // Xóa items trước:
            $this->conn->query("DELETE FROM cart_items WHERE cart_id IN (SELECT cart_id FROM carts WHERE user_id = $user_id)");
            // Xóa cart:
            $this->conn->query("DELETE FROM carts WHERE user_id = $user_id");

            // 3. Xóa Chi tiết Đơn hàng (Order Details) trước
            // (Phải xóa chi tiết của những đơn hàng thuộc về user này)
            $sql_details = "DELETE FROM order_details WHERE order_id IN (SELECT order_id FROM orders WHERE user_id = ?)";
            $stmt = $this->conn->prepare($sql_details);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            // 4. Xóa Đơn hàng (Orders)
            $sql_orders = "DELETE FROM orders WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql_orders);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            // 5. Cuối cùng: Xóa User
            $sql_user = "DELETE FROM users WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql_user);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            // Nếu mọi thứ OK, lưu thay đổi
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // Nếu có lỗi, hoàn tác tất cả
            $this->conn->rollback();
            return false; 
        }
    }
    /**
     * HÀM MỚI : Đếm user mới (ví dụ: đăng ký trong 7 ngày qua)
     */
    public function countNewUsers() {
        $sql = "SELECT COUNT(user_id) as new_users FROM users WHERE created_at >= CURDATE() - INTERVAL 7 DAY";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['new_users'];
    }

    
     // HÀM Tạo token reset
    public function generatePasswordResetToken($email) {
        $token = bin2hex(random_bytes(32)); // Tạo token ngẫu nhiên
        $expires = date('Y-m-d H:i:s', time() + 3600); // Hết hạn sau 1 giờ
        
        $sql = "UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expires, $email);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            return $token; // Trả về token để (giả lập) gửi mail
        }
        return false;
    }

     // HÀM Tìm user bằng token (còn hạn)
    public function findUserByResetToken($token) {
        $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
     // HÀM Cập nhật mật khẩu bằng token
    public function updatePasswordByToken($token, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        // Cập nhật mk VÀ xóa token
        $sql = "UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL 
                WHERE reset_token = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $token);
        return $stmt->execute();
    }
       public function getLatestUsers($limit = 5) {
        $sql = "SELECT user_id, full_name, email, created_at FROM users
                ORDER BY created_at DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * HÀM BỊ THIẾU (Giai đoạn 19 - Người 3): Cập nhật Avatar
     */
public function updateAvatar($user_id, $avatar_filename) {
        $sql = "UPDATE users SET avatar = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $avatar_filename, $user_id);
        return $stmt->execute();
    }
    /**
     * HÀM MỚI (để sửa lỗi): Admin cập nhật thông tin user
     */
    public function adminUpdateUser($user_id, $full_name, $email, $phone, $address, $province, $role) {
        $sql = "UPDATE users SET 
                    full_name = ?, email = ?, phone = ?, address = ?, province = ?, role = ?
                WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        // "ssssssi" = 6 string, 1 integer
        $stmt->bind_param("ssssssi", $full_name, $email, $phone, $address, $province, $role, $user_id);
        
        return $stmt->execute();
    }
}
?>