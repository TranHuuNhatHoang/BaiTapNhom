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
    /**
     * HÀM MỚI CỦA BẠN: Lấy đơn hàng của MỘT người dùng
     */
    public function getOrdersByUserId($user_id) {
        $sql = "SELECT * FROM orders 
                WHERE user_id = ?
                ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * HÀM MỚI: Lấy MỘT đơn hàng (để kiểm tra)
     */
    public function getOrderByIdAndUserId($order_id, $user_id) {
        $sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * HÀM MỚI: Lấy CHI TIẾT của một đơn hàng
     * (JOIN 3 bảng: order_details, products, brands)
     */
    public function getOrderDetailsByOrderId($order_id) {
        $sql = "SELECT 
                    od.quantity, 
                    od.unit_price, 
                    p.product_name, 
                    p.main_image,
                    b.brand_name
                FROM 
                    order_details od
                JOIN 
                    products p ON od.product_id = p.product_id
                LEFT JOIN
                    brands b ON p.brand_id = b.brand_id
                WHERE 
                    od.order_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
