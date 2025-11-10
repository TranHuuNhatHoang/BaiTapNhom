<a href="<?php echo BASE_URL; ?>">Quay lại trang chủ</a>
<hr>

<div class="product-detail" style="display: flex;">
    
    <div style="flex: 1; padding: 20px;">
        
        <img id="main-product-image" 
             src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
             style="width: 100%; border: 1px solid #eee;">
        
        <div class="thumbnail-gallery" style="margin-top: 10px; display: flex; gap: 10px;">
            
            <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
                 alt="Thumbnail" 
                 style="width: 80px; height: 80px; border: 2px solid blue; cursor: pointer;"
                 onclick="document.getElementById('main-product-image').src=this.src;">

            <?php foreach ($product_images as $image): ?>
                <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($image['image_url']); ?>" 
                     alt="Thumbnail" 
                     style="width: 80px; height: 80px; border: 1px solid #ccc; cursor: pointer;"
                     onclick="document.getElementById('main-product-image').src=this.src;">
            <?php endforeach; ?>
        </div>
    </div>

    <div style="flex: 1; padding: 20px;">
        
        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
        
        <p>Thương hiệu: <strong><?php echo htmlspecialchars($product['brand_name']); ?></strong></p>
        <p>Danh mục: <strong><?php echo htmlspecialchars($product['category_name']); ?></strong></p>
        
        <h2 style="color: red;"><?php echo number_format($product['price']); ?> VND</h2>
        
        <p>Số lượng còn lại: <?php echo $product['quantity']; ?></p>
        
        <hr>
        <h3>Mô tả sản phẩm</h3>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        
        <h3>Thông số kỹ thuật</h3>
        <?php
            $specs = json_decode($product['specifications'], true);
            if ($specs):
        ?>
            <ul style="border: 1px solid #eee; padding: 15px;">
                <?php foreach ($specs as $key => $value): ?>
                    <li><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo htmlspecialchars($value); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Đang cập nhật...</p>
        <?php endif; ?>

        <hr>
        <h3>Đánh giá Sản phẩm</h3>
        <div class="reviews-list">
            <?php
            // Lấy reviews (Code này đáng lẽ nên ở Controller, 
            // nhưng để tạm đây cho đơn giản)
            global $conn;
            if (!class_exists('Review')) {
                require_once ROOT_PATH . '/app/models/Review.php';
            }
            $reviewModel = new Review($conn);
            $reviews = $reviewModel->getReviewsByProductId($product['product_id']);
            ?>
            
            <?php if (empty($reviews)): ?>
                <p>Chưa có đánh giá nào cho sản phẩm này.</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div style="border-bottom: 1px solid #eee; margin-bottom: 15px; padding-bottom: 10px;">
                        <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                        <span style="color: orange; margin-left: 10px;">
                            <?php echo str_repeat('★', $review['rating']); // Hiển thị 5 sao ?>
                            <?php echo str_repeat('☆', 5 - $review['rating']); ?>
                        </span>
                        <p style="margin: 5px 0;"><?php echo htmlspecialchars($review['comment']); ?></p>
                        <small style="color: #888;"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <hr>
        <h4>Gửi đánh giá của bạn</h4>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <p><a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=login">Đăng nhập</a> để đánh giá.</p>
        <?php else: ?>
            <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=review&action=submit">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <div>
                    <label>Rating (sao):</label><br>
                    <select name="rating" required>
                        <option value="5">5 sao ★★★★★</option>
                        <option value="4">4 sao ★★★★☆</option>
                        <option value="3">3 sao ★★★☆☆</option>
                        <option value="2">2 sao ★★☆☆☆</option>
                        <option value="1">1 sao ★☆☆☆☆</option>
                    </select>
                </div>
                <div style="margin-top: 10px;">
                    <label for="comment">Bình luận:</label><br>
                    <textarea id="comment" name="comment" rows="4" style="width: 100%;"></textarea>
                </div>
                <button type="submit" style="margin-top: 10px;">Gửi đánh giá</button>
            </form>
        <?php endif; ?>
        
    </div> 
</div>

<!-- 

 THÊM MỚI (Người 3 - GĐ16): Sản phẩm Liên quan

-->
<?php if (isset($related_products) && !empty($related_products)): ?>
<div class="related-products" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #007bff;">
    <h2 style="color: #007bff;">Sản phẩm Liên quan</h2>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <?php foreach ($related_products as $related_product): ?>
            <!-- (Đây là code hiển thị 1 SP, copy từ index.php) -->
            <div class="product-item" style="border: 1px solid #ccc; padding: 10px;">
                <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($related_product['main_image']); ?>" 
                     alt="<?php echo htmlspecialchars($related_product['product_name']); ?>" 
                     style="width: 100%; height: auto;">
                <small style="color: #555;"><?php echo htmlspecialchars($related_product['brand_name']); ?></small>
                <h3 style="margin: 5px 0; font-size: 1em;"><?php echo htmlspecialchars($related_product['product_name']); ?></h3>
                <p style="color: red; font-weight: bold; margin: 5px 0;">
                    <?php echo number_format($related_product['price']); ?> VND
                </p>
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detail&id=<?php echo $related_product['product_id']; ?>">
                    Xem chi tiết</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
<!-- KẾT THÚC THÊM MỚI -->