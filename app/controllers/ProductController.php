<?php
// 1. Tải file Model mà bạn vừa tạo
require_once ROOT_PATH . '/app/models/Product.php';

class ProductController {

    public function index() {
        // 2. Lấy kết nối CSDL (biến $conn từ index.php)
        global $conn; 

        // 3. Tạo một đối tượng Product Model
        $productModel = new Product($conn);

        // 4. Gọi Model để lấy dữ liệu (Hàm này đã có JOIN)
        $products = $productModel->getAllProducts();

        // 5. Tải View
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        
        // Tải file View chính và "truyền" biến $products cho nó
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