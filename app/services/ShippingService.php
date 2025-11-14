<?php
/**
 * File này chứa logic gọi API của Giao Hàng Nhanh (GHN).
 * Đây là phiên bản dùng MÔI TRƯỜNG THỬ NGHIỆM (SANDBOX).
 *
 * (ĐÃ CẬP NHẬT GIAI ĐOẠN 23: Thêm hàm getOrderInfo)
 */

class ShippingService {
    
    // ============================================================
    // (Giữ Key Sandbox CÁ NHÂN của bạn)
    // ============================================================
    private $api_key = "9e958cee-c146-11f0-a621-f2a9392e54c8"; // (Token của bạn)
    private $shop_id = "198125"; // (Shop ID của bạn)
    
    // (URL Sandbox để TẠO ĐƠN HÀNG)
    private $api_url = "https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/create";
    
    // (URL Sandbox để LẤY DỮ LIỆU)
    private $master_data_url = "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/";
    
    // ============================================================
    // THÊM MỚI (BƯỚC 1 - GĐ23): URL API LẤY CHI TIẾT ĐƠN HÀNG
    // ============================================================
    private $api_url_detail = "https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail";


    /**
     * HÀM HELPER MỚI (BƯỚC 1): Gọi API cURL (Dùng cho GET)
     */
    private function callGetApi($url) {
        $headers = [
            "Token: " . $this->api_key,
            "Content-Type: application/json"
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false, // Bỏ qua SSL (cho Sandbox)
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        return $data['data'] ?? []; // Trả về mảng data
    }
    
    /**
     * HÀM HELPER MỚI (BƯỚC 1): Gọi API cURL (Dùng cho POST)
     */
    private function callPostApi($url, $data, $headers) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response_json = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Ghi log (để debug nếu cần)
        if ($http_code != 200) {
            $error_log = "GHN API Error (HTTP Code: $http_code)\n";
            $error_log .= "URL: $url\n";
            $error_log .= "Response (Phản hồi từ GHN): \n$response_json\n";
            $error_log .= "Data Sent (Dữ liệu đã gửi đi): \n" . json_encode($data) . "\n\n";
            file_put_contents('ghn_error.log', $error_log, FILE_APPEND);
        }
        
        return json_decode($response_json, true); // Trả về toàn bộ response
    }

    // (Các hàm getProvinces, getDistricts, getWards giữ nguyên)
    public function getProvinces() {
        $url = $this->master_data_url . "province";
        return $this->callGetApi($url);
    }
    public function getDistricts($province_id) {
        $url = $this->master_data_url . "district?province_id=" . $province_id;
        return $this->callGetApi($url);
    }
    public function getWards($district_id) {
        $url = $this->master_data_url . "ward?district_id=" . $district_id;
        return $this->callGetApi($url);
    }


    /**
     * Gọi API GHN để tạo đơn (HÀM CŨ - GIỮ NGUYÊN)
     * (Hàm này dùng Key CÁ NHÂN và Data TEST CÔNG KHAI)
     */
    public function createShipment($order, $order_details) {
        
        // 1. Chuẩn bị mảng 'items' (sản phẩm) từ $order_details
        $items_array = [];
        foreach ($order_details as $item) {
            $items_array[] = [
                "name" => $item['product_name'],
                "quantity" => (int)$item['quantity'],
                "price" => (int)$item['unit_price']
            ];
        }

        // 2. Xây dựng chuỗi 'to_address'
        $to_address_full = $order['shipping_address'] . ", " . 
                           $order['ward_name'] . ", " . 
                           $order['district_name'] . ", " . 
                           $order['province_name'] . ", Vietnam";

        // 3. Đặt 'insurance_value' mặc định
        $insurance_value = 2000000; // 2 triệu VND

        // 4. Chuẩn bị dữ liệu gửi đi
        $data = [
            "payment_type_id" => 2,
            "note" => $order['notes'] ?? "Tintest 123 (Testing from BaiTapNhom)",
            "required_note" => "KHONGCHOXEMHANG",
            
            // DÙNG DATA "TO" (ĐẾN) THẬT TỪ ĐƠN HÀNG
            "to_name" => $order['full_name'],
            "to_phone" => $order['shipping_phone'],
            "to_address" => $to_address_full, // (Chuỗi đầy đủ)
            "to_ward_code" => $order['shipping_ward_code'], // (Mã Phường THẬT)
            "to_district_id" => (int)$order['shipping_district_id'], // (Mã Quận THẬT)

            // Thông tin gói hàng
            "cod_amount" => (int)$order['total_amount'], 
            "weight" => 2000, 
            "length" => 30,
            "width" => 20,
            "height" => 10,
            "insurance_value" => $insurance_value, 
            "service_type_id" => 2, 
            
            "items" => $items_array
        ];

        // Headers (Token + ShopID CÁ NHÂN)
        $headers = [
            "Content-Type: application/json",
            "ShopId: " . $this->shop_id,
            "Token: " . $this->api_key
        ];
        
        // CẬP NHẬT: Dùng hàm helper callPostApi
        $response_data = $this->callPostApi($this->api_url, $data, $headers);

        // Xử lý kết quả
        if (isset($response_data['code']) && $response_data['code'] == 200) { 
            if (isset($response_data['data']['order_code'])) {
                return $response_data['data']['order_code']; // THÀNH CÔNG!
            }
        }
        
        return null;
    }
    
