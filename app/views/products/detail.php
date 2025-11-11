<a href="<?php echo BASE_URL; ?>">Quay lại trang chủ</a>
<hr>

<div class="product-detail">
    
    <div>
        
        <img id="main-product-image" 
             src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
             style="width: 100%;"> <div class="thumbnail-gallery">
            
            <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
                 alt="Thumbnail" 
                 class="thumbnail-image active" onclick="document.getElementById('main-product-image').src=this.src;">

            <?php foreach ($product_images as $image): ?>
                <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($image['image_url']); ?>" 
                     alt="Thumbnail" 
                     class="thumbnail-image" onclick="document.getElementById('main-product-image').src=this.src;">
            <?php endforeach; ?>
        </div>
    </div>

    <div>
        
        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
        
        <p>Thương hiệu: <strong><?php echo htmlspecialchars($product['brand_name']); ?></strong></p>
        <p>Danh mục: <strong><?php echo htmlspecialchars($product['category_name']); ?></strong></p>
        
        <h2 class="price"><?php echo number_format($product['price']); ?> VND</h2>
        
        <p>Số lượng còn lại: <?php echo $product['quantity']; ?></p>
        
        <hr>
        <h3>Mô tả sản phẩm</h3>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        
        <h3>Thông số kỹ thuật</h3>
        <?php
            $specs = json_decode($product['specifications'], true);
            if ($specs):
        ?>
            <ul class="product-specs">
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
                    <div class="review-item">
                        <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                        <span class="star-rating">
                            <?php echo str_repeat('★', $review['rating']); // Hiển thị 5 sao ?>
                            <?php echo str_repeat('☆', 5 - $review['rating']); ?>
                        </span>
                        <p><?php echo htmlspecialchars($review['comment']); ?></p>
                        <small><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></small>
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
                <div class="form-group">
                    <label>Rating (sao):</label><br>
                    <select name="rating" required class="form-control">
                        <option value="5">5 sao ★★★★★</option>
                        <option value="4">4 sao ★★★★☆</option>
                        <option value="3">3 sao ★★★☆☆</option>
                        <option value="2">2 sao ★★☆☆☆</option>
                        <option value="1">1 sao ★☆☆☆☆</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Bình luận:</label><br>
                    <textarea id="comment" name="comment" rows="4" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
            </form>
        <?php endif; ?>
        
    </div> 
</div>

<?php if (isset($related_products) && !empty($related_products)): ?>
<div class="related-products">
    <h2>Sản phẩm Liên quan</h2>
    <div class="product-grid">
        <?php foreach ($related_products as $related_product): ?>
            <div class="product-card">
                <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($related_product['main_image']); ?>" 
                     alt="<?php echo htmlspecialchars($related_product['product_name']); ?>">
                
                <div class="product-card-body">
                    <small class="brand"><?php echo htmlspecialchars($related_product['brand_name']); ?></small>
                    <h3><?php echo htmlspecialchars($related_product['product_name']); ?></h3>
                    <p class="price">
                        <?php echo number_format($related_product['price']); ?> VND
                    </p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detail&id=<?php echo $related_product['product_id']; ?>" class="btn btn-secondary">
                        Xem chi tiết</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>