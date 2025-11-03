<?php
// Tải các file Model (sau này)
// require_once ROOT_PATH . '/app/models/Product.php';

class ProductController {

    /**
     * Hành động (action) mặc định: Hiển thị trang chủ/danh sách sản phẩm
     */
    public function index() {
        // (Code của Giai đoạn 2 sẽ xử lý logic ở đây)
        // $productModel = new Product();
        // $products = $productModel->getAllProducts();

        // Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        
        // Đây là nội dung trang (Giai đoạn 2 sẽ tải view 'products/index.php')
        echo "<h1>Chào mừng đến với Cửa hàng Laptop</h1>";
        echo "<p>Nội dung trang chủ (danh sách sản phẩm) sẽ được thực hiện ở Giai đoạn 2.</p>";
        
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    // (Các action khác như 'detail' sẽ được thêm ở Giai đoạn 2)
}
?>