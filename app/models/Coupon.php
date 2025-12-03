<?php

class Coupon {
    private $conn;
    private $table_name = "coupons";
    private $usage_table = "coupon_usage"; // Giả định bảng này đã được tạo

    public function __construct($db) {
        $this->conn = $db;
    }

    // ============================================================
    // LOGIC CHO ADMIN
    // ============================================================
    
    /**
     * Tạo một Coupon mới
     */
    public function createCoupon($code, $type, $value, $expires, $max_usage, $is_public) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (coupon_code, discount_type, discount_value, expires_at, max_usage, is_public) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);

        // Bind 6 tham số: s s d s i i
        $stmt->bind_param("ssdsii", $code, $type, $value, $expires, $max_usage, $is_public);

        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            // Xử lý lỗi trùng mã (Unique Key coupon_code)
            return false;
        }
    }

    /**
     * Lấy tất cả Coupons (cho Admin)
     */
    public function getAllCoupons() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY expires_at DESC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Xóa một Coupon
     */
    public function deleteCoupon($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE coupon_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // ============================================================
    // LOGIC CHO NGƯỜI DÙNG (VALIDATION)
    // ============================================================

    /**
     * Lấy Coupon bằng Mã code
     */
    public function getCouponByCode($code) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE coupon_code = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Kiểm tra Coupon có hợp lệ không
     * Trả về TRUE nếu hợp lệ, FALSE hoặc chuỗi thông báo lỗi nếu không
     */
    public function isCouponValid($coupon, $user_id) {
        if (!$coupon) {
            return "Mã giảm giá không tồn tại.";
        }
        
        if (strtotime($coupon['expires_at']) < time()) {
            return "Mã giảm giá đã hết hạn sử dụng.";
        }
        
        // Kiểm tra số lượng sử dụng tối đa
        if ($coupon['usage_count'] >= $coupon['max_usage']) {
            return "Mã giảm giá đã hết lượt sử dụng.";
        }
        
        // KIỂM TRA QUAN TRỌNG: User đã sử dụng mã này chưa? (1 lần/tài khoản)
        $query = "SELECT COUNT(*) FROM " . $this->usage_table . " WHERE user_id = ? AND coupon_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $coupon['coupon_id']);
        $stmt->execute();
        
        if ($stmt->get_result()->fetch_row()[0] > 0) {
            return "Mỗi tài khoản chỉ được áp dụng mã này 1 lần duy nhất.";
        }
        
        return true; // Hợp lệ
    }

    /**
     * Ghi nhận việc sử dụng mã vào bảng coupon_usage và tăng usage_count
     */
    public function recordUsage($coupon_id, $user_id, $order_id) {
        try {
            $this->conn->begin_transaction();

            // 1. Ghi nhận vào bảng coupon_usage
            $query_usage = "INSERT INTO " . $this->usage_table . " (coupon_id, user_id, order_id) VALUES (?, ?, ?)";
            $stmt_usage = $this->conn->prepare($query_usage);
            $stmt_usage->bind_param("iii", $coupon_id, $user_id, $order_id);
            $stmt_usage->execute();

            // 2. Tăng usage_count trong bảng coupons
            $query_update = "UPDATE " . $this->table_name . " SET usage_count = usage_count + 1 WHERE coupon_id = ?";
            $stmt_update = $this->conn->prepare($query_update);
            $stmt_update->bind_param("i", $coupon_id);
            $stmt_update->execute();

            $this->conn->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->conn->rollback();
            // Nếu có lỗi do Unique Key (user_id, coupon_id) trùng lặp, rollback
            return false;
        }
    }
    
    /**
     * Hàm tính toán số tiền giảm
     */
    public function calculateDiscount($coupon, $total_price) {
        if ($coupon['discount_type'] == 'fixed') {
            return min($coupon['discount_value'], $total_price); 
        } elseif ($coupon['discount_type'] == 'percent') {
            $discount = $total_price * ($coupon['discount_value'] / 100);
            return $discount;
        }
        return 0;
    }
    
    /**
     * Hàm lấy mã giảm giá công khai (cho trang chủ)
     * Đây là hàm gây ra lỗi, đã sửa lại để kiểm tra cú pháp
     */
    public function getPublicCoupons() {
        $current_time = date('Y-m-d H:i:s');
        $query = "SELECT coupon_code, discount_type, discount_value, expires_at 
                  FROM " . $this->table_name . " 
                  WHERE is_public = 1 -- ĐÂY LÀ CỘT GÂY LỖI
                  AND expires_at > ? 
                  AND usage_count < max_usage
                  ORDER BY expires_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $current_time);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}