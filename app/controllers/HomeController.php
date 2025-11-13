<?php
// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Category.php'; 
require_once ROOT_PATH . '/app/models/Brand.php'; 
require_once ROOT_PATH . '/config/functions.php'; 

class HomeController {

    /**
     * Action: Hiển thị Trang chủ
     * URL: index.php (mặc định)
     */
    public function index() {
        global $conn; 
        $productModel = new Product($conn);

        // Lấy 8 sản phẩm nổi bật để chia cho 2 khu vực (Featured và Gaming)
        $all_products = $productModel->getFeaturedProducts(8); 
        
        // 1. Chia dữ liệu: 4 sản phẩm nổi bật chính
        $featured_products = array_slice($all_products, 0, 4);
        
        // 2. Mô phỏng 4 sản phẩm Gaming bằng 4 sản phẩm tiếp theo
        // (Sử dụng array_slice để tránh lỗi "Undefined method")
        $gaming_products = array_slice($all_products, 4, 4);

        // 3. Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/home/index.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
}