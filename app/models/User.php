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
     
    public function getUserById($id) {
        $sql = "SELECT user_id, full_name, email, phone, address, province 
                FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    
     // HÀM MỚI CỦA BẠN: Cập nhật thông tin (không đổi mật khẩu)
     
    
    public function updateProfile($user_id, $full_name, $phone, $address, $province) {
        $sql = "UPDATE users SET full_name = ?, phone = ?, address = ?, province = ?
                WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $full_name, $phone, $address, $province, $user_id);
        
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
    public function deleteUser($user_id) {
        //  Tác vụ này sẽ xóa user vĩnh viễn,nếu user còn có đơn hàng thì chỉ nên vô hiệu hóa
        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
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
}
?>