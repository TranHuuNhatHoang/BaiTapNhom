<?php
// 1. Tải file Model mà bạn vừa tạo
require_once ROOT_PATH . '/app/models/Product.php';

class ProductController {

    /**
     * CẬP NHẬT (NGƯỜI 2): Thêm $price_range
     */
    public function index() {
        global $conn; 
        $productModel = new Product($conn);

        // 1. Cài đặt Phân trang
        $products_per_page = 6; // Số sản phẩm trên mỗi trang
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) $current_page = 1;

        // --- LẤY BIẾN LỌC & SẮP XẾP ---
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at DESC';
        $price_range = isset($_GET['price']) ? $_GET['price'] : null; // <-- THÊM MỚI
        // --- KẾT THÚC ---

        // --- THÊM MỚI (Người 2 - GĐ16) ---
        // Chỉ lấy SP nổi bật khi không lọc/sắp xếp và ở trang 1
        $featured_products = [];
        if ($current_page == 1 && empty($price_range) && empty($_GET['sort'])) {
            $featured_products = $productModel->getFeaturedProducts(4); // Lấy 4 SP
        }
        // --- KẾT THÚC THÊM MỚI ---

        // 2. Lấy tổng số sản phẩm (ĐÃ LỌC)
        $total_products = $productModel->countAllProducts($price_range); // <-- SỬA
        
        // 3. Tính tổng số trang
        $total_pages = ceil($total_products / $products_per_page);
        if ($current_page > $total_pages && $total_products > 0) $current_page = $total_pages;

        // 4. Tính offset (vị trí bắt đầu)
        $offset = ($current_page - 1) * $products_per_page;

        // 5. Gọi Model (truyền thêm $sort và $price_range)
        $products = $productModel->getAllProducts($products_per_page, $offset, $sort, $price_range); // <-- SỬA

        // 6. Tải View (truyền tất cả biến)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/products/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Action: Hiển thị trang chi tiết sản phẩm (Giữ nguyên)
     */
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
        require_once ROOT_PATH . '/app/views/products/detail.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * CẬP NHẬT (NGƯỜI 2): Thêm $price_range
     */
    public function search() {
        global $conn;
        $productModel = new Product($conn);
        
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';
        
        $products = [];
        $total_pages = 0;
        $current_page = 1;
        $total_products = 0;

        if (!empty($query)) {
            $products_per_page = 9;
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($current_page < 1) $current_page = 1;
            
            // --- LẤY BIẾN LỌC & SẮP XẾP ---
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at DESC';
            $price_range = isset($_GET['price']) ? $_GET['price'] : null; // <-- THÊM MỚI
            // --- KẾT THÚC ---

            // 2. Lấy tổng số sản phẩm TÌM ĐƯỢC (ĐÃ LỌC)
            $total_products = $productModel->countSearchResults($query, $price_range); // <-- SỬA
            
            // 3. Tính tổng số trang
            $total_pages = ceil($total_products / $products_per_page);
            if ($current_page > $total_pages && $total_products > 0) $current_page = $total_pages;

            // 4. Tính offset
            $offset = ($current_page - 1) * $products_per_page;

            // 5. Gọi Model (truyền thêm $sort và $price_range)
            $products = $productModel->searchProductsByName($query, $products_per_page, $offset, $sort, $price_range); // <-- SỬA
        }

        // 6. Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/products/search.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * CẬP NHẬT (NGƯỜI 2): Thêm $price_range
     */
    public function category() {
        global $conn;
        
        $category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($category_id <= 0) die("Danh mục không hợp lệ.");

        $productModel = new Product($conn);
        if (!class_exists('Category')) {
            require_once ROOT_PATH . '/app/models/Category.php';
        }
        $categoryModel = new Category($conn);
        
        $category = $categoryModel->getCategoryById($category_id);
        if (!$category) die("Danh mục không tồn tại.");

        $products_per_page = 9;
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // --- LẤY BIẾN LỌC & SẮP XẾP ---
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at DESC';
        $price_range = isset($_GET['price']) ? $_GET['price'] : null; // <-- THÊM MỚI
        // --- KẾT THÚC ---

        // 5. Lấy tổng số sản phẩm (ĐÃ LỌC)
        $total_products = $productModel->countProductsByCategory($category_id, $price_range); // <-- SỬA
        
        // 6. Tính tổng số trang
        $total_pages = ceil($total_products / $products_per_page);
        if ($current_page < 1) $current_page = 1; // Sửa lỗi logic nhỏ
        if ($current_page > $total_pages && $total_products > 0) $current_page = $total_pages;

        // 7. Tính offset
        $offset = ($current_page - 1) * $products_per_page;

        // 8. Gọi Model (truyền thêm $sort và $price_range)
        $products = $productModel->getProductsByCategory($category_id, $products_per_page, $offset, $sort, $price_range); // <-- SỬA

        // 9. Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/products/category.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * CẬP NHẬT (NGƯỜI 2): Thêm $price_range
     */
    public function brand() {
        global $conn;
        
        $brand_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($brand_id <= 0) die("Thương hiệu không hợp lệ.");

        $productModel = new Product($conn);
        if (!class_exists('Brand')) {
            require_once ROOT_PATH . '/app/models/Brand.php';
        }
        $brandModel = new Brand($conn);
        
        $brand = $brandModel->getBrandById($brand_id);
        if (!$brand) die("Thương hiệu không tồn tại.");

        $products_per_page = 9;
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // --- LẤY BIẾN LỌC & SẮP XẾP ---
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at DESC';
        $price_range = isset($_GET['price']) ? $_GET['price'] : null; // <-- THÊM MỚI
        // --- KẾT THÚC ---

        // 5. Lấy tổng số sản phẩm (ĐÃ LỌC)
        $total_products = $productModel->countProductsByBrand($brand_id, $price_range); // <-- SỬA
        
        // 6. Tính tổng số trang
        $total_pages = ceil($total_products / $products_per_page);
        if ($current_page < 1) $current_page = 1;
        if ($current_page > $total_pages && $total_products > 0) $current_page = $total_pages;

        // 7. Tính offset
        $offset = ($current_page - 1) * $products_per_page;
        
        // 8. Lấy dữ liệu (truyền thêm $sort và $price_range)
        $products = $productModel->getProductsByBrand($brand_id, $products_per_page, $offset, $sort, $price_range); // <-- SỬA

        // 9. Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/products/brand.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
} 
?>