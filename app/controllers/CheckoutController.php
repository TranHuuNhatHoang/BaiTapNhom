<?php
// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Order.php';     
require_once ROOT_PATH . '/app/models/OrderDetail.php'; 


class CheckoutController {

    
     // Action: Hiển thị trang Checkout
     
    public function index() {
        global $conn;
        if (!isset($_SESSION['user_id'])) {
            // Nếu chưa đăng nhập, bắt họ quay về trang login
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
        // 2. Bắt buộc GIỎ HÀNG KHÔNG RỖNG
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            // Nếu giỏ hàng rỗng, đá về trang giỏ hàng
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
        // Lấy thông tin User (để điền sẵn vào form)
        $userModel = new User($conn);
        $user = $userModel->getUserById($_SESSION['user_id']);
        
        // Lấy Tóm tắt Giỏ hàng (để hiển thị)
        $productModel = new Product($conn);
        $cart_items = [];
        $total_price = 0;
        
        foreach ($cart as $product_id => $quantity) {
            $product = $productModel->getProductById($product_id);
            if ($product) {
                $product['quantity_in_cart'] = $quantity;
                $cart_items[] = $product;
                $total_price += $product['price'] * $quantity;
            }
        }
        // Tải View (truyền $user, $cart_items, $total_price)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/checkout/index.php'; // Sẽ tạo ở bước 4
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
     
     // Xử lý Đặt hàng (khi form POST tới)
     
    /**
     * CẬP NHẬT (Người 3): Xử lý Đặt hàng (Thêm logic Trừ Tồn Kho)
     */
    public function placeOrder() {
        global $conn;

        // --- Bảo vệ (Giống hàm index) ---
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
        
        // 1. Lấy thông tin từ form (code cũ)
        $user_id = $_SESSION['user_id'];
        $shipping_address = $_POST['address'];
        $shipping_phone = $_POST['phone'];
        $notes = $_POST['notes'];
        $payment_method = $_POST['payment_method'];
        
        // 2. Tính toán lại Tổng tiền (code cũ)
        $productModel = new Product($conn); // <-- Cần Model này
        $total_price = 0;
        $products_in_cart = [];
        
        foreach ($cart as $product_id => $quantity) {
            $product = $productModel->getProductById($product_id);
            if ($product) {
                // KIỂM TRA TỒN KHO NGAY TỪ ĐẦU
                if ($product['quantity'] < $quantity) {
                    die("Lỗi: Sản phẩm '" . htmlspecialchars($product['product_name']) . "' chỉ còn " . $product['quantity'] . " cái. Vui lòng quay lại giỏ hàng.");
                }
                
                $total_price += $product['price'] * $quantity;
                $products_in_cart[] = [
                    'id' => $product_id,
                    'name' => $product['product_name'], // Thêm tên để báo lỗi
                    'quantity' => $quantity,
                    'unit_price' => $product['price']
                ];
            }
        }

        // BẮT ĐẦU TRANSACTION
        $conn->begin_transaction();

        try {
            // 3. Tạo Đơn hàng (Bảng 'orders')
            $orderModel = new Order($conn);
            $order_id = $orderModel->createOrder($user_id, $total_price, $shipping_address, $shipping_phone, $notes, $payment_method);
            
            if (!$order_id) throw new Exception("Không thể tạo đơn hàng.");

            // 4. Tạo Chi tiết Đơn hàng (Bảng 'order_details')
            $orderDetailModel = new OrderDetail($conn);
            
            // --- CẬP NHẬT Ở ĐÂY (Người 3) ---
            foreach ($products_in_cart as $item) {
                
                // 4a. Thêm chi tiết đơn hàng (code cũ)
                $orderDetailModel->createDetail($order_id, $item['id'], $item['quantity'], $item['unit_price']);
                
                // 4b. TRỪ TỒN KHO (code mới)
                $rows_affected = $productModel->decrementStock($item['id'], $item['quantity']);
                
                // 4c. Kiểm tra
                if ($rows_affected <= 0) {
                    // Nếu không trừ được (hết hàng), hủy toàn bộ đơn
                    throw new Exception("Sản phẩm '" . htmlspecialchars($item['name']) . "' đã hết hàng trong quá trình bạn đặt.");
                }
            }
            // --- KẾT THÚC CẬP NHẬT ---
            
            // 5. Nếu mọi thứ OK -> Commit
            $conn->commit();
            
            // 6. Xóa giỏ hàng
            unset($_SESSION['cart']);
            
            // 7. Chuyển đến trang Cảm ơn
            header("Location: " . BASE_URL . "index.php?controller=checkout&action=success&order_id=" . $order_id);
            exit;

        } catch (Exception $e) {
            // 8. Nếu có lỗi (kể cả lỗi hết hàng) -> Rollback
            $conn->rollback();
            die("Đặt hàng thất bại: " . $e->getMessage());
        }
    }
    
     //  Hiển thị trang Cảm ơn
     
    public function success() {
        $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/checkout/success.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }  

}
?>