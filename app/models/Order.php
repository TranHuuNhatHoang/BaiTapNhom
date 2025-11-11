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
    /**
     * CẬP NHẬT (Người 1 - GĐ18): Thêm coupon_code và discount
     */
    public function createOrder($user_id, $total_amount, $shipping_address, $shipping_phone, $notes, $payment_method, $coupon_code, $discount_applied) {
        
        // Trừ tiền giảm giá khỏi tổng tiền
        $final_total = $total_amount - $discount_applied;
        
        $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, shipping_phone, notes, payment_method, order_status, coupon_code, discount_applied)
                VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        // (i, d, s, s, s, s, s, d)
        $stmt->bind_param("idsssssd", $user_id, $final_total, $shipping_address, $shipping_phone, $notes, $payment_method, $coupon_code, $discount_applied);
        
        if ($stmt->execute()) {
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

    
     // HÀM : Cập nhật trạng thái đơn hàng (cho Admin)
     
    public function updateOrderStatus($order_id, $new_status) {
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $new_status, $order_id);
        return $stmt->execute();
    }
    /**
     * HÀM MỚI (Người 1): Lấy 1 đơn hàng (cho Admin, có JOIN User)
     * (Hàm 'getOrderByIdAndUserId' đã có nhưng nó check user, không dùng cho admin được)
     */
    public function getOrderByIdForAdmin($order_id) {
        $sql = "SELECT o.*, u.full_name, u.email 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.user_id 
                WHERE o.order_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    /**
     * HÀM MỚI : Thống kê cho Dashboard
     */
    public function getOrderStats() {
        // Lấy tổng doanh thu (chỉ tính đơn 'completed')
        $sql_revenue = "SELECT SUM(total_amount) as total_revenue FROM orders WHERE order_status = 'completed'";
        $revenue = $this->conn->query($sql_revenue)->fetch_assoc()['total_revenue'];
        
        // Đếm đơn hàng mới (chưa xử lý 'pending')
        $sql_new_orders = "SELECT COUNT(order_id) as new_orders FROM orders WHERE order_status = 'pending'";
        $new_orders = $this->conn->query($sql_new_orders)->fetch_assoc()['new_orders'];
        
        return [
            'total_revenue' => $revenue ?? 0,
            'new_orders' => $new_orders ?? 0
        ];
    }
/**
     * HÀM MỚI (Người 1): Kiểm tra xem user đã mua SP này chưa
     */
    public function checkUserPurchase($user_id, $product_id) {
        $sql = "SELECT 
                    o.order_id
                FROM 
                    orders o
                JOIN 
                    order_details od ON o.order_id = od.order_id
                WHERE 
                    o.user_id = ? 
                    AND od.product_id = ? 
                    AND o.order_status = 'completed' -- Chỉ cho phép review khi đơn đã hoàn thành
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0; // Trả về true nếu tìm thấy (đã mua)
    }
 public function getRevenueLast7Days() {
        $sql = "SELECT 
                    DATE(created_at) as order_date, 
                    SUM(total_amount) as daily_revenue
                FROM 
                    orders
                WHERE 
                    order_status = 'completed' 
                    AND created_at >= CURDATE() - INTERVAL 7 DAY
                GROUP BY 
                    DATE(created_at)
                ORDER BY 
                    order_date ASC";
        
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

}
?>
