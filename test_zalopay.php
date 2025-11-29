<?php
// File này dùng để GIẢ LẬP việc ZaloPay gọi về localhost của bạn
// Cách dùng: Mở trình duyệt, chạy http://localhost/BaiTapNhom/test_zalopay.php

// 1. CẤU HÌNH
// Link tới Controller Callback của bạn
$url_callback = "http://localhost/BaiTapNhom/index.php?controller=zalopaycallback&action=index";
$key2 = "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz"; // Key Sandbox mặc định

// =======================================================================
// 2. QUAN TRỌNG: NHẬP MÃ ĐƠN HÀNG BẠN MUỐN TEST "THÀNH CÔNG" VÀO ĐÂY
// (Xem trong phpMyAdmin bảng orders, tìm đơn nào đang 'pending' và có payment_method='zalopay')
// =======================================================================
// 2. CẬP NHẬT: LẤY ID TỰ ĐỘNG TỪ URL (GET)
// Ví dụ: test_zalopay.php?order_id=105
// =======================================================================
$order_id_to_test = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// 3. Tạo dữ liệu giả lập (giống hệt ZaloPay gửi về)
$data_array = [
    "app_id" => 2553,
    "app_trans_id" => date("ymd") . "_TestLocal_" . $order_id_to_test,
    "app_time" => round(microtime(true) * 1000),
    "app_user" => "SimulatedUser",
    "amount" => 10000, // Số tiền test
    "embed_data" => "{}",
    "item" => "[]",
    "zp_trans_id" => rand(10000000, 99999999), // Mã giao dịch giả của Zalo
    "server_time" => round(microtime(true) * 1000),
    "channel" => 38,
    "merchant_user_id" => "user123",
    "user_fee_amount" => 0,
    "discount_amount" => 0,
    // QUAN TRỌNG: Description phải khớp format code controller để nó tách lấy ID
    "description" => "Thanh toan don hang #" . $order_id_to_test 
];

$data_json = json_encode($data_array);

// 4. Tạo chữ ký MAC (Bắt buộc để Controller chấp nhận)
$mac = hash_hmac("sha256", $data_json, $key2);

// 5. Gói dữ liệu gửi đi
$post_data = [
    "data" => $data_json,
    "mac" => $mac
];

// 6. Gọi cURL đến chính localhost của bạn
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_callback);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

// 7. Hiển thị kết quả ra màn hình
echo "<h1>Kết quả Giả lập Callback ZaloPay</h1>";
echo "<p>Đang gọi tới: <strong>$url_callback</strong></p>";
echo "<p>Đang giả lập thanh toán thành công cho Đơn hàng ID: <strong style='color:red; font-size:20px;'>$order_id_to_test</strong></p>";
echo "<hr>";
echo "<h3>Phản hồi từ Server (Controller của bạn):</h3>";
echo "<pre style='background:#eee; padding:10px;'>";
print_r(json_decode($response, true));
echo "</pre>";

if (strpos($response, '"return_code":1') !== false) {
    echo "<h2 style='color:green'>THÀNH CÔNG! Đơn hàng đã chuyển sang 'paid'.</h2>";
    echo "<p>Hãy vào phpMyAdmin hoặc trang Admin/Lịch sử đơn hàng để kiểm tra.</p>";
} else {
    echo "<h2 style='color:red'>THẤT BẠI!</h2>";
    echo "<p>Kiểm tra xem ID đơn hàng có đúng không, hoặc URL callback có đúng không.</p>";
}
?>