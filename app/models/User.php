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
}
?>