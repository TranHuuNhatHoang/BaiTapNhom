-- =========================================================================================
-- DATABASE: laptop_store (Lược đồ hoàn chỉnh - Phù hợp với dự án thi cuối kỳ)
-- =========================================================================================

-- TẠO VÀ SỬ DỤNG DATABASE
CREATE DATABASE IF NOT EXISTS laptop_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE laptop_store;

-- 1. BẢNG NGƯỜI DÙNG (KHÁCH HÀNG + ADMIN)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- LUÔN DÙNG PHP password_hash()
    phone VARCHAR(20),
    address VARCHAR(255),
    province VARCHAR(100), -- Vd: 'TP. Hồ Chí Minh'
    role ENUM('user','admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. BẢNG THƯƠNG HIỆU LAPTOP
CREATE TABLE brands (
    brand_id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255),
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. BẢNG DANH MỤC SẢN PHẨM
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- 4. BẢNG SẢN PHẨM (LAPTOP)
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE, -- URL thân thiện (ví dụ: dell-xps-13)
    brand_id INT,
    category_id INT,
    price DECIMAL(12,2) NOT NULL,
    quantity INT DEFAULT 0, -- Số lượng tồn kho
    main_image VARCHAR(255),
    description TEXT,
    specifications JSON, -- Lưu trữ cấu hình (CPU, RAM, SSD...)
    is_featured BOOLEAN DEFAULT FALSE, -- Sản phẩm nổi bật
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id) REFERENCES brands(brand_id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- 5. BẢNG HÌNH ẢNH SẢN PHẨM (Nhiều ảnh cho 1 sản phẩm)
CREATE TABLE product_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- 6. BẢNG ĐÁNH GIÁ SẢN PHẨM (REVIEWS)
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5), -- 1 đến 5 sao
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY uc_user_product (user_id, product_id) -- Mỗi user chỉ được đánh giá 1 lần trên 1 sản phẩm
);

-- 7. BẢNG ĐƠN HÀNG
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    shipping_province VARCHAR(100),
    shipping_phone VARCHAR(20) NOT NULL,
    notes TEXT,
    payment_method ENUM('cod','bank_transfer') DEFAULT 'cod',
    order_status ENUM('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT
);

-- 8. BẢNG CHI TIẾT ĐƠN HÀNG
CREATE TABLE order_details (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    unit_price DECIMAL(12,2) NOT NULL, -- Giá bán tại thời điểm đặt (Rất quan trọng)
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT
);

-- 9. BẢNG GIỎ HÀNG (Dành cho user đã đăng nhập)
CREATE TABLE carts (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE, -- Mỗi user 1 giỏ hàng
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 10. BẢNG CHI TIẾT GIỎ HÀNG
CREATE TABLE cart_items (
    cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    FOREIGN KEY (cart_id) REFERENCES carts(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY uc_cart_product (cart_id, product_id)
);

-- DỮ LIỆU MẪU (SEED DATA) cũng được bao gồm trong file này (dành cho môi trường phát triển)

-- ... DỮ LIỆU MẪU ĐƯỢC KẾ THỪA TỪ complete_laptop_store.sql ...

-- VIEW / REPORT MẪU (BÁO CÁO DOANH THU)
CREATE OR REPLACE VIEW v_revenue_by_month AS
SELECT
    DATE_FORMAT(created_at, '%Y-%m') AS month,
    SUM(total_amount) AS revenue,
    COUNT(order_id) AS total_orders
FROM orders
WHERE order_status IN ('paid', 'completed')
GROUP BY month
ORDER BY month DESC;
