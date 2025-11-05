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
     
    public function placeOrder() {
        global $conn;

        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
        
        // 1. Lấy thông tin từ form
        $user_id = $_SESSION['user_id'];
        $shipping_address = $_POST['address'];
        $shipping_phone = $_POST['phone'];
        $notes = $_POST['notes'];
        $payment_method = $_POST['payment_method'];
        
        // 2. Tính toán lại Tổng tiền (Không tin giá từ form)
        $productModel = new Product($conn);
        $total_price = 0;
        $products_in_cart = []; // Lưu lại sản phẩm để chèn vào order_details
        
        foreach ($cart as $product_id => $quantity) {
            $product = $productModel->getProductById($product_id); 
            if ($product) {
                $total_price += $product['price'] * $quantity;
                $products_in_cart[] = [
                    'id' => $product_id,
                    'quantity' => $quantity,
                    'unit_price' => $product['price']
                ];
            }
        }

        
        $conn->begin_transaction();

        try {
            // 3. Tạo Đơn hàng (Bảng 'orders')
            $orderModel = new Order($conn);
            $order_id = $orderModel->createOrder($user_id, $total_price, $shipping_address, $shipping_phone, $notes, $payment_method);
            
            if (!$order_id) throw new Exception("Không thể tạo đơn hàng.");

            // 4. Tạo Chi tiết Đơn hàng (Bảng 'order_details')
            $orderDetailModel = new OrderDetail($conn);
            foreach ($products_in_cart as $item) {
                $orderDetailModel->createDetail($order_id, $item['id'], $item['quantity'], $item['unit_price']);
            }
            
            $conn->commit();
            
            // 6. Xóa giỏ hàng
            unset($_SESSION['cart']);
            
            // 7. Chuyển đến trang Cảm ơn
            header("Location: " . BASE_URL . "index.php?controller=checkout&action=success&order_id=" . $order_id);
            exit;

        } catch (Exception $e) {
            // 8. Nếu có lỗi -> Rollback (Hủy bỏ)
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