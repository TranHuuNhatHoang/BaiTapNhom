<?php
// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Order.php';     
require_once ROOT_PATH . '/app/models/OrderDetail.php'; 
// (Chúng ta không cần Coupon.php ở đây vì chỉ đọc Session)

class CheckoutController {

    /**
     * Action: Hiển thị trang Checkout
     * CẬP NHẬT: Thêm logic đọc Coupon từ Session
     */
    public function index() {
        global $conn;
        
        // --- Code bảo vệ (đã có) ---
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
        // --- Hết code bảo vệ ---

        // Lấy thông tin User (đã có)
        $userModel = new User($conn);
        $user = $userModel->getUserById($_SESSION['user_id']);
        
        // Lấy Tóm tắt Giỏ hàng (đã có)
        $productModel = new Product($conn);
        $cart_items = [];
        $total_price = 0; // Tổng tiền HÀNG (chưa giảm)
        
        foreach ($cart as $product_id => $quantity) {
            $product = $productModel->getProductById($product_id);
            if ($product) {
                $product['quantity_in_cart'] = $quantity;
                $cart_items[] = $product;
                $total_price += $product['price'] * $quantity;
            }
        }
        
        // --- CẬP NHẬT (Người 1 - GĐ18): Lấy thông tin coupon từ Session ---
        $coupon_code = $_SESSION['cart_coupon']['code'] ?? null;
        $discount_amount = $_SESSION['cart_coupon']['discount'] ?? 0;
        
        // Đảm bảo không giảm giá nhiều hơn tổng tiền
        if ($discount_amount > $total_price) {
            $discount_amount = $total_price;
        }
        
        $final_price = $total_price - $discount_amount; // Tổng tiền cuối cùng
        // --- KẾT THÚC CẬP NHẬT ---
        
        // Tải View (truyền tất cả các biến cho view)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/checkout/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * Action: Xử lý Đặt hàng
     * CẬP NHẬT: Thêm logic lưu Coupon vào CSDL
     */
    public function placeOrder() {
        global $conn;

        // --- Code bảo vệ (đã có) ---
        if (!isset($_SESSION['user_id'])) { /* ... */ }
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart) || $_SERVER['REQUEST_METHOD'] !== 'POST') { /* ... */ }
        
        // 1. Lấy thông tin từ form (đã có)
        $user_id = $_SESSION['user_id'];
        $shipping_address = $_POST['address'];
        $shipping_phone = $_POST['phone'];
        $notes = $_POST['notes'];
        $payment_method = $_POST['payment_method'];
        
        // 2. Tính toán lại Tổng tiền (đã có)
        $productModel = new Product($conn);
        $total_price = 0; // Tổng tiền HÀNG (chưa giảm)
        $products_in_cart = [];
        
        foreach ($cart as $product_id => $quantity) {
            $product = $productModel->getProductById($product_id);
            if ($product) {
                if ($product['quantity'] < $quantity) {
                    die("Lỗi: Sản phẩm '" . htmlspecialchars($product['product_name']) . "' không đủ tồn kho.");
                }
                $total_price += $product['price'] * $quantity;
                $products_in_cart[] = [
                    'id' => $product_id,
                    'name' => $product['product_name'],
                    'quantity' => $quantity,
                    'unit_price' => $product['price']
                ];
            }
        }

        // --- CẬP NHẬT (Người 1 - GĐ18): Lấy thông tin coupon từ Session ---
        $coupon_code = $_SESSION['cart_coupon']['code'] ?? null;
        $discount_amount = $_SESSION['cart_coupon']['discount'] ?? 0;
        if ($discount_amount > $total_price) {
            $discount_amount = $total_price;
        }
        // --- KẾT THÚC CẬP NHẬT ---

        // BẮT ĐẦU TRANSACTION
        $conn->begin_transaction();
        try {
            // 3. Tạo Đơn hàng (Bảng 'orders')
            $orderModel = new Order($conn);
            
            // --- CẬP NHẬT (Người 1 - GĐ18): Truyền 8 tham số (thêm coupon) ---
            $order_id = $orderModel->createOrder(
                $user_id, 
                $total_price, // Gửi tổng tiền GỐC
                $shipping_address, 
                $shipping_phone, 
                $notes, 
                $payment_method,
                $coupon_code,       // Tham số thứ 7
                $discount_amount    // Tham số thứ 8
            );
            
            if (!$order_id) throw new Exception("Không thể tạo đơn hàng.");

            // 4. Tạo Chi tiết Đơn hàng VÀ Trừ tồn kho (code cũ, đã đúng)
            $orderDetailModel = new OrderDetail($conn);
            foreach ($products_in_cart as $item) {
                $orderDetailModel->createDetail($order_id, $item['id'], $item['quantity'], $item['unit_price']);
                $rows_affected = $productModel->decrementStock($item['id'], $item['quantity']);
                if ($rows_affected <= 0) {
                    throw new Exception("Sản phẩm '" . htmlspecialchars($item['name']) . "' đã hết hàng.");
                }
            }
            
            // 5. Nếu mọi thứ OK -> Commit
            $conn->commit();
            
            // 6. Xóa giỏ hàng
            unset($_SESSION['cart']);
            unset($_SESSION['cart_coupon']); // <-- THÊM MỚI: Xóa cả coupon
            
            // 7. Chuyển đến trang Cảm ơn
            header("Location: " . BASE_URL . "index.php?controller=checkout&action=success&order_id=" . $order_id);
            exit;

        } catch (Exception $e) {
            // 8. Nếu có lỗi -> Rollback
            $conn->rollback();
            die("Đặt hàng thất bại: " . $e->getMessage());
        }
    }
    
    /**
     * Hiển thị trang Cảm ơn (đã có)
     */
    public function success() {
        $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/checkout/success.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }   

}
?>