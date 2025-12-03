<?php
// 1. Tải các file Model
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Brand.php';
require_once ROOT_PATH . '/app/models/Category.php';
require_once ROOT_PATH . '/app/models/Order.php';
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/ProductImage.php'; 
require_once ROOT_PATH . '/app/models/Review.php';
require_once ROOT_PATH . '/app/models/Coupon.php'; 
require_once ROOT_PATH . '/app/services/ShippingService.php';
require_once ROOT_PATH . '/config/functions.php';
require_once ROOT_PATH . '/app/models/Contact.php'; // <== ĐÃ THÊM

class AdminController {

    /**
     * HÀM HELPER: Xử lý upload file
     */
    private function handleUpload($file_input_name, $upload_dir) {
        if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
            $file_tmp_path = $_FILES[$file_input_name]['tmp_name'];
            $file_name = basename($_FILES[$file_input_name]['name']);
            $file_type = $_FILES[$file_input_name]['type'];
            $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']; // Mở rộng thêm WEBP
            if (!in_array($file_type, $allowed_mime_types)) {
                return null; 
            }
            $new_file_name = uniqid() . '-' . $file_name;
            $dest_path = ROOT_PATH . $upload_dir . $new_file_name;
            
            // Kiểm tra và tạo thư mục nếu chưa có (để tránh lỗi di chuyển file)
            $absolute_upload_dir = ROOT_PATH . $upload_dir;
            if (!is_dir($absolute_upload_dir)) {
                 @mkdir($absolute_upload_dir, 0777, true);
            }

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
     * HÀM INDEX (Dashboard)
     */
    public function index() {
        global $conn;
        
        $orderModel = new Order($conn);
        $order_stats = $orderModel->getOrderStats();
        
        $userModel = new User($conn);
        $new_users = $userModel->countNewUsers();
        
        // LẤY SỐ LƯỢNG LIÊN HỆ MỚI
        $contactModel = new Contact($conn); 
        $new_contacts = $contactModel->countNewContacts(); 
        
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
 
    // ------------------------------------------------------------
    // QUẢN LÝ SẢN PHẨM
    // ------------------------------------------------------------
    
    /**
     * HÀM listProducts
     */
    public function listProducts() {
        global $conn; 
        $productModel = new Product($conn);

        $products_per_page = 6; 
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) $current_page = 1;
        $total_products = $productModel->countAllProducts();
        $total_pages = ceil($total_products / $products_per_page);
        if ($current_page > $total_pages && $total_products > 0) $current_page = $total_pages;
        $offset = ($current_page - 1) * $products_per_page;
        $products = $productModel->getAllProducts($products_per_page, $offset);

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/product_list.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM create
     */
    public function create() {
        global $conn;
        
        $brandModel = new Brand($conn);
        $brands = $brandModel->getAllBrands();
        
        $categoryModel = new Category($conn);
        $categories = $categoryModel->getAllCategories();
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/product_form.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM store
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
     * HÀM edit
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
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/product_form.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM update
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['product_id'];
            if ($id <= 0) die("ID không hợp lệ.");
            
            $return_url = $_POST['return_url'] ?? (BASE_URL . 'index.php?controller=admin&action=listProducts');
            if (strpos($return_url, 'controller=admin') === false) {
                 $return_url = BASE_URL . 'index.php?controller=admin&action=listProducts';
            }

            $new_image_name = $_POST['current_main_image']; 
            
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $new_image_name = $this->handleUpload('main_image', '/public/uploads/');
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
                
                header("Location: " . $return_url);
                exit;
                
            } else {
                die("Lỗi khi cập nhật sản phẩm.");
            }
        }
    }
 
