<?php
class Product {
    private $conn; // Biến kết nối CSDL

    // Hàm khởi tạo — tự chạy khi gọi new Product($conn)
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }
    /**
     * HÀM MỚI: Đếm tổng số sản phẩm
     */
    public function countAllProducts() {
        $sql = "SELECT COUNT(product_id) as total FROM products";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['total'];
    }

    /**
     * 🧩 LẤY TẤT CẢ SẢN PHẨM (KÈM TÊN HÃNG VÀ DANH MỤC)
     * Dùng cho trang Admin và trang Sản phẩm
     */
    /**
     * CẬP NHẬT HÀM NÀY: Sửa lại hàm getAllProducts
     * (Thêm $limit và $offset)
     */
    public function getAllProducts($limit, $offset) {
        
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
                    p.created_at DESC
                LIMIT ? OFFSET ?"; // <-- THÊM MỚI
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset); // 'i' = integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
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
    /**
     * Hàm lấy MỘT sản phẩm bằng ID (kèm Brand và Category)
     */
    public function getProductById($id) {
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
                WHERE 
                    p.product_id = ?"; // Dùng prepared statement
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id); // 'i' nghĩa là 'integer'
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Trả về sản phẩm (dưới dạng mảng)
        return $result->fetch_assoc();
    }
    /**
     * HÀM MỚI: Cập nhật sản phẩm
     */
    public function updateProduct($id, $name, $price, $brand_id, $category_id, $quantity, $description, $main_image) {
        // Tạo lại 'slug'
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        
        $sql = "UPDATE products SET 
                    product_name = ?, slug = ?, brand_id = ?, category_id = ?, 
                    price = ?, quantity = ?, description = ?, main_image = ?
                WHERE product_id = ?";
                
        $stmt = $this->conn->prepare($sql);
        // "ssiiidssi" = 8 tham số + 1 ID ở cuối
        $stmt->bind_param("ssiiidssi", $name, $slug, $brand_id, $category_id, $price, $quantity, $description, $main_image, $id);
        
        return $stmt->execute();
    }

    /**
     * HÀM MỚI: Xóa sản phẩm
     */
    public function deleteProduct($id) {
        $sql = "DELETE FROM products WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }

    
     // HÀM : Đếm kết quả tìm kiếm
    
    public function countSearchResults($query) {
        $search_term = "%" . $query . "%";
        $sql = "SELECT COUNT(product_id) as total FROM products WHERE product_name LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }
     // HÀM : Tìm sản phẩm theo Tên (có JOIN và Phân trang)
     
    public function searchProductsByName($query, $limit, $offset) {
        $search_term = "%" . $query . "%"; // Thêm dấu % để tìm kiếm (LIKE)
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
                WHERE
                    p.product_name LIKE ?
                ORDER BY 
                    p.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $search_term, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * HÀM MỚI : Đếm tổng số sản phẩm theo Danh mục
     */
    public function countProductsByCategory($category_id) {
        $sql = "SELECT COUNT(product_id) as total FROM products WHERE category_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $category_id); // 'i' = integer
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    /**
     * HÀM MỚI : Lấy sản phẩm theo Danh mục (có JOIN và Phân trang)
     */
    public function getProductsByCategory($category_id, $limit, $offset) {
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
                WHERE
                    p.category_id = ?
                ORDER BY 
                    p.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        // "iii" = integer (category_id), integer (limit), integer (offset)
        $stmt->bind_param("iii", $category_id, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    
     // HÀM : Lấy tất cả ảnh phụ của 1 sản phẩm
     
    public function getProductImages($product_id) {
        $sql = "SELECT * FROM product_images WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

} 

?>
