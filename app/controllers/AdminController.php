<?php
// 1. Tải cả 3 file Model
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Brand.php';
require_once ROOT_PATH . '/app/models/Category.php';

class AdminController {

    /**
     * Action: Hiển thị danh sách sản phẩm (trang chính admin)
     * URL: index.php?controller=admin&action=index (hoặc admin)
     */
    public function index() {
        global $conn;
        $productModel = new Product($conn);
        $products = $productModel->getAllProducts();
        
        // Tải view danh sách
        require_once ROOT_PATH . '/app/views/admin/product_list.php';
    }

    /**
     * Action: Hiển thị form thêm sản phẩm mới
     * URL: index.php?controller=admin&action=create
     */
    public function create() {
        global $conn;
        
        // Lấy dữ liệu cho 2 dropdown
        $brandModel = new Brand($conn);
        $brands = $brandModel->getAllBrands();
        
        $categoryModel = new Category($conn);
        $categories = $categoryModel->getAllCategories();
        
        // Tải view form (và truyền $brands, $categories cho nó)
        require_once ROOT_PATH . '/app/views/admin/product_form.php';
    }

    /**
     * Action: Xử lý lưu sản phẩm mới (khi POST form)
     * URL: (Form sẽ POST về) index.php?controller=admin&action=store
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Lấy dữ liệu từ form
            $name = $_POST['product_name'];
            $price = $_POST['price'];
            $brand_id = $_POST['brand_id'];
            $category_id = $_POST['category_id'];
            $quantity = $_POST['quantity'];
            $description = $_POST['description'];
            
            // Tạm thời xử lý ảnh (main_image) đơn giản
            // (Upload file ảnh là một tính năng phức tạp, sẽ làm ở nhánh khác)
            // Giả sử người dùng nhập tên file ảnh
            $main_image = $_POST['main_image']; 

            // 2. Gọi Model
            global $conn;
            $productModel = new Product($conn);
            
            // 3. Thực thi
            if ($productModel->createProduct($name, $price, $brand_id, $category_id, $quantity, $description, $main_image)) {
                // Thêm thành công, quay lại trang danh sách admin
                echo "Thêm sản phẩm thành công! Đang chuyển hướng...";
                header("Refresh: 2; URL=" . BASE_URL . "index.php?controller=admin");
                exit;
            } else {
                die("Lỗi khi thêm sản phẩm.");
            }
        }
    }
}
?>