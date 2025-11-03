<div class="product-list">
    <h1>Danh sách Sản phẩm</h1>
    
    <?php if (empty($products)): ?>
        <p>Hiện chưa có sản phẩm nào.</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            
            <?php foreach ($products as $product): ?>
                <div class="product-item" style="border: 1px solid #ccc; padding: 10px;">
                    
                    <img src="<?php echo BASE_URL; ?>public/images/<?php echo htmlspecialchars($product['main_image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                         style="width: 100%; height: auto;">
                    
                    <small style="color: #555;"><?php echo htmlspecialchars($product['brand_name']); ?></small>

                    <h3 style="margin: 5px 0;"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    
                    <p style="font-size: 0.9em; color: #777; margin: 5px 0;">
                        Danh mục: <?php echo htmlspecialchars($product['category_name']); ?>
                    </p>

                    <p style="color: red; font-weight: bold; margin: 5px 0;">
                        <?php echo number_format($product['price']); ?> VND
                    </p>
                    
                    <a href="#">Xem chi tiết</a>
                </div>
            <?php endforeach; ?>

        </div>
    <?php endif; ?>

</div>