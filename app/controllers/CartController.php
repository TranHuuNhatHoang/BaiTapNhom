<?php
class CartController {

    /**
     * Action: Thêm sản phẩm vào giỏ hàng (Session)
     * URL: (Form POST tới) index.php?controller=cart&action=add
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Lấy thông tin từ form
            $product_id = $_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            
            // 2. Validate
            if ($quantity <= 0) {
                $quantity = 1;
            }

            // 3. Khởi tạo giỏ hàng nếu chưa có
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // 4. Thêm/Cập nhật sản phẩm vào giỏ
            if (isset($_SESSION['cart'][$product_id])) {
                // Nếu đã có, cộng dồn số lượng
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                // Nếu chưa có, thêm mới
                $_SESSION['cart'][$product_id] = $quantity;
            }
            
            // 5. Thêm xong, quay lại trang chủ (hoặc trang chi tiết)
            // Tốt hơn là quay về trang giỏ hàng để xem
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
    }

    /**
     * Action: Hiển thị trang giỏ hàng (tạm thời)
     * URL: index.php?controller=cart&action=index
     */
    public function index() {
        // Tải header
        require_once ROOT_PATH . '/app/views/layouts/header.php';

        echo "<h1>Giỏ hàng của bạn</h1>";
        
        // In ra nội dung giỏ hàng (để test)
        echo "<pre>";
        if (!empty($_SESSION['cart'])) {
            print_r($_SESSION['cart']);
        } else {
            echo "Giỏ hàng rỗng.";
        }
        echo "</pre>";
        
        // (Sau này bạn sẽ tạo file app/views/cart/index.php đẹp hơn)

        // Tải footer
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
}
?>