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

        // 3. Kiểm tra
        if (!$product) {
            die("Không tìm thấy sản phẩm.");
        }

        // 4. Tải View (truyền biến $product cho view)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/products/detail.php'; // Sẽ tạo ở bước 4
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
}
?>