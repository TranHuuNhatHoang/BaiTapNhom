<?php
class Order {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Tạo một đơn hàng mới trong bảng 'orders'
     * Trả về ID của đơn hàng vừa tạo (order_id)
     */
    public function createOrder($user_id, $total_amount, $shipping_address, $shipping_phone, $notes, $payment_method) {
        $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, shipping_phone, notes, payment_method, order_status)
                VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        // (i = integer, d = double, s = string)
        $stmt->bind_param("idssss", $user_id, $total_amount, $shipping_address, $shipping_phone, $notes, $payment_method);
        
        if ($stmt->execute()) {
            // Lấy ID của đơn hàng vừa chèn
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }
}
?>