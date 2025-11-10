<div class="product-list">
    
    <h1>Thương hiệu: <?php echo htmlspecialchars($brand['brand_name']); ?></h1>
    <p>Tìm thấy <?php echo $total_products; ?> sản phẩm.</p>
    <div style="margin-bottom: 20px; text-align: right;">
        <form method="GET" action="<?php echo BASE_URL; ?>index.php">
            <input type="hidden" name="controller" value="product">
            <input type="hidden" name="action" value="brand">
            <input type="hidden" name="id" value="<?php echo $brand['brand_id']; ?>">
            
            <label for="sort">Sắp xếp theo:</label>
            <select id="sort" name="sort" onchange="this.form.submit()">
                <option value="created_at DESC" <?php echo ($sort ?? '') == 'created_at DESC' ? 'selected' : ''; ?>>Mới nhất</option>
                <option value="price ASC" <?php echo ($sort ?? '') == 'price ASC' ? 'selected' : ''; ?>>Giá: Tăng dần</option>
                <option value="price DESC" <?php echo ($sort ?? '') == 'price DESC' ? 'selected' : ''; ?>>Giá: Giảm dần</option>
            </select>
        </form>
    </div>
    <hr>
    
    <?php if (empty($products)): ?>
        <p>Thương hiệu này chưa có sản phẩm nào.</p>
    <?php else: ?>
    
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            
            <?php foreach ($products as $product): ?>
                <div class="product-item" style="border: 1px solid #ccc; padding: 10px;">
                    
                    <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
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
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>">
                        Xem chi tiết</a>
                    
                    <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=add" style="margin-top: 10px;">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" style="width: 50px;">
                        <button type="submit">Thêm vào giỏ</button>
                    </form>
                </div>
            <?php endforeach; ?>

        </div> <hr>
        <div class="pagination" style="text-align: center; margin-top: 20px;">
            <?php if (isset($total_pages) && $total_pages > 1): ?>
                
                <?php if ($current_page > 1): ?>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=brand&id=<?php echo $brand_id; ?>&page=<?php echo $current_page - 1; ?>">
                        &laquo; Trước
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=brand&id=<?php echo $brand_id; ?>&page=<?php echo $i; ?>" 
                       style="<?php echo $i == $current_page ? 'font-weight: bold;' : ''; ?> padding: 5px;">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=brand&id=<?php echo $brand_id; ?>&page=<?php echo $current_page + 1; ?>">
                        Sau &raquo;
                    </a>
                <?php endif; ?>
                
            <?php endif; ?>
        </div>
        
    <?php endif; ?>
</div>