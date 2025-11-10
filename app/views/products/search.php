<h1>Tìm kiếm Sản phẩm</h1>

<!--

FORM TÌM KIẾM + LỌC + SẮP XẾP (ĐÃ GỘP)

-->

<div style="margin-bottom: 20px; border: 1px solid #eee; padding: 10px;">
<form method="GET" action="<?php echo BASE_URL; ?>index.php">
<!-- Input hidden để giữ controller, action -->
<input type="hidden" name="controller" value="product">
<input type="hidden" name="action" value="search">

    <div style="margin-bottom: 10px;">
        <label for="query">Tìm kiếm:</label><br>
        <input type="text" id="query" name="query" value="<?php echo htmlspecialchars($query ?? ''); ?>" 
               placeholder="Nhập tên laptop..." style="width: 300px; padding: 10px;">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="sort">Sắp xếp:</label>
        <select id="sort" name="sort">
            <option value="created_at DESC" <?php echo ($sort ?? '') == 'created_at DESC' ? 'selected' : ''; ?>>Mới nhất</option>
            <option value="price ASC" <?php echo ($sort ?? '') == 'price ASC' ? 'selected' : ''; ?>>Giá: Tăng dần</option>
            <option value="price DESC" <?php echo ($sort ?? '') == 'price DESC' ? 'selected' : ''; ?>>Giá: Giảm dần</option>
        </select>
        
        <label for="price" style="margin-left: 10px;">Giá:</label>
        <select id="price" name="price">
            <option value="">Tất cả</option>
            <option value="duoi-10" <?php echo ($price_range ?? '') == 'duoi-10' ? 'selected' : ''; ?>>Dưới 10 triệu</option>
            <option value="10-20" <?php echo ($price_range ?? '') == '10-20' ? 'selected' : ''; ?>>Từ 10 - 20 triệu</option>
            <option value="tren-20" <?php echo ($price_range ?? '') == 'tren-20' ? 'selected' : ''; ?>>Trên 20 triệu</option>
        </select>
    </div>
    
    <button type="submit" style="padding: 10px;">Lọc / Tìm kiếm</button>
</form>


</div>
<!-- KẾT THÚC FORM GỘP -->

<hr>

<div class="product-list">

<?php if (!empty($query)): ?>
    <h2>Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($query); ?>"</h2>
    <p>Tìm thấy <?php echo $total_products; ?> kết quả.</p>
    
    <?php if (empty($products)): ?>
        <p>Không tìm thấy sản phẩm nào phù hợp.</p>
    <?php else: ?>
        <!-- (Code hiển thị sản phẩm của bạn...) -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <?php foreach ($products as $product): ?>
                <div class="product-item" style="border: 1px solid #ccc; padding: 10px;">
                    <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                         style="width: 100%; height: auto;">
                    <small style="color: #555;"><?php echo htmlspecialchars($product['brand_name']); ?></small>
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p><?php echo number_format($product['price']); ?> VND</p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>">
                        Xem chi tiết</a>
                    <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=add" style="margin-top: 10px;">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" style="width: 50px;">
                        <button type="submit">Thêm vào giỏ</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- 
        ============================================================
         PHÂN TRANG (CẬP NHẬT LINK)
        ============================================================
        -->
        <hr>
        <div class="pagination" style="text-align: center; margin-top: 20px;">
            <?php if (isset($total_pages) && $total_pages > 1): ?>
                
                <?php if ($current_page > 1): ?>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=search&query=<?php echo urlencode($query ?? ''); ?>&page=<?php echo $current_page - 1; ?>&sort=<?php echo urlencode($sort ?? ''); ?>&price=<?php echo urlencode($price_range ?? ''); ?>">
                        &laquo; Trước
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <!-- Sửa Link: Phải giữ lại 'query', 'sort', và 'price' -->
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=search&query=<?php echo urlencode($query ?? ''); ?>&page=<?php echo $i; ?>&sort=<?php echo urlencode($sort ?? ''); ?>&price=<?php echo urlencode($price_range ?? ''); ?>" 
                       style="<?php echo $i == $current_page ? 'font-weight: bold;' : ''; ?> padding: 5px;">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=search&query=<?php echo urlencode($query ?? ''); ?>&page=<?php echo $current_page + 1; ?>&sort=<?php echo urlencode($sort ?? ''); ?>&price=<?php echo urlencode($price_range ?? ''); ?>">
                        Sau &raquo;
                    </a>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    <?php endif; ?>
    
<?php endif; ?>


</div>