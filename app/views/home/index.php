<?php 
/*
 * File: app/views/home/index.php
 *
 * Các biến được truyền từ HomeController@index:
 * $featured_products (mảng) - 4 Sản phẩm nổi bật (Phần 1 - NAY LÀ SẢN PHẨM HOT)
 * $gaming_products (mảng) - 4 Sản phẩm mô phỏng Gaming (Phần 2 - NAY LÀ SẢN PHẨM MỚI NHẤT)
 * $public_coupons (mảng) - Mã giảm giá công khai
 * $top_brands (mảng) - 4 Thương hiệu hàng đầu (MỚI)
 */
?>

<div class="homepage-banner" style="margin-bottom: 30px;">
    <div class="banner-content">
        <h1>MacBook Pro M3 Max</h1>
        <p>Sức mạnh tối thượng. Hiệu năng vô song. Thiết kế đẳng cấp.</p>
        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=category&id=5" 
            class="btn btn-primary btn-large">
            Khám Phá MacBook
        </a>
    </div>
</div>

<?php if (isset($public_coupons) && !empty($public_coupons)): ?>
<div class="public-coupons section-spacing section-box">
    <h2 class="section-title title-highlight">🔥 Mã Giảm Giá Đang Hoạt Động</h2>
    <div class="coupon-grid">
        <?php foreach ($public_coupons as $coupon): ?>
            <?php 
                $min_order = isset($coupon['min_order_amount']) ? $coupon['min_order_amount'] : 0;
                $coupon_code = isset($coupon['coupon_code']) ? $coupon['coupon_code'] : '';
            ?>
            <div class="coupon-card coupon-card-modern">
                <div class="coupon-header">
                    <span class="discount-value">
                        <?php 
                            echo $coupon['discount_type'] == 'fixed' ? number_format($coupon['discount_value']) . ' VND' : $coupon['discount_value'] . ' %';
                        ?>
                    </span>
                    <span class="discount-label">Giảm</span>
                </div>
                <div class="coupon-body">
                    <p class="coupon-code">Mã: <strong><?php echo htmlspecialchars($coupon_code); ?></strong></p>
                    <p class="coupon-expiry">Hạn: **<?php echo date('d/m/Y', strtotime($coupon['expires_at'])); ?>**</p>
                    <button class="btn btn-secondary btn-small copy-coupon-btn" 
                        data-coupon-code="<?php echo htmlspecialchars($coupon_code); ?>">
                        Sao Chép Mã
                    </button>
                    <small class="coupon-note">
                        *<?php 
                            echo $min_order > 0 ? 'Áp dụng cho đơn từ ' . number_format($min_order) . ' VND' : 'Mọi đơn hàng'; 
                        ?>
                    </small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    // Script sao chép mã giảm giá (giữ nguyên)
    document.querySelectorAll('.copy-coupon-btn').forEach(button => {
        button.addEventListener('click', function() {
            const code = this.getAttribute('data-coupon-code');
            navigator.clipboard.writeText(code).then(() => {
                alert('Đã sao chép mã giảm giá: ' + code);
            }).catch(err => {
                console.error('Không thể sao chép: ', err);
                alert('Không thể sao chép mã. Vui lòng thử lại.');
            });
        });
    });
</script>
<?php endif; ?>

<div class="selling-points section-spacing">
    <div class="point-item">
        <i class="fas fa-shield-alt"></i> <h4>Bảo hành chính hãng</h4>
        <p>Đổi mới 1:1 trong 30 ngày. An tâm sử dụng.</p>
    </div>
    <div class="point-item">
        <i class="fas fa-shipping-fast"></i> <h4>Giao hàng miễn phí</h4>
        <p>Giao nhanh 24h, kiểm tra trước khi thanh toán.</p>
    </div>
    <div class="point-item">
        <i class="fas fa-headset"></i> <h4>Hỗ trợ 24/7</h4>
        <p>Tư vấn chuyên nghiệp, hỗ trợ kỹ thuật trọn đời.</p>
    </div>
    <div class="point-item">
        <i class="fas fa-tags"></i> <h4>Giá tốt nhất</h4>
        <p>Cam kết mức giá cạnh tranh nhất thị trường.</p>
    </div>
</div>

<hr>

<?php if (isset($featured_products) && !empty($featured_products)): ?>
<div class="featured-products section-spacing section-box">
    <h2 class="section-title title-highlight">📈 Sản phẩm Hot Bán Chạy</h2>
    
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

<?php if (isset($top_brands) && !empty($top_brands)): ?>
<div class="section-spacing section-box" style="padding: 20px;">
    <h2 class="section-title" style="margin-bottom: 10px; font-size: 1.5em;">Thương Hiệu Hàng Đầu</h2>
    <div class="brand-grid">
        <?php foreach ($top_brands as $brand): ?>
            <?php 
                // 1. Lấy tên thương hiệu (ví dụ: "Acer", "Apple")
                $brand_name = htmlspecialchars($brand['brand_name']);
                
                // 2. Chuẩn hóa tên thương hiệu thành tên file logo (ví dụ: "Acer" -> "acer.png")
                // Sử dụng strtolower và str_replace để thay thế khoảng trắng bằng gạch dưới, sau đó thêm .png
                $logo_filename = strtolower(str_replace(' ', '_', $brand_name)) . '.png';
                
                // 3. Đường dẫn cuối cùng: BASE_URL + public/images/logo/ + tên file
                $image_path = BASE_URL . 'public/images/logo/' . $logo_filename;
            ?>
            <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=brand&id=<?php echo $brand['brand_id']; ?>" 
               title="<?php echo $brand_name; ?>">
               
                <img src="<?php echo $image_path; ?>" 
                    alt="Logo <?php echo $brand_name; ?>"
                    style="max-height: 50px; width: auto; object-fit: contain; opacity: 0.8; transition: opacity 0.3s; margin: 0 auto;">
                    
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<hr>

<?php if (isset($gaming_products) && !empty($gaming_products)): ?>
<div class="gaming-products section-spacing section-box" style="background-color: #f0f3f7;">
    <h2 class="section-title title-highlight" style="color: #007bff;">✨ Laptop Gaming - Hàng Mới Về</h2>
    
    <div class="product-grid">
        <?php foreach ($gaming_products as $product): ?>
            <div class="product-card gaming-card">
                <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
                    alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                <div class="product-card-body">
                    <small class="brand" style="color: #dc3545; font-weight: bold;"><?php echo htmlspecialchars($product['brand_name']); ?></small>
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p class="price"><?php echo number_format($product['price']); ?> VND</p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>" 
                        class="btn btn-primary" style="width: 100%;">Chiến Ngay</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div style="text-align: center; margin-top: 30px;">
           <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=category&id=1" 
               class="btn btn-secondary btn-large">Xem tất cả Laptop Gaming</a>
    </div>
</div>
<?php endif; ?>

<hr>

<div class="final-cta section-spacing">
    <div class="cta-box">
        <h3>🎁 Đừng bỏ lỡ các ưu đãi độc quyền từ Laptop Store!</h3>
        <p>Đăng ký email ngay hôm nay để nhận tư vấn chuyên sâu và thông tin về các chương trình khuyến mãi SỐC nhất.</p>
        
        <form action="#" method="POST" class="cta-form">
            <input type="email" placeholder="Nhập Email của bạn..." required>
            <button type="submit" class="btn btn-success btn-cta btn-large">Đăng ký nhận tin</button>
        </form>
    </div>
</div>