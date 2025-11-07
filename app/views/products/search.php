<h1>Tìm kiếm Sản phẩm</h1>

<form method="GET" action="<?php echo BASE_URL; ?>index.php">
    <input type="hidden" name="controller" value="product">
    <input type="hidden" name="action" value="search">
    
    <input type="text" name="query" value="<?php echo htmlspecialchars($query ?? ''); ?>" 
           placeholder="Nhập tên laptop..." style="width: 300px; padding: 10px;">
    <button type="submit" style="padding: 10px;">Tìm kiếm</button>
</form>
<hr>

<div class="product-list">
    
    <?php if (!empty($query)): ?>
        <h2>Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($query); ?>"</h2>
        <p>Tìm thấy <?php echo $total_products; ?> kết quả.</p>
        
        <?php if (empty($products)): ?>
            <p>Không tìm thấy sản phẩm nào phù hợp.</p>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <?php foreach ($products as $product): ?>
                    <div class="product-item" style="border: 1px solid #ccc; padding: 10px;">
                        <img src="<?php echo BASE_URL; ?>public/images/<?php echo htmlspecialchars($product['main_image']); ?>" ...>
                        <small><?php echo htmlspecialchars($product['brand_name']); ?></small>
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p><?php echo number_format($product['price']); ?> VND</p>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>">
                            Xem chi tiết</a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <hr>
            <div class="pagination" style="text-align: center; margin-top: 20px;">
                <?php if (isset($total_pages) && $total_pages > 1): ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=search&query=<?php echo urlencode($query); ?>&page=<?php echo $i; ?>" 
                           style="<?php echo $i == $current_page ? 'font-weight: bold;' : ''; ?> padding: 5px;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
</div>