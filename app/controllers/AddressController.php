<?php
// (File này KHÔNG cần header/footer, chỉ trả về JSON)
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/app/models/Address.php';

class AddressController {
    
    /**
     * Action: Lấy danh sách Quận/Huyện (cho AJAX)
     * URL: index.php?controller=address&action=getDistricts&province_id=...
     */
    public function getDistricts() {
        global $conn;
        $province_id = (int)($_GET['province_id'] ?? 0);
        
        if ($province_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode([]); // Trả về mảng rỗng nếu không có ID
            exit;
        }

        $addressModel = new Address($conn);
        $districts = $addressModel->getDistrictsByProvince($province_id);
        
        // Trả về dữ liệu dạng JSON
        header('Content-Type: application/json');
        echo json_encode($districts);
        exit;
    }
    
    /**
     * Action: Lấy danh sách Phường/Xã (cho AJAX)
     * URL: index.php?controller=address&action=getWards&district_id=...
     */
    public function getWards() {
        global $conn;
        $district_id = (int)($_GET['district_id'] ?? 0);

        if ($district_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode([]); // Trả về mảng rỗng nếu không có ID
            exit;
        }
        
        $addressModel = new Address($conn);
        $wards = $addressModel->getWardsByDistrict($district_id);
        
        // Trả về dữ liệu dạng JSON
        header('Content-Type: application/json');
        echo json_encode($wards);
        exit;
    }
}
?>