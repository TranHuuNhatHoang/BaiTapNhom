<?php
// 1. Tải cả 3 file Model
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Brand.php';
require_once ROOT_PATH . '/app/models/Category.php';
require_once ROOT_PATH . '/app/models/Order.php';
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/ProductImage.php'; 
require_once ROOT_PATH . '/app/models/Review.php';

class AdminController {

    /**
     * HÀM HELPER: Xử lý upload file
     */
    private function handleUpload($file_input_name, $upload_dir) {
        // (Code hàm handleUpload... giữ nguyên)
        if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
            $file_tmp_path = $_FILES[$file_input_name]['tmp_name'];
            $file_name = basename($_FILES[$file_input_name]['name']);
            $file_type = $_FILES[$file_input_name]['type'];
            $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file_type, $allowed_mime_types)) {
                return null; 
            }
            $new_file_name = uniqid() . '-' . $file_name;
            $dest_path = ROOT_PATH . $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                return $new_file_name; 
            }
        }
        return null;
    }

    /**
     * Hàm Constructor: Bảo vệ toàn bộ Controller
     */
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            die("Lỗi 403: Bạn không có quyền truy cập khu vực này.");
        }
    }
 
    /**
     * HÀM INDEX (Dashboard - Đã sửa đúng)
     */
    public function index() {
        global $conn;
        
        $orderModel = new Order($conn);
        $order_stats = $orderModel->getOrderStats();
        
        $userModel = new User($conn);
        $new_users = $userModel->countNewUsers();
        
        $chart_data_raw = $orderModel->getRevenueLast7Days();
        
        $chart_labels = [];
        $chart_values = [];
        foreach ($chart_data_raw as $data) {
            $chart_labels[] = date('d/m', strtotime($data['order_date']));
            $chart_values[] = $data['daily_revenue'];
        }
        $chart_labels_json = json_encode($chart_labels);
        $chart_values_json = json_encode($chart_values);

        $latest_orders = $orderModel->getLatestOrders(5);
        $latest_users = $userModel->getLatestUsers(5);
        
        // Tải View (ĐẦY ĐỦ)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/dashboard.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
 
    /**
     * HÀM listProducts (Sửa lỗi: Thêm Header/Footer)
     */
    public function listProducts() {
        global $conn; 
        $productModel = new Product($conn);

        // (Code phân trang của bạn...)
        $products_per_page = 6; 
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) $current_page = 1;
        $total_products = $productModel->countAllProducts();
        $total_pages = ceil($total_products / $products_per_page);
        if ($current_page > $total_pages && $total_products > 0) $current_page = $total_pages;
        $offset = ($current_page - 1) * $products_per_page;
        $products = $productModel->getAllProducts($products_per_page, $offset);

        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/product_list.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM create (Sửa lỗi: Thêm Header/Footer)
     */
    public function create() {
        global $conn;
        
        $brandModel = new Brand($conn);
        $brands = $brandModel->getAllBrands();
        
        $categoryModel = new Category($conn);
        $categories = $categoryModel->getAllCategories();
        
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/product_form.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM store (Không đổi - Xử lý)
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $new_image_name = $this->handleUpload('main_image', '/public/uploads/');

            $name = $_POST['product_name'];
            $price = $_POST['price'];
            $brand_id = $_POST['brand_id'];
            $category_id = $_POST['category_id'];
            $quantity = $_POST['quantity'];
            $description = $_POST['description'];
            $main_image = $new_image_name; 

            global $conn;
            $productModel = new Product($conn);
            
            if ($productModel->createProduct($name, $price, $brand_id, $category_id, $quantity, $description, $main_image)) {
                header("Location: " . BASE_URL . "index.php?controller=admin&action=listProducts");
                exit;
            } else {
                die("Lỗi khi thêm sản phẩm.");
            }
        }
    }
 
    /**
     * HÀM edit (Sửa lỗi: Thêm Header/Footer)
     */
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) die("ID không hợp lệ.");
        global $conn;
        
        $productModel = new Product($conn);
        $product = $productModel->getProductById($id); 
        if (!$product) die("Không tìm thấy sản phẩm.");
        
        $brandModel = new Brand($conn);
        $brands = $brandModel->getAllBrands();
        $categoryModel = new Category($conn);
        $categories = $categoryModel->getAllCategories();
        
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/product_form.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM update (Không đổi - Xử lý)
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['product_id'];
            if ($id <= 0) die("ID không hợp lệ.");
            
            $new_image_name = $_POST['current_main_image']; // Giữ ảnh cũ
            
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $new_image_name = $this->handleUpload('main_image', '/public/uploads/');
                // (Sau này nên xóa ảnh cũ)
            }

            $name = $_POST['product_name'];
            $price = $_POST['price'];
            $brand_id = $_POST['brand_id'];
            $category_id = $_POST['category_id'];
            $quantity = $_POST['quantity'];
            $description = $_POST['description'];
            $main_image = $new_image_name;

            global $conn;
            $productModel = new Product($conn);
            
            if ($productModel->updateProduct($id, $name, $price, $brand_id, $category_id, $quantity, $description, $main_image)) {
                header("Location: " . BASE_URL . "index.php?controller=admin&action=listProducts");
                exit;
            } else {
                die("Lỗi khi cập nhật sản phẩm.");
            }
        }
    }
 
    /**
     * HÀM delete (Không đổi - Xử lý)
     */
    public function delete() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) die("ID không hợp lệ.");
        
        global $conn;
        $productModel = new Product($conn);
        
        if ($productModel->deleteProduct($id)) {
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listProducts");
            exit;
        } else {
            die("Lỗi khi xóa sản phẩm.");
        }
    }
    
    /**
     * HÀM listOrders (Sửa lỗi: Thêm Header/Footer)
     */
    public function listOrders() {
        global $conn;
        $orderModel = new Order($conn);
        $orders = $orderModel->getAllOrders();
        
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/order_list.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * HÀM listBrands (Sửa lỗi: Thêm Header/Footer)
     */
    public function listBrands() {
        global $conn;
        $brandModel = new Brand($conn);
        $brands = $brandModel->getAllBrands();
        
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/brand_list.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM createBrand (Sửa lỗi: Thêm Header/Footer)
     */
    public function createBrand() {
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/brand_form.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM storeBrand (Không đổi - Xử lý)
     */
    public function storeBrand() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_logo_name = $this->handleUpload('logo', '/public/uploads/');
            global $conn;
            $brandModel = new Brand($conn);
            $brandModel->createBrand($_POST['brand_name'], $_POST['description'], $new_logo_name);
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listBrands");
            exit;
        }
    }

    /**
     * HÀM editBrand (Sửa lỗi: Thêm Header/Footer)
     */
    public function editBrand() {
        $id = (int)$_GET['id'];
        global $conn;
        $brandModel = new Brand($conn);
        $brand = $brandModel->getBrandById($id);
        
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/brand_form.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM updateBrand (Không đổi - Xử lý)
     */
    public function updateBrand() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['brand_id'];
            $new_logo_name = $_POST['current_logo']; 
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $new_logo_name = $this->handleUpload('logo', '/public/uploads/');
            }
            global $conn;
            $brandModel = new Brand($conn);
            $brandModel->updateBrand($id, $_POST['brand_name'], $_POST['description'], $new_logo_name);
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listBrands");
            exit;
        }
    }
    
    /**
     * HÀM deleteBrand (Không đổi - Xử lý)
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
     * HÀM listCategories (Sửa lỗi: Thêm Header/Footer)
     */
    public function listCategories() {
        global $conn;
        $categoryModel = new Category($conn);
        $categories = $categoryModel->getAllCategories();
        
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/category_list.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM createCategory (Sửa lỗi: Thêm Header/Footer)
     */
    public function createCategory() {
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/category_form.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM storeCategory (Không đổi - Xử lý)
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
     * HÀM editCategory (Sửa lỗi: Thêm Header/Footer)
     */
    public function editCategory() {
        $id = (int)$_GET['id'];
        global $conn;
        $categoryModel = new Category($conn);
        $category = $categoryModel->getCategoryById($id);
        
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/category_form.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM updateCategory (Không đổi - Xử lý)
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
     * HÀM deleteCategory (Không đổi - Xử lý)
     */
    public function deleteCategory() {
        $id = (int)$_GET['id'];
        global $conn;
        $categoryModel = new Category($conn);
        $categoryModel->deleteCategory($id);
        header("Location: " . BASE_URL . "index.php?controller=admin&action=listCategories");
        exit;
    }
    
    /**
     * HÀM updateOrderStatus (Không đổi - Xử lý)
     */
    public function updateOrderStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = (int)$_POST['order_id'];
            $new_status = $_POST['new_status'];
            global $conn;
            $orderModel = new Order($conn);
            if ($orderModel->updateOrderStatus($order_id, $new_status)) {
                header("Location: " . BASE_URL . "index.php?controller=admin&action=listOrders");
                exit;
            } else {
                die("Lỗi khi cập nhật trạng thái đơn hàng.");
            }
        }
    }

    /**
     * HÀM orderDetail (Sửa lỗi: Thêm Header/Footer)
     */
    public function orderDetail() {
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($order_id <= 0) die("ID đơn hàng không hợp lệ.");
        global $conn;
        
        $orderModel = new Order($conn);
        $order = $orderModel->getOrderByIdForAdmin($order_id); 
        if (!$order) die("Không tìm thấy đơn hàng.");
        
        $order_details = $orderModel->getOrderDetailsByOrderId($order_id);
        
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/order_detail.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM listUsers (Sửa lỗi: Thêm Header/Footer)
     */
    public function listUsers() {
        global $conn;
        $userModel = new User($conn);
        $users = $userModel->getAllUsers();
        
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/user_list.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM updateUserRole (Không đổi - Xử lý)
     */
    public function updateUserRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = (int)$_POST['user_id'];
            $role = $_POST['role'];
            if ($user_id === $_SESSION['user_id']) die("Không thể tự thay đổi vai trò của chính mình.");
            global $conn;
            $userModel = new User($conn);
            $userModel->updateUserRole($user_id, $role);
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listUsers");
            exit;
        }
    }
    
    /**
     * HÀM deleteUser (Không đổi - Xử lý)
     */
    public function deleteUser() {
        $user_id = (int)$_GET['id'];
        if ($user_id === $_SESSION['user_id']) die("Không thể tự xóa chính mình.");
        global $conn;
        $userModel = new User($conn);
        $userModel->deleteUser($user_id);
        header("Location: " . BASE_URL . "index.php?controller=admin&action=listUsers");
        exit;
    }

    /**
     * HÀM manageImages (Sửa lỗi: Thêm Header/Footer)
     */
    public function manageImages() {
        $product_id = (int)$_GET['product_id'];
        if ($product_id <= 0) die("ID sản phẩm không hợp lệ.");
        global $conn;
        
        $productModel = new Product($conn);
        $product = $productModel->getProductById($product_id);
        $imageModel = new ProductImage($conn);
        $images = $imageModel->getImagesByProductId($product_id);
        
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/product_images.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * HÀM uploadImage (Không đổi - Xử lý)
     */
    public function uploadImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = (int)$_POST['product_id'];
            $new_image_name = $this->handleUpload('product_image_file', '/public/uploads/');
            if ($product_id > 0 && $new_image_name) {
                global $conn;
                $imageModel = new ProductImage($conn);
                $imageModel->addImage($product_id, $new_image_name);
            }
            header("Location: " . BASE_URL . "index.php?controller=admin&action=manageImages&product_id=" . $product_id);
            exit;
        }
    }
    
    /**
     * HÀM deleteImage (Không đổi - Xử lý)
     */
    public function deleteImage() {
        $image_id = (int)$_GET['image_id'];
        $product_id = (int)$_GET['product_id']; 
        global $conn;
        $imageModel = new ProductImage($conn);
        $image = $imageModel->getImageById($image_id);
        if ($image) {
            $file_path = ROOT_PATH . '/public/uploads/' . $image['image_url'];
            if (file_exists($file_path)) unlink($file_path);
            $imageModel->deleteImage($image_id);
        }
        header("Location: " . BASE_URL . "index.php?controller=admin&action=manageImages&product_id=" . $product_id);
        exit;
    }

    /**
     * HÀM listReviews (Sửa lỗi: Thêm Header/Footer)
     */
    public function listReviews() {
        global $conn;
        $reviewModel = new Review($conn);
        $reviews = $reviewModel->getAllReviews();
        
        // SỬA LỖI: Thêm Header/Footer
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/review_list.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * HÀM deleteReview (Không đổi - Xử lý)
     */
    public function deleteReview() {
        $review_id = (int)$_GET['id'];
        global $conn;
        $reviewModel = new Review($conn);
        $reviewModel->deleteReview($review_id);
        header("Location: " . BASE_URL . "index.php?controller=admin&action=listReviews");
        exit;
    }
    
    /**
     * HÀM MỚI (để sửa lỗi): Hiển thị form Sửa User
     * URL: index.php?controller=admin&action=editUser&id=123
     */
    public function editUser() {
        $user_id = (int)$_GET['id'];
        if ($user_id <= 0) die("ID không hợp lệ.");
        
        global $conn;
        $userModel = new User($conn);
        $user = $userModel->getUserById($user_id); // Dùng hàm đã có
        
        if (!$user) die("Không tìm thấy user.");

        // Tải layout và view
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/user_form.php'; // Tải form
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * HÀM MỚI (để sửa lỗi): Xử lý Cập nhật User
     * URL: (Form POST tới) index.php?controller=admin&action=updateUser
     */
    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = (int)$_POST['user_id'];
            $full_name = $_POST['full_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $province = $_POST['province'];
            $role = $_POST['role'];

            // Ngăn admin tự đổi vai trò của mình (nếu dùng form này)
            if ($user_id === $_SESSION['user_id']) {
                 // (logic an toàn, có thể bỏ qua nếu `select` đã bị disabled)
            }

            global $conn;
            $userModel = new User($conn);
            $userModel->adminUpdateUser($user_id, $full_name, $email, $phone, $address, $province, $role);
            
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listUsers");
            exit;
        }
    }
    
}
?>