<?php
class ZaloPayService {
    
    // Cấu hình Sandbox (Lấy từ tài liệu ZaloPay)
    private $config = [
        "app_id" => 2553,
        "key1" => "PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL",
        "key2" => "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz",
        "endpoint" => "https://sb-openapi.zalopay.vn/v2/create"
    ];

    /**
     * Tạo đơn hàng ZaloPay
     * @param array $order Thông tin đơn hàng
     * @return array|null Trả về mảng chứa order_url (link thanh toán) nếu thành công
     */
    public function createPayment($order) {
        // SỬA LỖI: Chuyển về trang Kết quả để kiểm tra trạng thái trước
        $embed_data = json_encode(['redirecturl' => BASE_URL . 'index.php?controller=checkout&action=paymentResult&order_id=' . $order['order_id']]);
        $items = json_encode([]); // (Có thể gửi chi tiết items nếu muốn)
        
        // Mã đơn hàng của ZaloPay phải là duy nhất và theo format: yymmdd_appid_orderid
        // Ví dụ: 231118_2553_123456
        $transID = rand(0,1000000); // Random để tránh trùng lặp khi test
        $app_trans_id = date("ymd") . "_" . $transID;

        $order_data = [
            "app_id" => $this->config["app_id"],
            "app_user" => $order['full_name'] ?? "KhachHang",
            "app_time" => round(microtime(true) * 1000), // miliseconds
            "amount" => (int)$order['total_amount'],
            "app_trans_id" => $app_trans_id,
            "embed_data" => $embed_data,
            "item" => $items,
            "description" => "Thanh toan don hang #" . $order['order_id'],
            "bank_code" => "zalopayapp", // Hoặc để trống để user chọn
        ];

        // Tạo chữ ký (MAC) để bảo mật
        // Công thức: app_id|app_trans_id|app_user|amount|app_time|embed_data|item
        $data_string = $order_data["app_id"] . "|" . $order_data["app_trans_id"] . "|" . $order_data["app_user"] . "|" . $order_data["amount"]
            . "|" . $order_data["app_time"] . "|" . $order_data["embed_data"] . "|" . $order_data["item"];
        
        $order_data["mac"] = hash_hmac("sha256", $data_string, $this->config["key1"]);

        // Gọi API
        $context = stream_context_create([
            "http" => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => http_build_query($order_data)
            ]
        ]);

        $response = file_get_contents($this->config["endpoint"], false, $context);
        $result = json_decode($response, true);

        if ($result['return_code'] == 1) {
            // Thành công!
            // Lưu app_trans_id vào $result để sau này dùng
            $result['app_trans_id'] = $app_trans_id; 
            return $result;
        } else {
            // Ghi log lỗi
            error_log("ZaloPay Error: " . print_r($result, true));
            return null;
        }
    }
}
?>