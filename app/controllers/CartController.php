<?php
// Tải Product model để lấy thông tin chi tiết sản phẩm
require_once ROOT_PATH . '/app/models/Product.php';

class CartController {

    /**
     * Action: Hiển thị trang giỏ hàng chi tiết
     * URL: index.php?controller=cart&action=index
     */
    public function index() {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $cart_items = []; // Mảng chứa thông tin chi tiết sản phẩm
        $total_price = 0;

        if (!empty($cart)) {
            global $conn;
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
        
        // Tải View (truyền $cart_items và $total_price cho view)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/cart/index.php'; // Sẽ tạo ở bước 3
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
}
?>