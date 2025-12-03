<?php
// Tải các file config và service cần thiết
require_once '../config/config.php';
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/app/services/ShippingService.php';

/**
 * =================================================================
 * CẢNH BÁO: CHỈ CHẠY FILE NÀY MỘT LẦN DUY NHẤT!
 * =================================================================
 * File này sẽ gọi API của GHN và "hút" toàn bộ dữ liệu
 * Tỉnh/Thành, Quận/Huyện, Phường/Xã (khoảng 10,000+ dòng)
 * vào 3 bảng CSDL (provinces, districts, wards) mà bạn đã tạo.
 *
 * QUAN TRỌNG:
 * 1. Đảm bảo file app/services/ShippingService.php
 * đã có Key/ShopID Sandbox (từ 5sao.ghn.dev) CÁ NHÂN của bạn.
 * (KHÔNG DÙNG KEY CÔNG KHAI '885' vì nó có thể bị giới hạn).
 *
 * 2. Quá trình này có thể mất 1-3 phút để chạy. Đừng tắt trình duyệt.
 *
 * 3. Sau khi chạy xong và thấy "HOÀN THÀNH!", HÃY XÓA FILE NÀY!
 */

// Tăng thời gian thực thi tối đa (vì nó chạy rất lâu)
set_time_limit(300); // 300 giây = 5 phút

echo "<pre>"; // Dùng thẻ <pre> để xem log cho dễ
echo "Bắt đầu quá trình đồng bộ địa chỉ từ GHN...\n";
echo "Vui lòng đợi (có thể mất vài phút)...\n\n";

// Đảm bảo trình duyệt hiển thị output ngay lập tức
ob_flush();
flush();

$shippingService = new ShippingService();

try {
    // 1. Lấy Tỉnh/Thành
    echo "Bắt đầu lấy Tỉnh/Thành...\n";
    $provinces = $shippingService->getProvinces();
    if (empty($provinces)) {
        throw new Exception("Không lấy được Tỉnh/Thành. Kiểm tra lại Token/ShopID trong ShippingService.php");
    }
    
    foreach ($provinces as $province) {
        $sql = "INSERT INTO provinces (province_id, province_name) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $province['ProvinceID'], $province['ProvinceName']);
        $stmt->execute();
        echo "Đã thêm Tỉnh: " . $province['ProvinceName'] . "\n";
        ob_flush(); flush(); // Đẩy log ra trình duyệt

        // 2. Lấy Quận/Huyện của Tỉnh này
        $districts = $shippingService->getDistricts($province['ProvinceID']);
        if (empty($districts)) continue; // Bỏ qua nếu Tỉnh không có Quận

        foreach ($districts as $district) {
            $sql_dist = "INSERT INTO districts (district_id, province_id, district_name) VALUES (?, ?, ?)";
            $stmt_dist = $conn->prepare($sql_dist);
            $stmt_dist->bind_param("iis", $district['DistrictID'], $province['ProvinceID'], $district['DistrictName']);
            $stmt_dist->execute();
            echo "  > Đã thêm Quận: " . $district['DistrictName'] . "\n";
            ob_flush(); flush();

            // 3. Lấy Phường/Xã của Quận này
            $wards = $shippingService->getWards($district['DistrictID']);
            if (empty($wards)) continue; // Bỏ qua nếu Quận không có Phường

            foreach ($wards as $ward) {
                $sql_ward = "INSERT INTO wards (ward_code, district_id, ward_name) VALUES (?, ?, ?)";
                $stmt_ward = $conn->prepare($sql_ward);
                $stmt_ward->bind_param("sis", $ward['WardCode'], $district['DistrictID'], $ward['WardName']);
                $stmt_ward->execute();
                // (Không echo Phường/Xã vì quá nhiều, sẽ làm chậm)
            }
        }
        echo "-----\n";
    }
    
    echo "\n\n============================================\n";
    echo "HOÀN THÀNH! Đã tải xong toàn bộ dữ liệu địa chỉ.\n";
    echo "BÂY GIỜ HÃY XÓA FILE 'config/setup_address.php' NÀY!";
    echo "\n============================================\n";

} catch (Exception $e) {
    echo "\n\n--- LỖI NGHIÊM TRỌNG ---\n";
    echo $e->getMessage();
}

echo "</pre>";
?>