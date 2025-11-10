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
    public function countAllProducts($price_range = null) {
        $price_sql = $this->buildPriceFilterSql($price_range);
        
        // Phải thêm alias 'p' và 'WHERE 1=1'
        $sql = "SELECT COUNT(p.product_id) as total FROM products p WHERE 1=1 $price_sql";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['total'];
    }

    /**
     * CẬP NHẬT (NGƯỜI 2): LẤY TẤT CẢ SẢN PHẨM (Thêm $sort_order)
     */
    public function getAllProducts($limit, $offset, $sort_order = 'created_at DESC', $price_range = null) {
        
        $allowed_sorts = [
            'created_at DESC' => 'created_at DESC', 'price ASC' => 'price ASC', 'price DESC' => 'price DESC'
        ];
        $order_by = $allowed_sorts[$sort_order] ?? 'created_at DESC';
        
        // Lấy SQL lọc giá
        $price_sql = $this->buildPriceFilterSql($price_range);

        $sql = "SELECT p.*, b.brand_name, c.category_name
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE 1=1 $price_sql -- THÊM MỚI
                ORDER BY $order_by 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
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
    public function countSearchResults($query, $price_range = null) {
        $price_sql = $this->buildPriceFilterSql($price_range);
        $search_term = "%" . $query . "%";
        
        $sql = "SELECT COUNT(p.product_id) as total FROM products p WHERE p.product_name LIKE ? $price_sql";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }
    
    /**
     * CẬP NHẬT (NGƯỜI 2): Tìm sản phẩm theo Tên (Thêm $sort_order)
     */
    public function searchProductsByName($query, $limit, $offset, $sort_order = 'created_at DESC', $price_range = null) {
        $allowed_sorts = ['created_at DESC' => 'created_at DESC', 'price ASC' => 'price ASC', 'price DESC' => 'price DESC'];
        $order_by = $allowed_sorts[$sort_order] ?? 'created_at DESC';
        $price_sql = $this->buildPriceFilterSql($price_range);
        $search_term = "%" . $query . "%";
        
        $sql = "SELECT p.*, b.brand_name, c.category_name
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE p.product_name LIKE ? $price_sql -- THÊM MỚI
                ORDER BY $order_by
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
    public function countProductsByCategory($category_id, $price_range = null) {
        $price_sql = $this->buildPriceFilterSql($price_range);
        
        $sql = "SELECT COUNT(p.product_id) as total FROM products p WHERE p.category_id = ? $price_sql";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    /**
     * CẬP NHẬT (NGƯỜI 2): Lấy sản phẩm theo Danh mục (Thêm $sort_order)
     */
    public function getProductsByCategory($category_id, $limit, $offset, $sort_order = 'created_at DESC', $price_range = null) {
        $allowed_sorts = ['created_at DESC' => 'created_at DESC', 'price ASC' => 'price ASC', 'price DESC' => 'price DESC'];
        $order_by = $allowed_sorts[$sort_order] ?? 'created_at DESC';
        $price_sql = $this->buildPriceFilterSql($price_range);

        $sql = "SELECT p.*, b.brand_name, c.category_name
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE p.category_id = ? $price_sql -- THÊM MỚI
                ORDER BY $order_by
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
    public function countProductsByBrand($brand_id, $price_range = null) {
        $price_sql = $this->buildPriceFilterSql($price_range);
        
        $sql = "SELECT COUNT(p.product_id) as total FROM products p WHERE p.brand_id = ? $price_sql";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    /**
     * CẬP NHẬT (NGƯỜI 2): Lấy sản phẩm theo Thương hiệu (Thêm $sort_order)
     */
    public function getProductsByBrand($brand_id, $limit, $offset, $sort_order = 'created_at DESC', $price_range = null) {
        $allowed_sorts = ['created_at DESC' => 'created_at DESC', 'price ASC' => 'price ASC', 'price DESC' => 'price DESC'];
        $order_by = $allowed_sorts[$sort_order] ?? 'created_at DESC';
        $price_sql = $this->buildPriceFilterSql($price_range);

        $sql = "SELECT p.*, b.brand_name, c.category_name
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE p.brand_id = ? $price_sql -- THÊM MỚI
                ORDER BY $order_by
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $brand_id, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * HÀM MỚI (Người 3): Giảm số lượng tồn kho
     *
     * @param int $product_id ID sản phẩm cần trừ
     * @param int $quantity_to_reduce Số lượng cần trừ
     * @return int Số dòng bị ảnh hưởng (1 = thành công, 0 = thất bại/hết hàng)
     */
    public function decrementStock($product_id, $quantity_to_reduce) {
        
        // Câu SQL này đảm bảo 2 việc:
        // 1. SET quantity = quantity - ? : Trừ số lượng tồn kho.
        // 2. WHERE product_id = ? AND quantity >= ? :
        //    Chỉ thực hiện việc trừ nếu SỐ LƯỢNG TỒN KHO (quantity)
        //    LỚN HƠN HOẶC BẰNG số lượng khách mua.
        
        $sql = "UPDATE products SET quantity = quantity - ? 
                WHERE product_id = ? AND quantity >= ?";
        
        $stmt = $this->conn->prepare($sql);
        
        // "iii" = integer, integer, integer
        $stmt->bind_param("iii", $quantity_to_reduce, $product_id, $quantity_to_reduce);
        
        $stmt->execute();
        
        // Trả về số dòng bị ảnh hưởng (affected_rows)
        // Nếu thành công (trừ kho được), nó trả về 1.
        // Nếu thất bại (hết hàng, quantity < $quantity_to_reduce), nó trả về 0.
        return $stmt->affected_rows;
    }

    /**
     * HÀM HỖ TRỢ MỚI (Người 2): Xây dựng chuỗi SQL cho Lọc Giá
     * Dùng 'p.price' vì các hàm JOIN đều dùng alias 'p'
     */
    private function buildPriceFilterSql($price_range) {
        $price_sql = "";
        
        // Whitelist (Danh sách trắng) an toàn
        switch ($price_range) {
            case 'duoi-10':
                $price_sql = " AND p.price < 10000000";
                break;
            case '10-20':
                $price_sql = " AND p.price BETWEEN 10000000 AND 20000000";
                break;
            case 'tren-20':
                $price_sql = " AND p.price > 20000000";
                break;
        }
        return $price_sql; // Trả về chuỗi SQL (hoặc "" nếu không lọc)
    }
    /**
     * HÀM MỚI (Người 2 - GĐ16): Lấy sản phẩm Nổi bật (is_featured = 1)
     */
    public function getFeaturedProducts($limit = 4) {
        $sql = "SELECT p.*, b.brand_name, c.category_name
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE p.is_featured = 1
                ORDER BY p.created_at DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

} // <--- Dấu } đóng class Product
?>