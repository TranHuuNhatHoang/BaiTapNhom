<?php
// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Category.php'; // Cần cho navbar
require_once ROOT_PATH . '/app/models/Brand.php'; // Cần cho navbar
require_once ROOT_PATH . '/config/functions.php'; // Cần cho flash message

class HomeController {

    /**
     * Action: Hiển thị Trang chủ
     * URL: index.php (mặc định)
     */
    public function index() {
        global $conn; 
        $productModel = new Product($conn);

        // 1. Lấy Sản phẩm Nổi bật (Logic này được copy từ ProductController)
        $featured_products = $productModel->getFeaturedProducts(4); // Lấy 4 SP
        
        // 2. Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/home/index.php'; // Tải View MỚI
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
}
?>