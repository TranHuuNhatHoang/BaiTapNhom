<?php
// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Product.php';

class CheckoutController {

    
     // Action: Hiển thị trang Checkout
     // URL: index.php?controller=checkout&action=index
     
    public function index() {
        global $conn;
        // 1. Bắt buộc ĐĂNG NHẬP
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
    /**
     * (Action 'placeOrder' (Xử lý đặt hàng) sẽ được làm ở tính năng tiếp theo)
     */
}
?>