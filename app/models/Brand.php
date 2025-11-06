<?php
class Brand {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Lấy tất cả thương hiệu
    public function getAllBrands() {
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // HÀM MỚI: Lấy 1 brand bằng ID
    public function getBrandById($id) {
        $sql = "SELECT * FROM brands WHERE brand_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // HÀM MỚI: Tạo brand
    public function createBrand($name, $description, $logo) {
        $sql = "INSERT INTO brands (brand_name, description, logo) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $name, $description, $logo);
        return $stmt->execute();
    }

    // HÀM MỚI: Cập nhật brand
    public function updateBrand($id, $name, $description, $logo) {
        $sql = "UPDATE brands SET brand_name = ?, description = ?, logo = ? WHERE brand_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $description, $logo, $id);
        return $stmt->execute();
    }

    // HÀM MỚI: Xóa brand
    public function deleteBrand($id) {
        $sql = "DELETE FROM brands WHERE brand_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>