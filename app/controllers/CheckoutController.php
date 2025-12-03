<?php
// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Product.php';
require_once ROOT_PATH . '/app/models/Order.php';     
require_once ROOT_PATH . '/app/models/OrderDetail.php'; 
require_once ROOT_PATH . '/app/models/Coupon.php'; // <== THÊM DÒNG NÀY
// Tải file functions (để dùng set_flash_message)
require_once ROOT_PATH . '/config/functions.php';
require_once ROOT_PATH . '/app/services/ZaloPayService.php';

class CheckoutController {

    /**
     * Action: Hiển thị trang Checkout
     */
   public function index() {
        global $conn;
        
        // --- Code bảo vệ ---
        if (!isset($_SESSION['user_id'])) {
            set_flash_message("Bạn phải đăng nhập để thanh toán.", 'error');
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            set_flash_message("Giỏ hàng của bạn rỗng.", 'info'); 
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
        // --- Hết code bảo vệ ---

        // 1. Lấy thông tin User
        $userModel = new User($conn);
        $user = $userModel->getUserById($_SESSION['user_id']);
        
        // 2. Lấy Tóm tắt Giỏ hàng
        $productModel = new Product($conn);
        $cart_items = [];
        $total_price = 0; 
        
        foreach ($cart as $product_id => $quantity) {
            $product = $productModel->getProductById($product_id);
            if ($product) {
                $product['quantity_in_cart'] = $quantity;
                $cart_items[] = $product;
                $total_price += $product['price'] * $quantity;
            }
        }
        
        // 3. Logic Coupon (Lấy từ Cart Session)
        $coupon_code = $_SESSION['cart_coupon']['code'] ?? null;
        $discount_amount = $_SESSION['cart_coupon']['discount'] ?? 0;
        
        if ($discount_amount > $total_price) {
            $discount_amount = $total_price;
        }
        $final_price = $total_price - $discount_amount;
        
        // 4. LOGIC ĐỊA CHỈ MỚI (Để tự động điền Dropdown)
        if (!class_exists('Address')) {
            require_once ROOT_PATH . '/app/models/Address.php';
        }
        $addressModel = new Address($conn);
        
        // Luôn lấy Tỉnh/Thành
        $provinces = $addressModel->getProvinces();
        
        // Nếu user đã lưu Tỉnh -> Lấy danh sách Quận của Tỉnh đó
        $districts = [];
        if (!empty($user['province_id'])) {
            $districts = $addressModel->getDistrictsByProvince($user['province_id']);
        }
        
        // Nếu user đã lưu Quận -> Lấy danh sách Phường của Quận đó
        $wards = [];
        if (!empty($user['district_id'])) {
            $wards = $addressModel->getWardsByDistrict($user['district_id']);
        }

        // 5. Tải View (Truyền thêm $provinces, $districts, $wards)
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/checkout/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }
    
    /**
     * Action: Xử lý Đặt hàng
     */
    public function placeOrder() {
        global $conn;

        // --- Code bảo vệ (đã có) ---
        if (!isset($_SESSION['user_id'])) {
            set_flash_message("Bạn phải đăng nhập để thanh toán.", 'error'); 
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            set_flash_message("Giỏ hàng của bạn rỗng. Vui lòng thêm sản phẩm.", 'info'); 
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
        
        // 1. Lấy thông tin từ form
        $user_id = $_SESSION['user_id'];
        $shipping_address = $_POST['address']; 
        $shipping_phone = $_POST['phone'];
        $notes = $_POST['notes'];
        $payment_method = $_POST['payment_method'];
        
        $shipping_district_id = (int)$_POST['district_id'];
        $shipping_ward_code = $_POST['ward_code'];

        // 2. Tính toán lại Tổng tiền (gốc) và kiểm tra tồn kho
        $productModel = new Product($conn);
        $total_price_original = 0; // Tổng tiền GỐC (chưa giảm)
        $products_in_cart = [];
        
        foreach ($cart as $product_id => $quantity) {
            $product = $productModel->getProductById($product_id);
            if ($product) {
                if ($product['quantity'] < $quantity) {
                    set_flash_message("Lỗi: Sản phẩm '" . htmlspecialchars($product['product_name']) . "' chỉ còn " . $product['quantity'] . " cái.", 'error');
                    header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
                    exit;
                }
                $total_price_original += $product['price'] * $quantity;
                $products_in_cart[] = [
                    'id' => $product_id,
                    'name' => $product['product_name'],
                    'quantity' => $quantity,
                    'unit_price' => $product['price']
                ];
            }
        }

        // --- Lấy thông tin coupon từ Session ---
        $coupon_code = $_SESSION['cart_coupon']['code'] ?? null;
        $discount_amount = $_SESSION['cart_coupon']['discount'] ?? 0;
        
        if ($discount_amount > $total_price_original) {
            $discount_amount = $total_price_original;
        }
        // --- End Coupon ---

        // BẮT ĐẦU TRANSACTION
        $conn->begin_transaction();
        try {
            // 3. Tạo Đơn hàng (Bảng 'orders')
            $orderModel = new Order($conn);
            
            // --- GỌI HÀM CREATE ORDER (10 tham số) ---
            $order_id = $orderModel->createOrder(
                $user_id, 
                $total_price_original, // Truyền tổng tiền GỐC
                $shipping_address, 
                $shipping_phone, 
                $shipping_district_id, 
                $shipping_ward_code, 
                $notes, 
                $payment_method,
                $coupon_code,
                $discount_amount // Truyền số tiền giảm
            );
            
            if (!$order_id) throw new Exception("Không thể tạo đơn hàng.");

            // 4. Tạo Chi tiết Đơn hàng VÀ Trừ tồn kho
            $orderDetailModel = new OrderDetail($conn);
            foreach ($products_in_cart as $item) {
                $orderDetailModel->createDetail($order_id, $item['id'], $item['quantity'], $item['unit_price']);
                $rows_affected = $productModel->decrementStock($item['id'], $item['quantity']);
                if ($rows_affected <= 0) {
                    throw new Exception("Sản phẩm '" . htmlspecialchars($item['name']) . "' đã hết hàng trong quá trình bạn đặt.");
                }
            }
            
            // 5. GHI NHẬN MÃ GIẢM GIÁ ĐÃ SỬ DỤNG (BẢNG MỚI)
            if ($coupon_code && $discount_amount > 0) {
                $couponModel = new Coupon($conn);
                $coupon = $couponModel->getCouponByCode($coupon_code);
                if ($coupon) {
                    // Ghi nhận vào bảng coupon_usage và tăng usage_count
                    if (!$couponModel->recordUsage($coupon['coupon_id'], $user_id, $order_id)) {
                        throw new Exception("Lỗi khi ghi nhận mã giảm giá đã sử dụng.");
                    }
                }
            }
            
            // 6. Nếu mọi thứ OK -> Commit
            $conn->commit();
            
            // 7. Xóa giỏ hàng VÀ XÓA COUPON
            unset($_SESSION['cart']);
            unset($_SESSION['cart_coupon']);
            unset($_SESSION['coupon_code']); // Xóa cả cái này nữa

            // === LOGIC THANH TOÁN ZALOPAY ===
            if ($payment_method == 'zalopay') {
                $zaloPayService = new ZaloPayService();
                $userModel = new User($conn); 
                
                // Lấy thông tin đơn hàng vừa tạo để gửi sang Zalo
                $orderForZalo = [
                    'order_id' => $order_id,
                    'total_amount' => $total_price_original - $discount_amount, // Tổng tiền cuối cùng
                    'full_name' => $userModel->getUserById($user_id)['full_name'] 
                ];

                $zaloResult = $zaloPayService->createPayment($orderForZalo);

                if ($zaloResult && isset($zaloResult['order_url'])) {
                    // Chuyển hướng sang trang thanh toán ZaloPay
                    header("Location: " . $zaloResult['order_url']);
                    exit;
                } else {
                    set_flash_message("Lỗi tạo cổng thanh toán ZaloPay. Đơn hàng đã được tạo với trạng thái 'Chờ thanh toán'.", 'error');
                    header("Location: " . BASE_URL . "index.php?controller=checkout&action=success&order_id=" . $order_id);
                    exit;
                }
            }
            
            // 8. Chuyển đến trang Cảm ơn (cho COD)
            set_flash_message("Đặt hàng thành công! Cảm ơn bạn đã mua hàng.", 'success');
            header("Location: " . BASE_URL . "index.php?controller=checkout&action=success&order_id=" . $order_id);
            exit;

        } catch (Exception $e) {
            // 9. Nếu có lỗi -> Rollback
            $conn->rollback();
            set_flash_message("Đặt hàng thất bại: " . $e->getMessage(), 'error');
            header("Location: " . BASE_URL . "index.php?controller=cart&action=index");
            exit;
        }
    }
    
    // ... (Giữ nguyên các hàm success, paymentResult) ...
    public function success() {
        $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/checkout/success.php'; 
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    } 

    public function paymentResult() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }

        $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        
        global $conn;
        $orderModel = new Order($conn);
        
        $order = $orderModel->getOrderByIdAndUserId($order_id, $_SESSION['user_id']);
        
        if (!$order) {
            set_flash_message("Không tìm thấy đơn hàng.", 'error');
            header("Location: " . BASE_URL . "index.php?controller=account&action=history");
            exit;
        }

        if ($order['order_status'] == 'paid') {
            set_flash_message("Thanh toán ZaloPay thành công!", 'success');
            header("Location: " . BASE_URL . "index.php?controller=checkout&action=success&order_id=" . $order_id);
            exit;
        } else {
            require_once ROOT_PATH . '/app/views/layouts/header.php';
            require_once ROOT_PATH . '/app/views/checkout/payment_result.php'; 
            require_once ROOT_PATH . '/app/views/layouts/footer.php';
        }
    }
}