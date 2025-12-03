<?php
// File: app/controllers/HomeController.php

// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Category.php'; 
require_once ROOT_PATH . '/app/models/Brand.php'; 
require_once ROOT_PATH . '/app/models/Coupon.php'; 
require_once ROOT_PATH . '/config/functions.php'; 

class HomeController {

    /**
     * Action: Hiển thị Trang chủ
     */
    public function index() {
        global $conn; 
        $productModel = new Product($conn);
        
        // Cần phải require Coupon Model trước khi khởi tạo
        if (!class_exists('Coupon')) {
            require_once ROOT_PATH . '/app/models/Coupon.php'; 
        }
        $couponModel = new Coupon($conn); 

        // 1. Lấy sản phẩm (8 sản phẩm nổi bật, chia thành 2 khu vực)
        // Giả định getFeaturedProducts lấy sản phẩm theo tiêu chí Bán Chạy/Hot
        $all_products = $productModel->getFeaturedProducts(8); 
        $featured_products = array_slice($all_products, 0, 4); // Sản phẩm Hot
        $gaming_products = array_slice($all_products, 4, 4); // Sản phẩm mới nhất (sản phẩm tiếp theo)

        // 2. LẤY MÃ GIẢM GIÁ CÔNG KHAI
        $public_coupons = $couponModel->getPublicCoupons(); 

        // 3. Lấy 4 thương hiệu nổi bật
        if (!class_exists('Brand')) {
            require_once ROOT_PATH . '/app/models/Brand.php'; 
        }
        $brandModel = new Brand($conn);
        $all_brands_data = $brandModel->getAllBrands();
        $top_brands = array_slice($all_brands_data, 0, 4); // Lấy 4 thương hiệu đầu
        
        // 4. Tải View 
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/home/index.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
}