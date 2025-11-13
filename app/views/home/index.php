<?php 
/*
 * File này là Trang chủ MỚI - Đã bổ sung đầy đủ các khu vực cơ bản
 *
 * Các biến được truyền từ HomeController@index:
 * $featured_products (mảng) - 4 Sản phẩm nổi bật
 * $gaming_products (mảng) - 4 Sản phẩm mô phỏng Gaming
 */
?>

<!-- 
============================================================
 BANNER TRANG CHỦ 
============================================================
-->
<div class="homepage-banner" style="margin-bottom: 30px;">
    <div class="banner-content">
        <h1>MacBook Pro M3 Max</h1>
        <p>Sức mạnh tối thượng. Hiệu năng vô song.</p>
        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=category&id=5" 
            class="btn btn-primary">
            Khám Phá Ngay
        </a>
    </div>
</div>
<!-- KẾT THÚC BANNER -->

<!-- 
============================================================
 KHU VỰC LỢI ÍCH/ĐIỂM MẠNH (Selling Points) 
============================================================
-->
<div class="selling-points" style="margin-bottom: 30px;">
    <div class="point-item">
        <i class="fas fa-shield-alt"></i> <!-- Icon Bảo hành -->
        <h4>Bảo hành chính hãng</h4>
        <p>Đổi mới 1:1 trong 30 ngày. An tâm sử dụng.</p>
    </div>
    <div class="point-item">
        <i class="fas fa-shipping-fast"></i> <!-- Icon Giao hàng -->
        <h4>Giao hàng miễn phí</h4>
        <p>Giao nhanh toàn quốc, kiểm tra trước khi thanh toán.</p>
    </div>
    <div class="point-item">
        <i class="fas fa-headset"></i> <!-- Icon Hỗ trợ -->
        <h4>Hỗ trợ 24/7</h4>
        <p>Tư vấn chuyên nghiệp, hỗ trợ kỹ thuật trọn đời.</p>
    </div>
    <div class="point-item">
        <i class="fas fa-tags"></i> <!-- Icon Giá tốt -->
        <h4>Giá tốt nhất</h4>
        <p>Cam kết mức giá cạnh tranh nhất thị trường.</p>
    </div>
</div>
<!-- KẾT THÚC LỢI ÍCH -->


<!-- 
============================================================
 SẢN PHẨM NỔI BẬT 
============================================================
-->
<?php if (isset($featured_products) && !empty($featured_products)): ?>
<div class="featured-products section-spacing">
    <h2 class="section-title">✨ Sản phẩm Nổi bật trong tuần</h2>
    
    <div class="product-grid">
        <?php foreach ($featured_products as $product): ?>
            
            <div class="product-card">
                <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
                    alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                
                <div class="product-card-body">
                    <small class="brand"><?php echo htmlspecialchars($product['brand_name']); ?></small>
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p class="price"><?php echo number_format($product['price']); ?> VND</p>
                    
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>" 
                        class="btn btn-secondary" style="width: 100%;">Xem chi tiết</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
<!-- KẾT THÚC NỔI BẬT -->

<!-- 
============================================================
 4. LỜI KÊU GỌI HÀNH ĐỘNG CUỐI CÙNG (CTA) 
============================================================
-->
<div class="final-cta section-spacing">
    <div class="cta-box">
        <h3>Không tìm thấy sản phẩm? Hãy để chúng tôi giúp bạn!</h3>
        <p>Đăng ký email để nhận tư vấn chuyên sâu và thông tin ưu đãi mới nhất từ cửa hàng.</p>
        
        <form action="#" method="POST" class="cta-form">
            <input type="email" placeholder="Nhập Email của bạn..." required>
            <button type="submit" class="btn btn-success btn-cta">Đăng ký ngay</button>
        </form>
    </div>
</div>
<!-- KẾT THÚC CTA -->