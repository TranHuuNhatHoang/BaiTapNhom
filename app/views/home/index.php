<?php 
/*
 * File này là Trang chủ MỚI
 * Chỉ hiển thị Banner và Sản phẩm Nổi bật
 * (Code này được copy từ file products/index.php cũ)
 *
 * Các biến được truyền từ HomeController@index:
 * $featured_products (mảng)
 */
?>

<!-- 
============================================================
 BANNER TRANG CHỦ (Lấy từ GĐ 21)
============================================================
-->
<div class="homepage-banner" style="margin-bottom: 30px;"> <!-- Thêm margin dưới -->
    <div class="banner-content">
        <h1>MacBook Pro M3 Max</h1>
        <p>Sức mạnh tối thượng. Hiệu năng vô song.</p>
        <!-- (Link này trỏ đến trang Category "MacBook" (ID=5 CSDL của bạn)) -->
        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=category&id=5" 
           class="btn btn-primary">
            Khám Phá Ngay
        </a>
    </div>
</div>
<!-- KẾT THÚC BANNER -->


<!-- 
============================================================
 SẢN PHẨM NỔI BẬT (Lấy từ GĐ 16)
============================================================
-->
<?php if (isset($featured_products) && !empty($featured_products)): ?>
<div class="featured-products" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #eee;">
    <h2 style="font-size: 1.8em; margin-bottom: 15px;">Sản phẩm Nổi bật</h2>
    
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

<!-- (Trang chủ có thể thêm các nội dung khác như "Thương hiệu nổi bật"...) -->