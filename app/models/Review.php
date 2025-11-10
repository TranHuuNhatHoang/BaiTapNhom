<?php
class Review {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * HÀM MỚI (Người 1): Lấy tất cả review của 1 SP (để hiển thị)
     * (JOIN với 'users' để lấy tên)
     */
    public function getReviewsByProductId($product_id) {
        $sql = "SELECT r.*, u.full_name 
                FROM reviews r
                JOIN users u ON r.user_id = u.user_id
                WHERE r.product_id = ?
                ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * HÀM MỚI (Người 1): Tạo 1 review mới
     */
    public function createReview($product_id, $user_id, $rating, $comment) {
        $sql = "INSERT INTO reviews (product_id, user_id, rating, comment) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
        return $stmt->execute();
    }
    /**
     * HÀM MỚI (Người 1): Lấy tất cả review (JOIN để lấy tên user và SP)
     */
    public function getAllReviews() {
        $sql = "SELECT r.*, u.full_name, p.product_name 
                FROM reviews r
                JOIN users u ON r.user_id = u.user_id
                JOIN products p ON r.product_id = p.product_id
                ORDER BY r.created_at DESC";
        
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * HÀM MỚI (Người 1): Xóa 1 review
     */
    public function deleteReview($review_id) {
        $sql = "DELETE FROM reviews WHERE review_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $review_id);
        return $stmt->execute();
    }
    
    // (Sau này có thể thêm hàm approveReview(id) 
    // nếu CSDL của bạn có cột 'is_approved')
}
?>