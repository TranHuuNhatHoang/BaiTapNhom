<?php
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/app/models/Order.php';

class ZaloPayCallbackController {
    
    // Key2 của Sandbox (Dùng để xác thực dữ liệu nhận về có phải chính chủ ZaloPay không)
    private $key2 = "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz";

    public function index() {
        $result = [];

        try {
            // 1. Nhận dữ liệu JSON từ ZaloPay gửi sang
            $postdata = file_get_contents('php://input');
            $postdatajson = json_decode($postdata, true);
            
            // Kiểm tra xem có dữ liệu không
            if (!isset($postdatajson['data']) || !isset($postdatajson['mac'])) {
                 // Nếu không có dữ liệu, đây có thể là truy cập trực tiếp từ trình duyệt
                 throw new Exception("Dữ liệu không hợp lệ");
            }

            $mac = $postdatajson["mac"];
            $dataStr = $postdatajson["data"];
            
            // 2. Kiểm tra chữ ký (MAC) để đảm bảo an toàn
            $reqMac = hash_hmac("sha256", $dataStr, $this->key2);

            if ($reqMac !== $mac) {
                // Chữ ký không khớp -> Giả mạo
                $result["return_code"] = -1;
                $result["return_message"] = "mac not equal";
            } else {
                // 3. Chữ ký hợp lệ -> Xử lý đơn hàng
                $dataInfo = json_decode($dataStr, true);
                
                // Phân tích description để lấy Order ID
                // (Lúc tạo đơn mình ghi: "Thanh toan don hang #101")
                $description = $dataInfo['description']; 
                preg_match('/#(\d+)/', $description, $matches);
                
                if (isset($matches[1])) {
                    $order_id = $matches[1];
                    $zp_trans_id = $dataInfo['zp_trans_id']; // Mã giao dịch của ZaloPay
                    
                    // 4. Gọi Model để cập nhật CSDL
                    global $conn;
                    $orderModel = new Order($conn);
                    $orderModel->updatePaymentStatus($order_id, $zp_trans_id, 'paid');
                    
                    $result["return_code"] = 1;
                    $result["return_message"] = "success";
                } else {
                    $result["return_code"] = 0;
                    $result["return_message"] = "Order ID not found in description";
                }
            }
        } catch (Exception $e) {
            $result["return_code"] = 0; 
            $result["return_message"] = $e->getMessage();
        }

        // Trả về JSON cho ZaloPay
        header('Content-Type: application/json');
        echo json_encode($result);
        exit; // Quan trọng: Dừng script tại đây
    }
}
?>