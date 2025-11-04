<?php
class Category {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Lấy tất cả danh mục
    public function getAllCategories() {
        $sql = "SELECT * FROM categories ORDER BY category_name ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>