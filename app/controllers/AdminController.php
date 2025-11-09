<?php
// 1. Tải cả 3 file Model
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Brand.php';
require_once ROOT_PATH . '/app/models/Category.php';
require_once ROOT_PATH . '/app/models/Order.php';
require_once ROOT_PATH . '/app/models/User.php';

class AdminController {

    /**
     * HÀM HELPER: Xử lý upload file
     * Trả về tên file nếu thành công, trả về null nếu thất bại hoặc không có file
     */
    private function handleUpload($file_input_name, $upload_dir) {
        // Kiểm tra xem có file được tải lên không và không có lỗi
        if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
            
            $file_tmp_path = $_FILES[$file_input_name]['tmp_name'];
            $file_name = basename($_FILES[$file_input_name]['name']);
            $file_type = $_FILES[$file_input_name]['type'];
            
            // Chỉ cho phép định dạng ảnh
            $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file_type, $allowed_mime_types)) {
                return null; // Không phải file ảnh
            }
            
            // Tạo tên file duy nhất (để tránh ghi đè)
            $new_file_name = uniqid() . '-' . $file_name;
            $dest_path = ROOT_PATH . $upload_dir . $new_file_name;

            // Di chuyển file vào thư mục đích
            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                return $new_file_name; // Trả về TÊN FILE MỚI
            }
        }
        return null; // Thất bại
    }

    /**
     * Action: Hiển thị danh sách sản phẩm (trang chính admin)
     * URL: index.php?controller=admin&action=index (hoặc admin)
     */

    /**
 * HÀM INDEX MỚI (cho Dashboard)
 */
    public function index() {
        global $conn;

        // 1. Lấy thống kê đơn hàng
        $orderModel = new Order($conn);
        $order_stats = $orderModel->getOrderStats();

        // 2. Lấy thống kê user
        $userModel = new User($conn);
        $new_users = $userModel->countNewUsers();

        // 3. Tải view dashboard
        require_once ROOT_PATH . '/app/views/admin/dashboard.php';
    }
    public function listProducts() {
        global $conn; 
        $productModel = new Product($conn);

        // 1. Cài đặt Phân trang
        $products_per_page = 6; 
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

public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Xử lý upload ảnh
            $new_image_name = $this->handleUpload('main_image', '/public/uploads/');

            // 2. Lấy dữ liệu từ form
            $name = $_POST['product_name'];
            $price = $_POST['price'];
            $brand_id = $_POST['brand_id'];
            $category_id = $_POST['category_id'];
            $quantity = $_POST['quantity'];
            $description = $_POST['description'];
            $main_image = $new_image_name; // Lấy tên file đã xử lý

            // 3. Gọi Model
            global $conn;
            $productModel = new Product($conn);
            
            if ($productModel->createProduct($name, $price, $brand_id, $category_id, $quantity, $description, $main_image)) {
                header("Location: " . BASE_URL . "index.php?controller=admin");
                exit;
            } else {
                die("Lỗi khi thêm sản phẩm.");
            }
        }
    }
    // Giữ nguyên hàm edit() để hiển thị form
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) die("ID không hợp lệ.");

        global $conn;
        
        // 1. Lấy thông tin sản phẩm cần sửa
        $productModel = new Product($conn);
        $product = $productModel->getProductById($id); 
        if (!$product) die("Không tìm thấy sản phẩm.");
        
        // 2. Lấy data cho dropdown (giống hệt hàm create)
        $brandModel = new Brand($conn);
        $brands = $brandModel->getAllBrands();
        
        $categoryModel = new Category($conn);
        $categories = $categoryModel->getAllCategories();
        
        // 3. Tải view form (dùng chung view 'product_form.php')
        require_once ROOT_PATH . '/app/views/admin/product_form.php';
    }

  public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['product_id'];
            if ($id <= 0) die("ID không hợp lệ.");
            
            $new_image_name = null;
            
            // 1. Kiểm tra xem có TẢI ẢNH MỚI không
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $new_image_name = $this->handleUpload('main_image', '/public/uploads/');
            } else {
                // Giữ nguyên ảnh cũ (lấy từ input hidden: current_main_image)
                $new_image_name = $_POST['current_main_image'];
            }

            // 2. Lấy data
            $name = $_POST['product_name'];
            $price = $_POST['price'];
            $brand_id = $_POST['brand_id'];
            $category_id = $_POST['category_id'];
            $quantity = $_POST['quantity'];
            $description = $_POST['description'];
            $main_image = $new_image_name; // Sử dụng tên file đã được xử lý/giữ lại

            global $conn;
            $productModel = new Product($conn);
            
            // 3. Cập nhật
            if ($productModel->updateProduct($id, $name, $price, $brand_id, $category_id, $quantity, $description, $main_image)) {
                header("Location: " . BASE_URL . "index.php?controller=admin");
                exit;
            } else {
                die("Lỗi khi cập nhật sản phẩm.");
            }
        }
    }
    // Giữ nguyên hàm delete()
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
    
    // Các hàm khác giữ nguyên
    public function listOrders() {
        global $conn;
        $orderModel = new Order($conn);
        $orders = $orderModel->getAllOrders();
        
        // Tải view danh sách
        require_once ROOT_PATH . '/app/views/admin/order_list.php'; 
    }
    
    public function listBrands() {
        global $conn;
        $brandModel = new Brand($conn);
        $brands = $brandModel->getAllBrands();
        require_once ROOT_PATH . '/app/views/admin/brand_list.php'; 
    }

    /**
     * Action: Hiển thị form thêm Brand
     */
    public function createBrand() {
        require_once ROOT_PATH . '/app/views/admin/brand_form.php'; 
    }

    /**
     * CẬP NHẬT Action: Xử lý lưu Brand mới
     */
    public function storeBrand() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Xử lý upload logo trước
            $new_logo_name = $this->handleUpload('logo', '/public/uploads/');

            global $conn;
            $brandModel = new Brand($conn);
            
            // 2. Lưu vào CSDL (truyền tên logo đã được xử lý)
            $brandModel->createBrand($_POST['brand_name'], $_POST['description'], $new_logo_name);
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
        require_once ROOT_PATH . '/app/views/admin/brand_form.php'; 
    }

    /**
     * CẬP NHẬT Action: Xử lý cập nhật Brand
     */
    public function updateBrand() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['brand_id'];
            $new_logo_name = null;
            
            // 1. Kiểm tra xem có TẢI LOGO MỚI không
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $new_logo_name = $this->handleUpload('logo', '/public/uploads/');
                // (Sau này cần code để XÓA LOGO CŨ)
            } else {
                // Nếu không tải logo mới, giữ nguyên logo cũ
                // *LƯU Ý: Đảm bảo có input hidden tên 'current_logo' trong form*
                $new_logo_name = $_POST['current_logo']; 
            }

            global $conn;
            $brandModel = new Brand($conn);
            
            // 2. Cập nhật (truyền tên logo đã được xử lý/giữ lại)
            $brandModel->updateBrand($id, $_POST['brand_name'], $_POST['description'], $new_logo_name);
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
        require_once ROOT_PATH . '/app/views/admin/category_list.php'; 
    }

    /**
     * Action: Hiển thị form thêm Category
     */
    public function createCategory() {
        require_once ROOT_PATH . '/app/views/admin/category_form.php'; 
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
        require_once ROOT_PATH . '/app/views/admin/category_form.php'; 
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
    
    // *LƯU Ý QUAN TRỌNG: Các hàm store() và update() cũ đã được đổi tên thành storeProduct() và updateProduct().
    // Nếu Router của bạn vẫn mong đợi store/update, bạn cần tạo alias hoặc sửa Router.

    /**
     * HÀM MỚI: Xử lý Cập nhật Trạng thái Đơn hàng
     * URL: (Form POST tới) index.php?controller=admin&action=updateOrderStatus
     */
    public function updateOrderStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = (int)$_POST['order_id'];
            $new_status = $_POST['new_status'];
            
            // (Bạn nên kiểm tra xem $new_status có hợp lệ không,
            // ví dụ: 'pending', 'paid', 'shipped', 'completed', 'cancelled')
            
            global $conn;
            $orderModel = new Order($conn);
            
            if ($orderModel->updateOrderStatus($order_id, $new_status)) {
                // Cập nhật thành công, quay lại danh sách đơn hàng
                header("Location: " . BASE_URL . "index.php?controller=admin&action=listOrders");
                exit;
            } else {
                die("Lỗi khi cập nhật trạng thái đơn hàng.");
            }
        }
    }

    /**
     * HÀM MỚI (Người 1): Admin xem Chi tiết 1 Đơn hàng
     * URL: index.php?controller=admin&action=orderDetail&id=123
     */
    public function orderDetail() {
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($order_id <= 0) die("ID đơn hàng không hợp lệ.");

        global $conn;
        
        // Dùng Model Order (đã có)
        $orderModel = new Order($conn);
        
        // 1. Lấy thông tin chính của đơn hàng
        $order = $orderModel->getOrderByIdForAdmin($order_id); // Dùng hàm bạn vừa tạo
        
        if (!$order) {
            die("Không tìm thấy đơn hàng.");
        }
        
        // 2. Lấy chi tiết các sản phẩm trong đơn (dùng hàm đã có từ GĐ trước)
        $order_details = $orderModel->getOrderDetailsByOrderId($order_id);
        
        // 3. Tải View (truyền $order và $order_details)
        require_once ROOT_PATH . '/app/views/admin/order_detail.php'; // Sẽ tạo ở Bước 5
    }

     //  QUẢN LÝ USERS  
     // Action: Hiển thị danh sách Users
    public function listUsers() {
        global $conn;
        // Tải User Model 
        $userModel = new User($conn);
        $users = $userModel->getAllUsers();
        // Tải view
        require_once ROOT_PATH . '/app/views/admin/user_list.php'; 
    }

    
     // Action: Xử lý cập nhật vai trò User
    public function updateUserRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = (int)$_POST['user_id'];
            $role = $_POST['role'];
            // Ngăn admin tự thay đổi vai trò của chính mình
            if ($user_id === $_SESSION['user_id']) {
                 die("Không thể tự thay đổi vai trò của chính mình.");
            } 
            global $conn;
            $userModel = new User($conn);
            $userModel->updateUserRole($user_id, $role);
            // Cập nhật xong, quay lại danh sách
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listUsers");
            exit;
        }
    }
    
     // Action: Xử lý xóa User
    public function deleteUser() {
        $user_id = (int)$_GET['id'];
        //  Ngăn admin tự xóa chính mình
        if ($user_id === $_SESSION['user_id']) {
             die("Không thể tự xóa chính mình.");
        }
        global $conn;
        $userModel = new User($conn);
        $userModel->deleteUser($user_id);
        // Xóa xong, quay lại danh sách
        header("Location: " . BASE_URL . "index.php?controller=admin&action=listUsers");
        exit;
    }
    
}
?>