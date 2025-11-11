<div class="product-list">

<h1>Danh mục: <?php echo htmlspecialchars($category['category_name']); ?></h1>
<p>Tìm thấy <?php echo $total_products; ?> sản phẩm.</p>

<div class="filter-sort-form">
    <form method="GET" action="<?php echo BASE_URL; ?>index.php">
        <input type="hidden" name="controller" value="product">
        <input type="hidden" name="action" value="category">
        <input type="hidden" name="id" value="<?php echo $category['category_id']; ?>">
        
        <label for="sort">Sắp xếp:</label>
        <select id="sort" name="sort" class="form-control">
            <option value="created_at DESC" <?php echo ($sort ?? '') == 'created_at DESC' ? 'selected' : ''; ?>>Mới nhất</option>
            <option value="price ASC" <?php echo ($sort ?? '') == 'price ASC' ? 'selected' : ''; ?>>Giá: Tăng dần</option>
            <option value="price DESC" <?php echo ($sort ?? '') == 'price DESC' ? 'selected' : ''; ?>>Giá: Giảm dần</option>
        </select>
        
        <label for="price">Giá:</label>
        <select id="price" name="price" class="form-control">
            <option value="">Tất cả</option>
            <option value="duoi-10" <?php echo ($price_range ?? '') == 'duoi-10' ? 'selected' : ''; ?>>Dưới 10 triệu</option>
            <option value="10-20" <?php echo ($price_range ?? '') == '10-20' ? 'selected' : ''; ?>>Từ 10 - 20 triệu</option>
            <option value="tren-20" <?php echo ($price_range ?? '') == 'tren-20' ? 'selected' : ''; ?>>Trên 20 triệu</option>
        </select>
        
        <button type="submit" class="btn btn-primary">Lọc</button>
    </form>
</div>
<hr>

<?php if (empty($products)): ?>
    <p>Danh mục này chưa có sản phẩm nào.</p>
<?php else: ?>

    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                
                <div class="product-card-body">
                    <small class="brand"><?php echo htmlspecialchars($product['brand_name']); ?></small>
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p>
                        Danh mục: <?php echo htmlspecialchars($product['category_name']); ?>
                    </p>
                    <p class="price">
                        <?php echo number_format($product['price']); ?> VND
                    </p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>" class="btn btn-secondary">
                        Xem chi tiết</a>
                    
                    <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=add">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" class="form-control">
                        <button type="submit" class="btn btn-primary">Thêm vào giỏ</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div> 

    <hr>
    <div class="pagination">
        <?php if (isset($total_pages) && $total_pages > 1): ?>
            
            <?php if ($current_page > 1): ?>
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=category&id=<?php echo $category_id; ?>&page=<?php echo $current_page - 1; ?>&sort=<?php echo urlencode($sort ?? ''); ?>&price=<?php echo urlencode($price_range ?? ''); ?>">
                    &laquo; Trước
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=category&id=<?php echo $category_id; ?>&page=<?php echo $i; ?>&sort=<?php echo urlencode($sort ?? ''); ?>&price=<?php echo urlencode($price_range ?? ''); ?>" 
                   class="<?php echo $i == $current_page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=category&id=<?php echo $category_id; ?>&page=<?php echo $current_page + 1; ?>&sort=<?php echo urlencode($sort ?? ''); ?>&price=<?php echo urlencode($price_range ?? ''); ?>">
                    Sau &raquo;
                </a>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>
    
<?php endif; ?>


</div>