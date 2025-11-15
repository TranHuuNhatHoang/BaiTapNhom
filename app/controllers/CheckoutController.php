<?php
// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Order.php';     
require_once ROOT_PATH . '/app/models/OrderDetail.php'; 
// Tải file functions (để dùng set_flash_message)
require_once ROOT_PATH . '/config/functions.php';

class CheckoutController {

    /**
     * Action: Hiển thị trang Checkout
     * CẬP NHẬT: Thêm logic Flash Message
     */
    public function index() {
        global $conn;
        
        // --- Code bảo vệ (Cập nhật với Flash Message) ---
        if (!isset($_SESSION['user_id'])) {
            set_flash_message("Bạn phải đăng nhập để thanh toán.", 'error'); // MỚI
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            set_flash_message("Giỏ hàng của bạn rỗng. Vui lòng thêm sản phẩm.", 'info'); // MỚI
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
        
        // --- Lấy thông tin coupon (đã có) ---
        $coupon_code = $_SESSION['cart_coupon']['code'] ?? null;
        $discount_amount = $_SESSION['cart_coupon']['discount'] ?? 0;
        if ($discount_amount > $total_price) {
            $discount_amount = $total_price;
        }
        $final_price = $total_price - $discount_amount;
        
        // Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/checkout/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * Action: Xử lý Đặt hàng
     * CẬP NHẬT: Thêm logic Flash Message
     */
    /**
     * Action: Xử lý Đặt hàng
     * CẬP NHẬT (BƯỚC 7): Lấy Mã Tỉnh/Quận/Phường từ $_POST
     */
    public function placeOrder() {
        global $conn;

        // --- Code bảo vệ (đã có) ---
        if (!isset($_SESSION['user_id'])) {
            set_flash_message("Bạn phải đăng nhập để thanh toán.", 'error'); 
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            set_flash_message("Giỏ hàng của bạn rỗng. Vui lòng thêm sản phẩm.", 'info'); 
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
        
        // 1. Lấy thông tin từ form (CẬP NHẬT)
        $user_id = $_SESSION['user_id'];
        $shipping_address = $_POST['address']; // (Đây là Số nhà/Tên đường)
        $shipping_phone = $_POST['phone'];
        $notes = $_POST['notes'];
        $payment_method = $_POST['payment_method'];
        
        // --- THÊM MỚI (BƯỚC 7): Lấy Mã Tỉnh/Quận/Phường ---
        $shipping_district_id = (int)$_POST['district_id'];
        $shipping_ward_code = $_POST['ward_code'];
        // --- KẾT THÚC THÊM MỚI ---

        // 2. Tính toán lại Tổng tiền (đã có)
        $productModel = new Product($conn);
        $total_price = 0; // Tổng tiền HÀNG (chưa giảm)
        $products_in_cart = [];
        
        foreach ($cart as $product_id => $quantity) {
            $product = $productModel->getProductById($product_id);
            if ($product) {
                if ($product['quantity'] < $quantity) {
                    set_flash_message("Lỗi: Sản phẩm '" . htmlspecialchars($product['product_name']) . "' chỉ còn " . $product['quantity'] . " cái.", 'error');
                    header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
                    exit;
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

        // --- Lấy thông tin coupon (đã có) ---
        $coupon_code = $_SESSION['cart_coupon']['code'] ?? null;
        $discount_amount = $_SESSION['cart_coupon']['discount'] ?? 0;
        if ($discount_amount > $total_price) {
            $discount_amount = $total_price;
        }

        // BẮT ĐẦU TRANSACTION
        $conn->begin_transaction();
        try {
            // 3. Tạo Đơn hàng (Bảng 'orders')
            $orderModel = new Order($conn);
            
            // --- CẬP NHẬT (BƯỚC 7): Truyền 10 tham số (thêm 2 mã địa chỉ) ---
            $order_id = $orderModel->createOrder(
                $user_id, 
                $total_price, // Tổng tiền GỐC
                $shipping_address, 
                $shipping_phone, 
                $shipping_district_id, // THÊM MỚI
                $shipping_ward_code,   // THÊM MỚI
                $notes, 
                $payment_method,
                $coupon_code,
                $discount_amount
            );
            
            if (!$order_id) throw new Exception("Không thể tạo đơn hàng.");

            // 4. Tạo Chi tiết Đơn hàng VÀ Trừ tồn kho (code cũ, đã đúng)
            $orderDetailModel = new OrderDetail($conn);
            foreach ($products_in_cart as $item) {
                $orderDetailModel->createDetail($order_id, $item['id'], $item['quantity'], $item['unit_price']);
                $rows_affected = $productModel->decrementStock($item['id'], $item['quantity']);
                if ($rows_affected <= 0) {
                    throw new Exception("Sản phẩm '" . htmlspecialchars($item['name']) . "' đã hết hàng trong quá trình bạn đặt.");
                }
            }
            
            // 5. Nếu mọi thứ OK -> Commit
            $conn->commit();
            
            // 6. Xóa giỏ hàng VÀ XÓA COUPON
            unset($_SESSION['cart']);
            unset($_SESSION['cart_coupon']);
            
            // 7. Chuyển đến trang Cảm ơn
            set_flash_message("Đặt hàng thành công! Cảm ơn bạn đã mua hàng.", 'success');
            header("Location: " . BASE_URL . "index.php?controller=checkout&action=success&order_id=" . $order_id);
            exit;

        } catch (Exception $e) {
            // 8. Nếu có lỗi -> Rollback
            $conn->rollback();
            set_flash_message("Đặt hàng thất bại: " . $e->getMessage(), 'error');
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
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