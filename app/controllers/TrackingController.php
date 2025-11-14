<?php
// Tải các file config và service cần thiết
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/app/services/ShippingService.php';
require_once ROOT_PATH . '/app/models/Order.php'; // (Cần để kiểm tra bảo mật)

/**
 * Controller MỚI (GĐ 23) - Xử lý các yêu cầu tra cứu vận đơn (AJAX)
 */
class TrackingController {

    /**
     * Action: Lấy thông tin chi tiết vận đơn từ API (GHN)
     * URL: index.php?controller=tracking&action=getOrderStatus&order_code=...
     */
    /**
     * Action: Lấy thông tin chi tiết vận đơn từ API (GHN)
     * URL: index.php?controller=tracking&action=getOrderStatus&order_code=...
     * (ĐÃ SỬA LỖI: Thêm '?? null' để tránh Warning)
     */
    public function getOrderStatus() {
        global $conn;
        
        $order_code = trim($_GET['order_code'] ?? '');
        
        // --- BẢO MẬT (Rất quan trọng) ---
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401); // 401 Unauthorized
            echo json_encode(['success' => false, 'message' => 'Bạn phải đăng nhập.']);
            exit;
        }
        $user_id = $_SESSION['user_id'];

        $orderModel = new Order($conn);
        $order = $orderModel->getOrderByIdAndUserId(null, $user_id, $order_code); 
        
        if (!$order && $_SESSION['user_role'] !== 'admin') {
            header('Content-Type: application/json');
            http_response_code(403); // 403 Forbidden
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền xem đơn hàng này.']);
            exit;
        }
        // --- KẾT THÚC BẢO MẬT ---


        // 3. Gọi ShippingService
        $shippingService = new ShippingService();
        $order_info = $shippingService->getOrderInfo($order_code);

        // 4. Trả về kết quả (JSON)
        header('Content-Type: application/json');
        if ($order_info) {
            
            // =========================================================
            // SỬA LỖI Ở ĐÂY (Dòng 57):
            // Dùng '?? null' (Null Coalescing Operator)
            // (Nghĩa là: dùng $order_info['log'] NẾU NÓ TỒN TẠI,
            // nếu không, dùng null)
            // =========================================================
            echo json_encode([
                'success' => true,
                'status' => $order_info['status'] ?? 'unknown',
                'log' => $order_info['log'] ?? null // <-- ĐÃ SỬA
            ]);
            
        } else {
            http_response_code(404); // 404 Not Found
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin vận đơn trên hệ thống (hoặc API Sandbox lỗi).']);
        }
        exit;
    }
}
?>