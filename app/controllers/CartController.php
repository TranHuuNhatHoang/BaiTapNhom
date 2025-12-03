<?php
// Tải Product model để lấy thông tin chi tiết sản phẩm
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Coupon.php';
// Tải file functions (để dùng set_flash_message)
require_once ROOT_PATH . '/config/functions.php';

class CartController {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
    }

    /**
     * Action: Hiển thị trang giỏ hàng chi tiết
     */
    public function index() {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $cart_items = []; 
        $total_price = 0; 
        $user_id = $_SESSION['user_id'];

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
        
        // --- LOGIC COUPON MỚI ---
        $final_price = $total_price;
        $discount_amount = 0;
        $coupon_code = $_SESSION['coupon_code'] ?? null;

        if ($coupon_code) {
            global $conn;
            $couponModel = new Coupon($conn);
            $coupon = $couponModel->getCouponByCode($coupon_code);
            
            // Kiểm tra lại tính hợp lệ
            $validation_result = $couponModel->isCouponValid($coupon, $user_id);
            
            if ($validation_result === true) {
                 $discount_amount = $couponModel->calculateDiscount($coupon, $total_price);
                 $final_price = $total_price - $discount_amount;
                 // Lưu chính xác mã và số tiền giảm vào session để Checkout dùng
                 $_SESSION['cart_coupon'] = [
                    'code' => $coupon_code,
                    'discount' => $discount_amount
                 ];
            } else {
                 // Nếu không hợp lệ, xóa session coupon
                 unset($_SESSION['coupon_code']);
                 unset($_SESSION['cart_coupon']);
                 $coupon_code = null;
                 set_flash_message("Mã giảm giá đã bị gỡ bỏ do: " . $validation_result, 'warning'); 
            }
        }
        
        // Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/cart/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Action: Thêm vào giỏ hàng (AJAX - Người 2)
     */
    public function add() {
        // ... (Giữ nguyên, không thay đổi) ...
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
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'];
            $quantity = (int)$_POST['quantity'];

            if (isset($_SESSION['cart'][$product_id])) {
                if ($quantity > 0) {
                    $_SESSION['cart'][$product_id] = $quantity;
                    set_flash_message("Cập nhật số lượng thành công.", 'success');
                } else {
                    unset($_SESSION['cart'][$product_id]);
                    set_flash_message("Đã xóa sản phẩm khỏi giỏ hàng.", 'info');
                }
            }
        }
        
        header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
        exit;
    }

    /**
     * Action: Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove() {
        $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            set_flash_message("Đã xóa sản phẩm khỏi giỏ hàng.", 'info');
        }
        
        header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
        exit;
    }

    /**
     * Action: Áp dụng Mã giảm giá (Dùng logic mới của Coupon Model)
     */
    public function applyCoupon() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $coupon_code = trim(strtoupper($_POST['coupon_code']));
            $user_id = $_SESSION['user_id'];
            
            // Tính lại tổng tiền gốc từ giỏ hàng (bảo vệ khỏi việc user sửa form)
            $cart = $_SESSION['cart'] ?? [];
            global $conn;
            $productModel = new Product($conn);
            $total_price = 0;
            foreach ($cart as $product_id => $quantity) {
                $product = $productModel->getProductById($product_id);
                if ($product) $total_price += $product['price'] * $quantity;
            }
            
            if ($total_price <= 0) {
                set_flash_message("Giỏ hàng rỗng, không thể áp dụng mã.", 'error');
                header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
                exit;
            }

            $couponModel = new Coupon($conn);
            $coupon = $couponModel->getCouponByCode($coupon_code);
            $validation_result = $couponModel->isCouponValid($coupon, $user_id); // Dùng hàm kiểm tra mới
            
            if ($validation_result === true) {
                // Hợp lệ: Tính toán giảm giá và lưu vào Session
                $discount_amount = $couponModel->calculateDiscount($coupon, $total_price);

                $_SESSION['coupon_code'] = $coupon_code; // Mã code (dùng để kiểm tra lại ở Checkout)
                $_SESSION['cart_coupon'] = [ // Chi tiết giảm giá
                    'code' => $coupon_code,
                    'discount' => $discount_amount
                ];
                set_flash_message("Áp dụng mã **" . htmlspecialchars($coupon_code) . "** thành công! Giảm **" . number_format($discount_amount) . " VND**.", 'success');
            } else {
                // Không hợp lệ: Báo lỗi và xóa session (nếu có)
                unset($_SESSION['coupon_code']);
                unset($_SESSION['cart_coupon']);
                set_flash_message("Lỗi áp dụng mã: " . $validation_result, 'error');
            }

            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
    }
    
    /**
     * Action: Xóa Mã giảm giá
     */
    public function removeCoupon() {
        unset($_SESSION['coupon_code']);
        unset($_SESSION['cart_coupon']);
        set_flash_message("Mã giảm giá đã được gỡ bỏ.", 'success');
        header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
        exit;
    }
}