<?php
class Product {
    private $conn; // Biến kết nối CSDL

    // Hàm khởi tạo — tự chạy khi gọi new Product($conn)
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * 🧩 LẤY TẤT CẢ SẢN PHẨM (KÈM TÊN HÃNG VÀ DANH MỤC)
     * Dùng cho trang Admin và trang Sản phẩm
     */
    public function getAllProducts() {
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

        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    /**
     * 🆕 TẠO SẢN PHẨM MỚI
     * Dùng khi thêm sản phẩm trong trang Admin
     */
    public function createProduct($name, $price, $brand_id, $category_id, $quantity, $description, $main_image) {
        // Tạo slug (chuỗi URL-friendly)
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        $sql = "INSERT INTO products 
                (product_name, slug, brand_id, category_id, price, quantity, description, main_image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);

        // Kiểm tra chuẩn bị truy vấn
        if (!$stmt) {
            die("Lỗi prepare SQL: " . $this->conn->error);
        }

        // Gắn tham số vào truy vấn (bind)
        // s = string, i = integer, d = double
        $stmt->bind_param("ssiiidss", $name, $slug, $brand_id, $category_id, $price, $quantity, $description, $main_image);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Lỗi thêm sản phẩm: " . $stmt->error;
            return false;
        }
    }
}
?>
