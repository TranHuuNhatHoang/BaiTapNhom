<a href="<?php echo BASE_URL; ?>">Quay lại trang chủ</a>
<hr>

<div class="product-detail" style="display: flex;">
    
    <div style="flex: 1; padding: 20px;">
        <img src="<?php echo BASE_URL; ?>public/images/<?php echo htmlspecialchars($product['main_image']); ?>" 
             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
             style="width: 100%;">
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

    </div>
</div>