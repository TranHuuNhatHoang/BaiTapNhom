<?php
class Coupon {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * HÀM MỚI (Người 3): Lấy coupon bằng code
     */
    public function findCouponByCode($code) {
        $sql = "SELECT * FROM coupons 
                WHERE coupon_code = ? 
                AND (expires_at IS NULL OR expires_at > NOW())
                AND (usage_count < max_usage)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // (Sau này bạn sẽ cần hàm 'incrementUsageCount($code)'
    // khi Người 1 làm Checkout)
}
?>