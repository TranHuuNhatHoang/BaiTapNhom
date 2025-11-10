<?php
class Product {
    private $conn; // Biến kết nối CSDL

    // Hàm khởi tạo — tự chạy khi gọi new Product($conn)
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * HÀM MỚI (GĐ 12): Đếm tổng số sản phẩm
     */
    public function countAllProducts() {
        $sql = "SELECT COUNT(product_id) as total FROM products";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['total'];
    }

    /**
     * CẬP NHẬT (NGƯỜI 2): LẤY TẤT CẢ SẢN PHẨM (Thêm $sort_order)
     */
    public function getAllProducts($limit, $offset, $sort_order = 'created_at DESC') {
        
        // 1. Whitelist (Danh sách trắng) để Sắp xếp an toàn (chống SQL Injection)
        $allowed_sorts = [
            'created_at DESC' => 'created_at DESC', // Mới nhất
            'price ASC' => 'price ASC',             // Giá tăng
            'price DESC' => 'price DESC'            // Giá giảm
        ];
        $order_by = $allowed_sorts[$sort_order] ?? 'created_at DESC'; // Mặc định

        // 2. Câu SQL
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
                ORDER BY $order_by -- Đã cập nhật
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    /**
     * TẠO SẢN PHẨM MỚI (Cho Admin)
     */
    public function createProduct($name, $price, $brand_id, $category_id, $quantity, $description, $main_image) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $sql = "INSERT INTO products 
                    (product_name, slug, brand_id, category_id, price, quantity, description, main_image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) die("Lỗi prepare SQL: " . $this->conn->error);
        $stmt->bind_param("ssiiidss", $name, $slug, $brand_id, $category_id, $price, $quantity, $description, $main_image);
        return $stmt->execute();
    }

    /**
     * Lấy MỘT sản phẩm bằng ID (kèm Brand và Category)
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
                    p.product_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Cập nhật sản phẩm (Cho Admin)
     */
    public function updateProduct($id, $name, $price, $brand_id, $category_id, $quantity, $description, $main_image) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $sql = "UPDATE products SET 
                    product_name = ?, slug = ?, brand_id = ?, category_id = ?, 
                    price = ?, quantity = ?, description = ?, main_image = ?
                WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssiiidssi", $name, $slug, $brand_id, $category_id, $price, $quantity, $description, $main_image, $id);
        return $stmt->execute();
    }

    /**
     * Xóa sản phẩm (Cho Admin)
     */
    public function deleteProduct($id) {
        $sql = "DELETE FROM products WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Đếm kết quả tìm kiếm
     */
    public function countSearchResults($query) {
        $search_term = "%" . $query . "%";
        $sql = "SELECT COUNT(product_id) as total FROM products WHERE product_name LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }
    
    /**
     * CẬP NHẬT (NGƯỜI 2): Tìm sản phẩm theo Tên (Thêm $sort_order)
     */
    public function searchProductsByName($query, $limit, $offset, $sort_order = 'created_at DESC') {
        // 1. Whitelist
        $allowed_sorts = ['created_at DESC' => 'created_at DESC', 'price ASC' => 'price ASC', 'price DESC' => 'price DESC'];
        $order_by = $allowed_sorts[$sort_order] ?? 'created_at DESC';
        
        $search_term = "%" . $query . "%";
        
        $sql = "SELECT p.*, b.brand_name, c.category_name
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE p.product_name LIKE ?
                ORDER BY $order_by -- Đã cập nhật
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $search_term, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Đếm tổng số sản phẩm theo Danh mục
     */
    public function countProductsByCategory($category_id) {
        $sql = "SELECT COUNT(product_id) as total FROM products WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    /**
     * CẬP NHẬT (NGƯỜI 2): Lấy sản phẩm theo Danh mục (Thêm $sort_order)
     */
    public function getProductsByCategory($category_id, $limit, $offset, $sort_order = 'created_at DESC') {
        // 1. Whitelist
        $allowed_sorts = ['created_at DESC' => 'created_at DESC', 'price ASC' => 'price ASC', 'price DESC' => 'price DESC'];
        $order_by = $allowed_sorts[$sort_order] ?? 'created_at DESC';

        $sql = "SELECT p.*, b.brand_name, c.category_name
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE p.category_id = ?
                ORDER BY $order_by -- Đã cập nhật
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $category_id, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy tất cả ảnh phụ của 1 sản phẩm
     */
    public function getProductImages($product_id) {
        $sql = "SELECT * FROM product_images WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Đếm tổng số sản phẩm theo Thương hiệu
     */
    public function countProductsByBrand($brand_id) {
        $sql = "SELECT COUNT(product_id) as total FROM products WHERE brand_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    /**
     * CẬP NHẬT (NGƯỜI 2): Lấy sản phẩm theo Thương hiệu (Thêm $sort_order)
     */
    public function getProductsByBrand($brand_id, $limit, $offset, $sort_order = 'created_at DESC') {
        // 1. Whitelist
        $allowed_sorts = ['created_at DESC' => 'created_at DESC', 'price ASC' => 'price ASC', 'price DESC' => 'price DESC'];
        $order_by = $allowed_sorts[$sort_order] ?? 'created_at DESC';

        $sql = "SELECT p.*, b.brand_name, c.category_name
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE p.brand_id = ?
                ORDER BY $order_by -- Đã cập nhật
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $brand_id, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
     // HÀM : Giảm số lượng tồn kho
    public function decrementStock($product_id, $quantity_to_reduce) {
        // Trừ số lượng khỏi CSDL
        // (UPDATE ... SET quantity = quantity - ?)
        $sql = "UPDATE products SET quantity = quantity - ? 
                WHERE product_id = ? AND quantity >= ?";
                // (Chỉ update nếu tồn kho >= số lượng mua)
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $quantity_to_reduce, $product_id, $quantity_to_reduce);
        $stmt->execute();
        // Trả về số dòng bị ảnh hưởng (1 là thành công, 0 là thất bại/hết hàng)
        return $stmt->affected_rows;
    }

} // <--- Dấu } đóng class Product
?>