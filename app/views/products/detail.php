<?php 
/*
 * File này hiển thị trang Chi tiết Sản phẩm
 * Nó sử dụng các class CSS toàn cục:
 * .price, .btn, .form-control, .product-grid, .product-card
 *
 * Các biến được truyền từ ProductController@detail:
 * $product, $product_images, $reviews, $related_products
 */
?>

<!-- (Biến $product, $product_images, $related_products... được truyền từ Controller) -->
<a href="<?php echo BASE_URL; ?>" class="btn btn-secondary" style="margin-bottom: 15px;">&laquo; Quay lại</a>
<hr>

<div class="product-detail" style="display: flex; gap: 20px;">
    
    <!-- Cột bên trái: Ảnh (Layout Gallery giữ nguyên) -->
    <div style="flex: 1; padding: 10px;">
        
        <!-- Ảnh chính (lớn) -->
        <img id="main-product-image" 
             src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
             style="width: 100%; border: 1px solid #eee; border-radius: 5px;">
        
        <!-- Thư viện ảnh (thumbnails) (Style giữ nguyên) -->
        <div class="thumbnail-gallery" style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;">
            
            <!-- Thumbnail của ảnh chính -->
            <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
                 alt="Thumbnail" 
                 style="width: 80px; height: 80px; border: 2px solid #007bff; cursor: pointer; border-radius: 4px;"
                 onclick="document.getElementById('main-product-image').src=this.src;">

            <!-- Lặp qua các ảnh phụ (biến $product_images từ Controller) -->
            <?php foreach ($product_images as $image): ?>
                <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($image['image_url']); ?>" 
                     alt="Thumbnail" 
                     style="width: 80px; height: 80px; border: 1px solid #ccc; cursor: pointer; border-radius: 4px;"
                     onclick="document.getElementById('main-product-image').src=this.src;">
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Cột bên phải: Thông tin (Layout giữ nguyên) -->
    <div style="flex: 1; padding: 10px;">
        
        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
        
        <p>Thương hiệu: <strong><?php echo htmlspecialchars($product['brand_name']); ?></strong></p>
        <p>Danh mục: <strong><?php echo htmlspecialchars($product['category_name']); ?></strong></p>
        
        <!-- Áp dụng class .price -->
        <h2 class="price" style="font-size: 2.2em;"><?php echo number_format($product['price']); ?> VND</h2>
        
        <p>Số lượng còn lại: <?php echo $product['quantity']; ?></p>
        
        <!-- Form Thêm vào giỏ (Áp dụng class) -->
        <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=add" class="cart-form-ajax" style="margin-top: 20px; display: flex; gap: 10px; max-width: 300px;">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            <div style="flex: 1;">
                <label for="quantity">Số lượng:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control">
            </div>
            <div style="align-self: flex-end;">
                <button type="submit" class="btn btn-primary">Thêm vào giỏ</button>
            </div>
        </form>
        
        <hr>
        <h3>Mô tả sản phẩm</h3>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); // nl2br để giữ xuống dòng ?></p>
        
        <!-- Hiển thị thông số (JSON) -->
        <h3>Thông số kỹ thuật</h3>
        <?php
            $specs = json_decode($product['specifications'], true);
            if ($specs):
        ?>
            <ul style="border: 1px solid #eee; padding: 15px; list-style-position: inside; border-radius: 5px;">
                <?php foreach ($specs as $key => $value): ?>
                    <li><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo htmlspecialchars($value); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Đang cập nhật...</p>
        <?php endif; ?>

        <!-- 
        ============================================================
         KHU VỰC ĐÁNH GIÁ (GĐ 19 - Người 1) (Áp dụng class)
        ============================================================
        -->
        <hr>
        <h3>Đánh giá Sản phẩm</h3>
        <div class="reviews-list" style="margin-bottom: 20px;">
            <?php
            // (Code lấy $reviews đã có)
            global $conn;
            if (!class_exists('Review')) {
                require_once ROOT_PATH . '/app/models/Review.php';
            }
            $reviewModel_detail = new Review($conn);
            
            // =========================================================
            // SỬA LỖI Ở ĐÂY: Gọi đúng hàm (bỏ 'Approved')
            $reviews = $reviewModel_detail->getReviewsByProductId($product['product_id']);
            // =========================================================
            
            ?>
            
            <?php if (empty($reviews)): ?>
                <p>Chưa có đánh giá nào cho sản phẩm này.</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <!-- (Giữ style đơn giản cho review item) -->
                    <div class="review-item" style="border-bottom: 1px solid #eee; margin-bottom: 15px; padding-bottom: 10px;">
                        <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                        <span style="color: orange; margin-left: 10px;">
                            <?php echo str_repeat('★', $review['rating']); ?>
                            <?php echo str_repeat('☆', 5 - $review['rating']); ?>
                        </span>
                        <p style="margin: 5px 0;"><?php echo htmlspecialchars($review['comment']); ?></p>
                        <small style="color: #888;"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Form Gửi Đánh giá (Áp dụng class) -->
        <hr>
        <h4>Gửi đánh giá của bạn</h4>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <p><a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=login">Đăng nhập</a> để đánh giá.</p>
        <?php else: ?>
            <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=review&action=submit">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                
                <div class="form-group">
                    <label for="rating">Rating (sao):</label>
                    <select name="rating" id="rating" class="form-control" required>
                        <option value="5">5 sao ★★★★★</option>
                        <option value="4">4 sao ★★★★☆</option>
                        <option value="3">3 sao ★★★☆☆</option>
                        <option value="2">2 sao ★★☆☆☆</option>
                        <option value="1">1 sao ★☆☆☆☆</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="comment">Bình luận:</label>
                    <textarea id="comment" name="comment" rows="4" class="form-control"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
            </form>
        <?php endif; ?>

    </div> <!-- Hết Cột bên phải (Thông tin) -->
</div> <!-- Hết .product-detail -->


<!-- 
============================================================
 SẢN PHẨM LIÊN QUAN (GĐ 16 - Người 3) (Áp dụng class)
============================================================
-->
<?php if (isset($related_products) && !empty($related_products)): ?>
<div class="related-products" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #007bff;">
    <h2 style="color: #007bff; margin-bottom: 15px;">Sản phẩm Liên quan</h2>
    
    <!-- Áp dụng .product-grid -->
    <div class="product-grid">
        <?php foreach ($related_products as $related_product): ?>
            
            <!-- Áp dụng .product-card -->
            <div class="product-card">
                <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($related_product['main_image']); ?>" 
                     alt="<?php echo htmlspecialchars($related_product['product_name']); ?>">
                
                <div class="product-card-body">
                    <small class="brand"><?php echo htmlspecialchars($related_product['brand_name']); ?></small>
                    <h3><?php echo htmlspecialchars($related_product['product_name']); ?></h3>
                    <p class="price"><?php echo number_format($related_product['price']); ?> VND</p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detail&id=<?php echo $related_product['product_id']; ?>" 
                       class="btn btn-secondary" style="width: 100%;">
                        Xem chi tiết
                    </a>
                </div>
            </div>
            
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>