    // ============================================================
    // HÀM MỚI (BƯỚC 1 - GĐ23): Lấy Thông tin Chi tiết Đơn hàng
    // ============================================================
    /**
     * Gọi API GHN để lấy chi tiết 1 đơn hàng (dùng order_code)
     * @param string $order_code (Mã vận đơn, vd: "L4MR8T")
     * @return array|null Trả về mảng data (chứa 'status', 'log'...) nếu thành công
     */
    // ============================================================
    // HÀM (GĐ 23): Lấy Thông tin Chi tiết Đơn hàng
    // (ĐÃ CẬP NHẬT: Thêm Ghi Log (Debug) khi logic thất bại)
    // ============================================================
    /**
     * Gọi API GHN để lấy chi tiết 1 đơn hàng (dùng order_code)
     * @param string $order_code (Mã vận đơn, vd: "L4MR8T")
     * @return array|null Trả về mảng data (chứa 'status', 'log'...) nếu thành công
     */
    // ============================================================
    // HÀM (GĐ 23): Lấy Thông tin Chi tiết Đơn hàng
    // (ĐÃ SỬA LỖI: Xử lý 'data' là OBJECT thay vì MẢNG)
    // ============================================================
    /**
     * Gọi API GHN để lấy chi tiết 1 đơn hàng (dùng order_code)
     * @param string $order_code (Mã vận đơn, vd: "L4MR8T")
     * @return array|null Trả về mảng data (chứa 'status', 'log'...) nếu thành công
     */
    public function getOrderInfo($order_code) {
        
        // 1. Chuẩn bị Dữ liệu (chỉ cần order_code)
        $data = [
            "order_code" => $order_code
        ];

        // 2. Chuẩn bị Headers (QUAN TRỌNG: Chỉ cần Token CÁ NHÂN)
        $headers = [
            "Content-Type: application/json",
            "Token: "."9e958cee-c146-11f0-a621-f2a9392e54c8" // (Token CÁ NHÂN của bạn)
        ];
        
        // 3. Gọi hàm POST helper (vì API này là POST)
        $response_data = $this->callPostApi($this->api_url_detail, $data, $headers);
        
        // 4. Xử lý kết quả
        if (isset($response_data['code']) && $response_data['code'] == 200) { 
            
            // =========================================================
            // SỬA LỖI Ở ĐÂY:
            // Lỗi cũ: if (isset($response_data['data'][0]))
            // (Log mới cho thấy data LÀ 1 object, KHÔNG PHẢI mảng)
            // =========================================================
            if (isset($response_data['data']) && isset($response_data['data']['order_code'])) {
                return $response_data['data']; // Trả về object data
            }
        }
        
        // (Nếu thất bại, Ghi log lỗi - code này sẽ không chạy nữa)
        $error_log = "GHN API Logic Error (GetOrderInfo Failed - Lỗi Logic)\n";
        $error_log .= "URL: " . $this->api_url_detail . "\n";
        $error_log .= "Response (Phản hồi từ GHN): \n" . json_encode($response_data) . "\n";
        $error_log .= "Data Sent (Dữ liệu đã gửi đi): \n" . json_encode($data) . "\n\n";
        file_put_contents('ghn_error.log', $error_log, FILE_APPEND);
        
        return null; // Thất bại
    }

} // <-- Dấu } đóng class
?>