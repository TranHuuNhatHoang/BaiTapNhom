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

    // HÀM MỚI: Lấy 1 category bằng ID
    public function getCategoryById($id) {
        $sql = "SELECT * FROM categories WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // HÀM MỚI: Tạo category
    public function createCategory($name, $description) {
        $sql = "INSERT INTO categories (category_name, description) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $name, $description);
        return $stmt->execute();
    }

    // HÀM MỚI: Cập nhật category
    public function updateCategory($id, $name, $description) {
        $sql = "UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $description, $id);
        return $stmt->execute();
    }

    // HÀM MỚI: Xóa category
    public function deleteCategory($id) {
        $sql = "DELETE FROM categories WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>