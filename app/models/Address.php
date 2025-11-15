<?php
/**
 * File Model (MỚI) - Dùng để truy vấn CSDL địa chỉ
 * (Không gọi API nữa, mà đọc 3 bảng đã lưu)
 */
class Address {
    
    private $conn; // Biến giữ kết nối CSDL

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Lấy TẤT CẢ Tỉnh/Thành từ CSDL
     */
    public function getProvinces() {
        $sql = "SELECT * FROM provinces ORDER BY province_name ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy Quận/Huyện theo ID Tỉnh
     */
    public function getDistrictsByProvince($province_id) {
        $sql = "SELECT * FROM districts WHERE province_id = ? ORDER BY district_name ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $province_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy Phường/Xã theo ID Quận
     */
    public function getWardsByDistrict($district_id) {
        $sql = "SELECT * FROM wards WHERE district_id = ? ORDER BY ward_name ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $district_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>