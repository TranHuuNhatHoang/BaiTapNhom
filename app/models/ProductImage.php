<?php
class ProductImage {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * HÀM MỚI (Người 3): Lấy tất cả ảnh của 1 SP
     */
    public function getImagesByProductId($product_id) {
        $sql = "SELECT * FROM product_images WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * HÀM MỚI (Người 3): Thêm ảnh mới
     */
    public function addImage($product_id, $image_url) {
        $sql = "INSERT INTO product_images (product_id, image_url) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $product_id, $image_url);
        return $stmt->execute();
    }
    
    /**
     * HÀM MỚI (Người 3): Lấy 1 ảnh (để xóa)
     */
    public function getImageById($image_id) {
        $sql = "SELECT * FROM product_images WHERE image_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * HÀM MỚI (Người 3): Xóa 1 ảnh
     */
    public function deleteImage($image_id) {
        $sql = "DELETE FROM product_images WHERE image_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $image_id);
        return $stmt->execute();
    }
}
?>