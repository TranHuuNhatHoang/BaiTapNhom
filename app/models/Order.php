<?php
class Order {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }
 public function createOrder($user_id, $total_amount, $shipping_address, $shipping_phone, $notes, $payment_method) {
        $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, shipping_phone, notes, payment_method, order_status)
                VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        // (i = integer, d = double, s = string)
        $stmt->bind_param("idsssss", $user_id, $total_amount, $shipping_address, $shipping_phone, $notes, $payment_method);
        
        if ($stmt->execute()) {
            // Lấy ID của đơn hàng vừa chèn
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }
    /**
     * HÀM MỚI CỦA BẠN: Lấy tất cả đơn hàng (cho Admin)
     * (JOIN với 'users' để lấy tên người đặt)
     */
    public function getAllOrders() {
        $sql = "SELECT o.*, u.full_name, u.email
                FROM orders o
                JOIN users u ON o.user_id = u.user_id
                ORDER BY o.created_at DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>