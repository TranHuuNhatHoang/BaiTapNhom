<?php
class OrderDetail {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Thêm một chi tiết đơn hàng (1 sản phẩm) vào bảng 'order_details'
     */
    public function createDetail($order_id, $product_id, $quantity, $unit_price) {
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, unit_price)
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        // (i = integer, i, i, d = double)
        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $unit_price);
        
        return $stmt->execute();
    }
}
?>