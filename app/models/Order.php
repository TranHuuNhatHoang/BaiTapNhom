<?php
class Order {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Tạo một đơn hàng mới trong bảng 'orders'
     * CẬP NHẬT: Thêm coupon_code và discount, district_id, ward_code (10 tham số)
     * Trả về ID của đơn hàng vừa tạo (order_id)
     */
    public function createOrder($user_id, $total_amount, $shipping_address, $shipping_phone, $shipping_district_id, $shipping_ward_code, $notes, $payment_method, $coupon_code, $discount_applied) {
        
        // Tính tổng tiền cuối cùng
        $final_total = $total_amount - $discount_applied;
        
        // SQL đã được cập nhật để thêm 2 cột mới (discount, code)
        // và 2 cột địa chỉ mới
        $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, shipping_phone, 
                                     shipping_district_id, shipping_ward_code, 
                                     notes, payment_method, order_status, 
                                     coupon_code, discount_applied)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        // bind_param đã được cập nhật: i (user_id), d (final_total), s (addr), s (phone), 
        // i (district_id), s (ward_code), s (notes), s (method), s (coupon_code), d (discount_applied)
        // Tổng cộng: 10 tham số (i d s s i s s s s d)
        
        // Chú ý: $final_total (double) là tham số thứ 2
        // $coupon_code (string) là tham số thứ 9
        // $discount_applied (double) là tham số thứ 10
        
        $stmt->bind_param("idssissssd", 
            $user_id, 
            $final_total, 
            $shipping_address, 
            $shipping_phone, 
            $shipping_district_id, 
            $shipping_ward_code, 
            $notes, 
            $payment_method, 
            $coupon_code, 
            $discount_applied
        );
        
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
        $sql = "SELECT order_id, created_at, total_amount, order_status, shipping_address, 
                        shipping_provider, tracking_code 
                FROM orders 
                WHERE user_id = ?
                ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * CẬP NHẬT: Lấy 1 đơn hàng của User
     * (Có thể tìm bằng order_id HOẶC tracking_code)
     */
    public function getOrderByIdAndUserId($order_id, $user_id, $tracking_code = null) {
        
        // 1. Câu SQL cơ bản
        $sql = "SELECT *, shipping_provider, tracking_code
                FROM orders WHERE user_id = ?";
        
        // 2. Thêm điều kiện (ID hoặc Mã vận đơn)
        $param_type = "is"; 
        $param_value = $order_id; // Mặc định dùng order_id
        
        if ($order_id) {
            $sql .= " AND order_id = ?";
        } else if ($tracking_code) {
            $sql .= " AND tracking_code = ?";
            $param_type = "is"; // Dùng is (int, string)
            $param_value = $tracking_code;
        } else {
            return null; // Không có ID hoặc Code
        }
        
        // 3. Thực thi
        $stmt = $this->conn->prepare($sql);
        // Bind $user_id (int) và $param_value (int hoặc string)
        $stmt->bind_param($param_type, $user_id, $param_value);
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
     * CẬP NHẬT: Lấy 1 đơn hàng (cho Admin)
     * (ĐÃ JOIN VỚI USERS, DISTRICTS, WARDS, VÀ PROVINCES)
     */
    public function getOrderByIdForAdmin($order_id) {
        $sql = "SELECT 
                    o.*, 
                    u.full_name, 
                    u.email,
                    w.ward_name,
                    d.district_name,
                    p.province_name
                FROM 
                    orders o 
                LEFT JOIN 
                    users u ON o.user_id = u.user_id
                -- THÊM 3 JOIN MỚI ĐỂ LẤY TÊN ĐỊA CHỈ --
                LEFT JOIN 
                    wards w ON o.shipping_ward_code = w.ward_code
                LEFT JOIN 
                    districts d ON w.district_id = d.district_id
                LEFT JOIN 
                    provinces p ON d.province_id = p.province_id
                WHERE 
                    o.order_id = ?";
        
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
    
    public function getLatestOrders($limit = 5) {
        $sql = "SELECT o.order_id, o.order_status, o.total_amount, u.full_name
                FROM orders o
                JOIN users u ON o.user_id = u.user_id
                ORDER BY o.created_at DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * HÀM MỚI (Admin): Cập nhật Mã vận đơn
     */
    public function updateTrackingInfo($order_id, $provider, $tracking_code) {
        $sql = "UPDATE orders SET shipping_provider = ?, tracking_code = ? 
                WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $provider, $tracking_code, $order_id);
        return $stmt->execute();
    }
    
    /**
     * HÀM MỚI: Cập nhật trạng thái thanh toán (ZaloPay)
     */
    public function updatePaymentStatus($order_id, $zalopay_trans_id, $status = 'paid') {
        $sql = "UPDATE orders SET 
                    zalopay_trans_id = ?, 
                    order_status = ? 
                WHERE order_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $zalopay_trans_id, $status, $order_id);
        
        return $stmt->execute();
    }
}
?>