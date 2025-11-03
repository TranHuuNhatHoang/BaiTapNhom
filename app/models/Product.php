<?php
class Product {
    
    private $conn; // Biến để giữ kết nối CSDL

    // Hàm này sẽ được gọi khi bạn tạo 'new Product()'
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Hàm lấy TẤT CẢ sản phẩm, KÈM THEO TÊN BRAND VÀ CATEGORY
     */
    public function getAllProducts() {
        
        // 1. Viết câu SQL với LEFT JOIN
        // p = products, b = brands, c = categories
        $sql = "SELECT 
                    p.*, 
                    b.brand_name, 
                    c.category_name
                FROM 
                    products p
                LEFT JOIN 
                    brands b ON p.brand_id = b.brand_id
                LEFT JOIN 
                    categories c ON p.category_id = c.category_id
                ORDER BY 
                    p.created_at DESC";

        // 2. Thực thi truy vấn
        $result = $this->conn->query($sql);

        // 3. Kiểm tra và trả về kết quả
        if ($result->num_rows > 0) {
            // Lấy tất cả các dòng và trả về dưới dạng mảng
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            // Trả về mảng rỗng nếu không có sản phẩm
            return [];
        }
    }

    /**
     * (Sau này bạn sẽ thêm các hàm khác ở đây, ví dụ:
     * public function getProductById($id) { ... }
     * public function createProduct($data) { ... }
     * ...)
     */
}
?>