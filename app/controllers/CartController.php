<?php
// Tải Product model để lấy thông tin chi tiết sản phẩm
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Coupon.php';
// Tải file functions (để dùng set_flash_message)
require_once ROOT_PATH . '/config/functions.php';

class CartController {

    /**
     * Action: Hiển thị trang giỏ hàng chi tiết
     * (Hàm này không thay đổi)
     */
    public function index() {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $cart_items = []; 
        $total_price = 0; 

        if (!empty($cart)) {
            global $conn;
            
            if (!class_exists('Product')) {
                 require_once ROOT_PATH . '/app/models/Product.php';
            }
            $productModel = new Product($conn);
            
            foreach ($cart as $product_id => $quantity) {
                $product = $productModel->getProductById($product_id);
                if ($product) {
                    $product['quantity_in_cart'] = $quantity;
                    $cart_items[] = $product;
                    $total_price += $product['price'] * $quantity;
                }
            }
        }
        
        // --- PHẦN LOGIC COUPON (Không thay đổi) ---
        $coupon_code = $_SESSION['cart_coupon']['code'] ?? null;
        $discount_amount = $_SESSION['cart_coupon']['discount'] ?? 0;
        if ($discount_amount > $total_price) {
            $discount_amount = $total_price;
        }
        $final_price = $total_price - $discount_amount;

        // Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/cart/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Action: Thêm vào giỏ hàng (AJAX - Người 2)
     * (Hàm này trả về JSON, KHÔNG dùng Flash Message)
     */
    public function add() {
        $product_id = $_POST['product_id'] ?? $_GET['product_id'] ?? 0;
        $quantity = (int)($_POST['quantity'] ?? $_GET['quantity'] ?? 1);
        
        if ($quantity <= 0) $quantity = 1;
        if ($product_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ.']);
            exit;
        }

        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        $cart_count = array_sum($_SESSION['cart']);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Đã thêm vào giỏ hàng!',
            'cart_count' => $cart_count
        ]);
        exit;
    }

    /**
     * Action: Cập nhật số lượng sản phẩm
     * CẬP NHẬT (Người 3): Thêm Flash Message
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'];
            $quantity = (int)$_POST['quantity'];

            if (isset($_SESSION['cart'][$product_id])) {
                if ($quantity > 0) {
                    $_SESSION['cart'][$product_id] = $quantity;
                    set_flash_message("Cập nhật số lượng thành công.", 'success'); // MỚI
                } else {
                    unset($_SESSION['cart'][$product_id]);
                    set_flash_message("Đã xóa sản phẩm khỏi giỏ hàng.", 'info'); // MỚI
                }
            }
        }
        
        header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
        exit;
    }

    /**
     * Action: Xóa sản phẩm khỏi giỏ hàng
     * CẬP NHẬT (Người 3): Thêm Flash Message
     */
    public function remove() {
        $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            set_flash_message("Đã xóa sản phẩm khỏi giỏ hàng.", 'info'); // MỚI
        }
        
        header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
        exit;
    }

    /**
     * Action: Áp dụng Mã giảm giá
     * CẬP NHẬT (Người 3): Thêm Flash Message
     */
    public function applyCoupon() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['coupon_code']);
            $total_price = (float)$_POST['total_price']; 
            
            global $conn;
            $couponModel = new Coupon($conn);
            $coupon = $couponModel->findCouponByCode($code);
            
            $discount_amount = 0;

            if ($coupon) {
                // Tính toán giảm giá
                if ($coupon['discount_type'] == 'percent') {
                    $discount_amount = $total_price * ($coupon['discount_value'] / 100);
                } else { // 'fixed'
                    $discount_amount = $coupon['discount_value'];
                }
                
                $_SESSION['cart_coupon'] = [
                    'code' => $coupon['coupon_code'],
                    'discount' => $discount_amount
                ];
                set_flash_message("Áp dụng mã '" . htmlspecialchars($code) . "' thành công!", 'success'); // MỚI
                
            } else {
                // Mã không hợp lệ
                unset($_SESSION['cart_coupon']);
                set_flash_message("Mã giảm giá không hợp lệ hoặc đã hết hạn.", 'error'); // MỚI
            }
        }
        
        header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
        exit;
    }
    
    /**
     * Action: Xóa Mã giảm giá
     * CẬP NHẬT (Người 3): Thêm Flash Message
     */
    public function removeCoupon() {
        unset($_SESSION['cart_coupon']);
        set_flash_message("Đã xóa mã giảm giá.", 'info'); // MỚI
        header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
        exit;
    }
}
?>