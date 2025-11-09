<?php
// 1. Tải file Model mà bạn vừa tạo
require_once ROOT_PATH . '/app/models/Product.php';

class ProductController {

    /**
     * CẬP NHẬT HÀM INDEX (cho Pagination)
     */
    public function index() {
        global $conn; 
        $productModel = new Product($conn);

        // 1. Cài đặt Phân trang
        $products_per_page = 6; // Số sản phẩm trên mỗi trang (Bạn có thể đổi số này)
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) $current_page = 1;

        // 2. Lấy tổng số sản phẩm
        $total_products = $productModel->countAllProducts();
        
        // 3. Tính tổng số trang
        $total_pages = ceil($total_products / $products_per_page);
        if ($current_page > $total_pages && $total_products > 0) $current_page = $total_pages;

        // 4. Tính offset (vị trí bắt đầu)
        $offset = ($current_page - 1) * $products_per_page;

        // 5. Gọi Model để lấy dữ liệu (đã có limit, offset)
        $products = $productModel->getAllProducts($products_per_page, $offset);

        // 6. Tải View (truyền các biến $products, $total_pages, $current_page)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/products/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
       public function detail() {
        // 1. Lấy ID từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            die("ID sản phẩm không hợp lệ.");
        }

        // 2. Gọi Model
        global $conn;
        $productModel = new Product($conn);
        $product = $productModel->getProductById($id);

         //  THÊM MỚI  
        $product_images = $productModel->getProductImages($id);
        //  KẾT THÚC THÊM MỚI 

        // 3. Kiểm tra
        if (!$product) {
            die("Không tìm thấy sản phẩm.");
        }

        // 4. Tải View (truyền biến $product cho view)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/products/detail.php'; // Sẽ tạo ở bước 4
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
     // HÀM MỚI: Trang Tìm kiếm Sản phẩm
    public function search() {
        global $conn;
        $productModel = new Product($conn);
        
        // Lấy từ khóa tìm kiếm từ URL
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';
        
        $products = [];
        $total_pages = 0;
        $current_page = 1;
        $total_products = 0;

        // Chỉ tìm kiếm nếu có từ khóa
        if (!empty($query)) {
            // 1. Cài đặt Phân trang
            $products_per_page = 9;
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($current_page < 1) $current_page = 1;

            // 2. Lấy tổng số sản phẩm TÌM ĐƯỢC
            $total_products = $productModel->countSearchResults($query);
            
            // 3. Tính tổng số trang
            $total_pages = ceil($total_products / $products_per_page);
            if ($current_page > $total_pages && $total_products > 0) $current_page = $total_pages;

            // 4. Tính offset
            $offset = ($current_page - 1) * $products_per_page;

            // 5. Gọi Model để lấy dữ liệu
            $products = $productModel->searchProductsByName($query, $products_per_page, $offset);
        }

        // 6. Tải View (truyền các biến $products, $total_pages, $current_page, $query, $total_products)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/products/search.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    // (Hàm 'index', 'detail', 'search' đã có ở trên)
    // ...

    /**
     * HÀM MỚI (Người 2): Hiển thị sản phẩm theo Danh mục
     * URL: index.php?controller=product&action=category&id=1
     */
    public function category() {
        global $conn;
        
        // 1. Lấy ID Danh mục từ URL
        $category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($category_id <= 0) die("Danh mục không hợp lệ.");

        // 2. Tải các Model cần thiết
        $productModel = new Product($conn);
        // (Kiểm tra class Category đã được require ở navbar chưa)
        if (!class_exists('Category')) {
            require_once ROOT_PATH . '/app/models/Category.php';
        }
        $categoryModel = new Category($conn);
        
        // 3. Lấy thông tin danh mục (để lấy tên)
        $category = $categoryModel->getCategoryById($category_id);
        if (!$category) die("Danh mục không tồn tại.");

        // 4. Cài đặt Phân trang (Giống hệt hàm index/search)
        $products_per_page = 9; // 9 sản phẩm/trang
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) $current_page = 1;

        // 5. Lấy tổng số sản phẩm TRONG DANH MỤC NÀY
        $total_products = $productModel->countProductsByCategory($category_id);
        
        // 6. Tính tổng số trang
        $total_pages = ceil($total_products / $products_per_page);
        if ($current_page > $total_pages && $total_products > 0) $current_page = $total_pages;

        // 7. Tính offset
        $offset = ($current_page - 1) * $products_per_page;

        // 8. Gọi Model để lấy dữ liệu
        $products = $productModel->getProductsByCategory($category_id, $products_per_page, $offset);

        // 9. Tải View (truyền tất cả các biến cần thiết)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/products/category.php'; // Sẽ tạo ở Bước 4
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

} // <-- Dấu } đóng class ProductController

?>