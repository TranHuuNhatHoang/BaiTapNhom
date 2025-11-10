<?php
class Review {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
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