<?php
// 1. Tải cả 3 file Model
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Brand.php';
require_once ROOT_PATH . '/app/models/Category.php';
require_once ROOT_PATH . '/app/models/Order.php';
class AdminController {

    /**
     * Action: Hiển thị danh sách sản phẩm (trang chính admin)
     * URL: index.php?controller=admin&action=index (hoặc admin)
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
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) die("ID không hợp lệ.");

        global $conn;
        
        // 1. Lấy thông tin sản phẩm cần sửa
        $productModel = new Product($conn);
        $product = $productModel->getProductById($id); // <-- Cần hàm này
        if (!$product) die("Không tìm thấy sản phẩm.");
        
        // 2. Lấy data cho dropdown (giống hệt hàm create)
        $brandModel = new Brand($conn);
        $brands = $brandModel->getAllBrands();
        
        $categoryModel = new Category($conn);
        $categories = $categoryModel->getAllCategories();
        
        // 3. Tải view form (dùng chung view 'product_form.php')
        // (Truyền cả $product, $brands, $categories cho view)
        require_once ROOT_PATH . '/app/views/admin/product_form.php';
    }
public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy ID từ form (phải là hidden input)
            $id = (int)$_POST['product_id'];
            if ($id <= 0) die("ID không hợp lệ.");

            // Lấy data (giống hàm store)
            $name = $_POST['product_name'];
            $price = $_POST['price'];
            $brand_id = $_POST['brand_id'];
            $category_id = $_POST['category_id'];
            $quantity = $_POST['quantity'];
            $description = $_POST['description'];
            $main_image = $_POST['main_image'];

            global $conn;
            $productModel = new Product($conn);
            
            if ($productModel->updateProduct($id, $name, $price, $brand_id, $category_id, $quantity, $description, $main_image)) {
                // Sửa thành công, quay lại trang danh sách
                header("Location: " . BASE_URL . "index.php?controller=admin");
                exit;
            } else {
                die("Lỗi khi cập nhật sản phẩm.");
            }
        }
    }
    public function delete() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) die("ID không hợp lệ.");
        
        global $conn;
        $productModel = new Product($conn);
        
        if ($productModel->deleteProduct($id)) {
            // Xóa thành công, quay lại trang danh sách
            header("Location: " . BASE_URL . "index.php?controller=admin");
            exit;
        } else {
            die("Lỗi khi xóa sản phẩm.");
        }
    }
   public function listOrders() {
        global $conn;
        $orderModel = new Order($conn);
        $orders = $orderModel->getAllOrders();
        
        // Tải view danh sách
        require_once ROOT_PATH . '/app/views/admin/order_list.php'; // Sẽ tạo ở bước 3
    }
      public function listBrands() {
        global $conn;
        $brandModel = new Brand($conn);
        $brands = $brandModel->getAllBrands();
        require_once ROOT_PATH . '/app/views/admin/brand_list.php'; // Sẽ tạo
    }

    /**
     * Action: Hiển thị form thêm Brand
     */
    public function createBrand() {
        require_once ROOT_PATH . '/app/views/admin/brand_form.php'; // Sẽ tạo
    }

    /**
     * Action: Xử lý lưu Brand mới
     */
    public function storeBrand() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $brandModel = new Brand($conn);
            $brandModel->createBrand($_POST['brand_name'], $_POST['description'], $_POST['logo']);
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listBrands");
            exit;
        }
    }

    /**
     * Action: Hiển thị form sửa Brand
     */
    public function editBrand() {
        $id = (int)$_GET['id'];
        global $conn;
        $brandModel = new Brand($conn);
        $brand = $brandModel->getBrandById($id);
        require_once ROOT_PATH . '/app/views/admin/brand_form.php'; // Dùng chung form
    }

    /**
     * Action: Xử lý cập nhật Brand
     */
    public function updateBrand() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['brand_id'];
            global $conn;
            $brandModel = new Brand($conn);
            $brandModel->updateBrand($id, $_POST['brand_name'], $_POST['description'], $_POST['logo']);
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listBrands");
            exit;
        }
    }
    
    /**
     * Action: Xử lý xóa Brand
     */
    public function deleteBrand() {
        $id = (int)$_GET['id'];
        global $conn;
        $brandModel = new Brand($conn);
        $brandModel->deleteBrand($id);
        header("Location: " . BASE_URL . "index.php?controller=admin&action=listBrands");
        exit;
    }
     /**
     * Action: Hiển thị danh sách Categories
     */
    public function listCategories() {
        global $conn;
        $categoryModel = new Category($conn);
        $categories = $categoryModel->getAllCategories();
        require_once ROOT_PATH . '/app/views/admin/category_list.php'; // Sẽ tạo
    }

    /**
     * Action: Hiển thị form thêm Category
     */
    public function createCategory() {
        require_once ROOT_PATH . '/app/views/admin/category_form.php'; // Sẽ tạo
    }

    /**
     * Action: Xử lý lưu Category mới
     */
    public function storeCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $categoryModel = new Category($conn);
            $categoryModel->createCategory($_POST['category_name'], $_POST['description']);
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listCategories");
            exit;
        }
    }

    /**
     * Action: Hiển thị form sửa Category
     */
    public function editCategory() {
        $id = (int)$_GET['id'];
        global $conn;
        $categoryModel = new Category($conn);
        $category = $categoryModel->getCategoryById($id);
        require_once ROOT_PATH . '/app/views/admin/category_form.php'; // Dùng chung form
    }

    /**
     * Action: Xử lý cập nhật Category
     */
    public function updateCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['category_id'];
            global $conn;
            $categoryModel = new Category($conn);
            $categoryModel->updateCategory($id, $_POST['category_name'], $_POST['description']);
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listCategories");
            exit;
        }
    }
    
    /**
     * Action: Xử lý xóa Category
     */
    public function deleteCategory() {
        $id = (int)$_GET['id'];
        global $conn;
        $categoryModel = new Category($conn);
        $categoryModel->deleteCategory($id);
        header("Location: " . BASE_URL . "index.php?controller=admin&action=listCategories");
        exit;
    }
}
?>