<?php
// Tải Product model để lấy thông tin chi tiết sản phẩm
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Coupon.php'; // <-- THÊM MỚI

class CartController {

    /**
     * Action: Hiển thị trang giỏ hàng chi tiết
     * URL: index.php?controller=cart&action=index
     */
    /**
     * CẬP NHẬT (Người 3): Hiển thị trang giỏ hàng (thêm logic Coupon)
     *
     * Hàm này lấy giỏ hàng từ Session, tính tổng tiền,
     * sau đó kiểm tra Session xem có mã giảm giá nào không để tính toán
     * tổng tiền cuối cùng.
     */
    public function index() {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $cart_items = []; // Mảng chứa thông tin chi tiết sản phẩm
        $total_price = 0; // Tổng tiền HÀNG (chưa giảm)

        if (!empty($cart)) {
            global $conn;
            
            // (Đảm bảo Product.php đã được require)
            if (!class_exists('Product')) {
                 require_once ROOT_PATH . '/app/models/Product.php';
            }
            $productModel = new Product($conn);
            
            // Lặp qua giỏ hàng (chỉ có ID và số lượng)
            foreach ($cart as $product_id => $quantity) {
                $product = $productModel->getProductById($product_id);
                if ($product) {
                    // Thêm thông tin chi tiết vào mảng
                    $product['quantity_in_cart'] = $quantity;
                    $cart_items[] = $product;
                    
                    // Tính tổng tiền
                    $total_price += $product['price'] * $quantity;
                }
            }
        }
        
        // --- PHẦN LOGIC COUPON MỚI ĐƯỢC THÊM ---
        $coupon_code = $_SESSION['cart_coupon']['code'] ?? null;
        $discount_amount = $_SESSION['cart_coupon']['discount'] ?? 0;
        
        // Đảm bảo không giảm giá nhiều hơn tổng tiền
        if ($discount_amount > $total_price) {
            $discount_amount = $total_price;
        }
        
        $final_price = $total_price - $discount_amount;
        // --- KẾT THÚC PHẦN MỚI ---

        // Tải View (Controller truyền tất cả các biến này cho View)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/cart/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Action: Thêm vào giỏ hàng (đã làm)
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            
            if ($quantity <= 0) $quantity = 1;
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            
            // Chuyển đến trang giỏ hàng
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
    }

    /**
     * Action: Cập nhật số lượng sản phẩm
     * URL: (Form POST tới) index.php?controller=cart&action=update
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'];
            $quantity = (int)$_POST['quantity'];

            if (isset($_SESSION['cart'][$product_id])) {
                if ($quantity > 0) {
                    $_SESSION['cart'][$product_id] = $quantity;
                } else {
                    // Nếu số lượng là 0 hoặc âm, coi như là xóa
                    unset($_SESSION['cart'][$product_id]);
                }
            }
            
            // Cập nhật xong, quay lại trang giỏ hàng
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
    }

    /**
     * Action: Xóa sản phẩm khỏi giỏ hàng
     * URL: (Link GET) index.php?controller=cart&action=remove&id=101
     */
    public function remove() {
        $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        
        // Xóa xong, quay lại trang giỏ hàng
        header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
        exit;
    }
    /**
     * HÀM MỚI (Người 3): Áp dụng Mã giảm giá
     * URL: (Form POST tới) index.php?controller=cart&action=applyCoupon
     */
    public function applyCoupon() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['coupon_code']);
            $total_price = (float)$_POST['total_price']; // Lấy tổng tiền từ form ẩn
            
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
                
                // Lưu vào Session
                $_SESSION['cart_coupon'] = [
                    'code' => $coupon['coupon_code'],
                    'discount' => $discount_amount
                ];
                
            } else {
                // Mã không hợp lệ
                unset($_SESSION['cart_coupon']);
            }
        }
        
        // Quay lại trang giỏ hàng
        header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
        exit;
    }
    
    /**
     * HÀM MỚI (Người 3): Xóa Mã giảm giá
     * URL: (Link GET) index.php?controller=cart&action=removeCoupon
     */
    public function removeCoupon() {
        unset($_SESSION['cart_coupon']);
        header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
        exit;
    }
}
?>