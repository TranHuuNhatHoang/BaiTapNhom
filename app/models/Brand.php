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
}
?>