    /**
     * HÀM delete
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
    
    // ------------------------------------------------------------
    // QUẢN LÝ ĐƠN HÀNG
    // ------------------------------------------------------------

    /**
     * HÀM listOrders
     */
    public function listOrders() {
        global $conn;
        $orderModel = new Order($conn);
        $orders = $orderModel->getAllOrders();
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/order_list.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * HÀM updateOrderStatus
     */
    public function updateOrderStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = (int)$_POST['order_id'];
            $new_status = $_POST['new_status'];
            
            global $conn;
            $orderModel = new Order($conn);

            // LOGIC: Tự động gọi API khi chuyển sang "shipped"
            if ($new_status == 'shipped') {
                
                $order = $orderModel->getOrderByIdForAdmin($order_id);
                $order_details = $orderModel->getOrderDetailsByOrderId($order_id);
                
                if ($order && !empty($order_details)) {
                    
                    $shippingService = new ShippingService();
                    
                    $tracking_code = $shippingService->createShipment($order, $order_details);
                    
                    if ($tracking_code) {
                        $provider = 'GHN'; 
                        $orderModel->updateTrackingInfo($order_id, $provider, $tracking_code);
                        
                        set_flash_message("Tạo vận đơn API thành công! Mã: $tracking_code", 'success');
                        
                    } else {
                        set_flash_message("LỖI API: Không thể tạo đơn vận chuyển. Vui lòng kiểm tra lại (địa chỉ, SĐT) hoặc thử lại sau.", 'error');
                        header("Location: " . BASE_URL . "index.php?controller=admin&action=orderDetail&id=" . $order_id);
                        exit;
                    }
                }
            }

            // Cập nhật Status
            $orderModel->updateOrderStatus($order_id, $new_status);
            
            if (!isset($_SESSION['flash_message'])) {
                 set_flash_message("Cập nhật trạng thái đơn hàng thành công.", 'success');
            }
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listOrders");
            exit;
        }
    }

    /**
     * HÀM orderDetail
     */
    public function orderDetail() {
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($order_id <= 0) die("ID đơn hàng không hợp lệ.");
        global $conn;
        
        $orderModel = new Order($conn);
        $order = $orderModel->getOrderByIdForAdmin($order_id); 
        if (!$order) die("Không tìm thấy đơn hàng.");
        
        $order_details = $orderModel->getOrderDetailsByOrderId($order_id);
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/order_detail.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    // ------------------------------------------------------------
    // QUẢN LÝ THƯƠNG HIỆU & DANH MỤC
    // ------------------------------------------------------------

    /**
     * HÀM listBrands
     */
    public function listBrands() {
        global $conn;
        $brandModel = new Brand($conn);
        $brands = $brandModel->getAllBrands();
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/brand_list.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    public function createBrand() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/brand_form.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

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

    public function editBrand() {
        $id = (int)$_GET['id'];
        global $conn;
        $brandModel = new Brand($conn);
        $brand = $brandModel->getBrandById($id);
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/brand_form.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

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
    
    public function deleteBrand() {
        $id = (int)$_GET['id'];
        global $conn;
        $brandModel = new Brand($conn);
        $brandModel->deleteBrand($id);
        header("Location: " . BASE_URL . "index.php?controller=admin&action=listBrands");
        exit;
    }
    
    /**
     * HÀM listCategories
     */
    public function listCategories() {
        global $conn;
        $categoryModel = new Category($conn);
        $categories = $categoryModel->getAllCategories();
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/category_list.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    public function createCategory() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/category_form.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    public function storeCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $categoryModel = new Category($conn);
            $categoryModel->createCategory($_POST['category_name'], $_POST['description']);
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listCategories");
            exit;
        }
    }

    public function editCategory() {
        $id = (int)$_GET['id'];
        global $conn;
        $categoryModel = new Category($conn);
        $category = $categoryModel->getCategoryById($id);
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/category_form.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

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
    
    public function deleteCategory() {
        $id = (int)$_GET['id'];
        global $conn;
        $categoryModel = new Category($conn);
        $categoryModel->deleteCategory($id);
        header("Location: " . BASE_URL . "index.php?controller=admin&action=listCategories");
        exit;
    }
    
    // ------------------------------------------------------------
    // QUẢN LÝ NGƯỜI DÙNG & ĐÁNH GIÁ
    // ------------------------------------------------------------

    /**
     * HÀM listUsers
     */
    public function listUsers() {
        global $conn;
        $userModel = new User($conn);
        $users = $userModel->getAllUsers();
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/user_list.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM updateUserRole
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
     * HÀM deleteUser
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
     * HÀM editUser
     */
    public function editUser() {
        $user_id = (int)$_GET['id'];
        if ($user_id <= 0) die("ID không hợp lệ.");
        
        global $conn;
        $userModel = new User($conn);
        $user = $userModel->getUserById($user_id); 
        
        if (!$user) die("Không tìm thấy user.");

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/user_form.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * HÀM updateUser
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

            global $conn;
            $userModel = new User($conn);
            $userModel->adminUpdateUser($user_id, $full_name, $email, $phone, $address, $province, $role);
            
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listUsers");
            exit;
        }
    }
    
    /**
     * HÀM listReviews
     */
    public function listReviews() {
        global $conn;
        $reviewModel = new Review($conn);
        $reviews = $reviewModel->getAllReviews();
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/review_list.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * HÀM deleteReview
     */
    public function deleteReview() {
        $review_id = (int)$_GET['id'];
        global $conn;
        $reviewModel = new Review($conn);
        $reviewModel->deleteReview($review_id);
        header("Location: " . BASE_URL . "index.php?controller=admin&action=listReviews");
        exit;
    }

    // ------------------------------------------------------------
    // QUẢN LÝ HÌNH ẢNH SẢN PHẨM
    // ------------------------------------------------------------
    
    public function manageImages() {
        $product_id = (int)$_GET['product_id'];
        if ($product_id <= 0) die("ID sản phẩm không hợp lệ.");

        $return_url = $_GET['return_url'] ?? (BASE_URL . 'index.php?controller=admin&action=listProducts');
        if (strpos($return_url, 'action=listProducts') === false) {
             $return_url = BASE_URL . 'index.php?controller=admin&action=listProducts';
        }

        global $conn;
        $productModel = new Product($conn);
        $product = $productModel->getProductById($product_id);
        
        $imageModel = new ProductImage($conn);
        $images = $imageModel->getImagesByProductId($product_id);
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/product_images.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    public function uploadImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = (int)$_POST['product_id'];
            
            $return_url = $_POST['return_url'] ?? (BASE_URL . 'index.php?controller=admin&action=listProducts');
            
            $new_image_name = $this->handleUpload('product_image_file', '/public/uploads/');
            
            if ($product_id > 0 && $new_image_name) {
                global $conn;
                $imageModel = new ProductImage($conn);
                $imageModel->addImage($product_id, $new_image_name);
            }
            
            $redirect_to = BASE_URL . "index.php?controller=admin&action=manageImages&product_id=" . $product_id . "&return_url=" . urlencode($return_url);
            header("Location: " . $redirect_to);
            exit;
        }
    }
    
    public function deleteImage() {
        $image_id = (int)$_GET['image_id'];
        $product_id = (int)$_GET['product_id']; 
        
        $return_url = $_GET['return_url'] ?? (BASE_URL . 'index.php?controller=admin&action=listProducts');

        global $conn;
        $imageModel = new ProductImage($conn);
        
        $image = $imageModel->getImageById($image_id);
        if ($image) {
            $file_path = ROOT_PATH . '/public/uploads/' . $image['image_url'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $imageModel->deleteImage($image_id);
        }
        
        $redirect_to = BASE_URL . "index.php?controller=admin&action=manageImages&product_id=" . $product_id . "&return_url=" . urlencode($return_url);
        header("Location: " . $redirect_to);
        exit;
    }

    // ------------------------------------------------------------
    // QUẢN LÝ MÃ GIẢM GIÁ (COUPON)
    // ------------------------------------------------------------
    
    /**
     * HÀM listCoupons: Hiển thị danh sách Mã giảm giá
     */
    public function listCoupons() {
        global $conn;
        // Cần tải Coupon Model ngay trước khi dùng
        require_once ROOT_PATH . '/app/models/Coupon.php';
        $couponModel = new Coupon($conn);
        $coupons = $couponModel->getAllCoupons();

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/coupon_list.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM createCoupon: Hiển thị form tạo Mã giảm giá
     */
    public function createCoupon() {
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/coupon_form.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM storeCoupon: Xử lý tạo Mã giảm giá
     */
    public function storeCoupon() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            require_once ROOT_PATH . '/app/models/Coupon.php';
            $couponModel = new Coupon($conn);
            
            $expires = $_POST['expires_at'] ? date('Y-m-d H:i:s', strtotime($_POST['expires_at'])) : NULL;
            $is_public = isset($_POST['is_public']) ? 1 : 0;
            $max_usage = (int)$_POST['max_usage'];
            // Đảm bảo mã được lưu bằng chữ hoa
            $code = trim(strtoupper($_POST['coupon_code']));

            if ($couponModel->createCoupon(
                $code, 
                $_POST['discount_type'], 
                $_POST['discount_value'], 
                $expires, 
                $max_usage,
                $is_public
            )) {
                set_flash_message("Tạo mã giảm giá **" . htmlspecialchars($code) . "** thành công.", 'success');
            } else {
                set_flash_message("Lỗi khi tạo mã giảm giá. Mã **" . htmlspecialchars($code) . "** có thể đã tồn tại.", 'error');
            }
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listCoupons");
            exit;
        }
    }
    
    /**
     * HÀM deleteCoupon: Xử lý xóa Mã giảm giá
     */
    public function deleteCoupon() {
        $id = (int)$_GET['id'];
        global $conn;
        require_once ROOT_PATH . '/app/models/Coupon.php';
        $couponModel = new Coupon($conn);
        
        $couponModel->deleteCoupon($id);
        set_flash_message("Xóa mã giảm giá thành công.", 'success');
        header("Location: " . BASE_URL . "index.php?controller=admin&action=listCoupons");
        exit;
    }
    
    // ------------------------------------------------------------
    // QUẢN LÝ LIÊN HỆ (CONTACTS) <== PHẦN MỚI
    // ------------------------------------------------------------

    /**
     * HÀM listContacts: Hiển thị danh sách Liên hệ
     */
    public function listContacts() {
        global $conn;
        $contactModel = new Contact($conn);
        $contacts = $contactModel->getAllContacts();

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/admin/contact_list.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * HÀM updateContactStatus: Cập nhật trạng thái Liên hệ
     */
    public function updateContactStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contact_id = (int)$_POST['contact_id'];
            $new_status = $_POST['new_status'];
            
            global $conn;
            $contactModel = new Contact($conn);
            
            if ($contactModel->updateStatus($contact_id, $new_status)) {
                set_flash_message("Cập nhật trạng thái liên hệ #$contact_id thành công.", 'success');
            } else {
                set_flash_message("Lỗi khi cập nhật trạng thái liên hệ.", 'error');
            }

            // Chuyển hướng về trang danh sách
            header("Location: " . BASE_URL . "index.php?controller=admin&action=listContacts");
            exit;
        }
    }
}