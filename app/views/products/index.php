<?php 
/*
 * File này BÂY GIỜ là trang "Tất cả Sản phẩm"
 * (Đã XÓA Banner và SP Nổi bật)
 *
 * Các biến được truyền từ ProductController@index:
 * $products, $total_pages, $current_page, $sort, $price_range
 */
?>

<div class="product-list">

    <!-- 
    ============================================================
     ĐÃ XÓA BANNER VÀ SP NỔI BẬT KHỎI FILE NÀY
    ============================================================
    -->

    <!-- 
    ============================================================
     KHỐI LỌC & SẮP XẾP (Cập nhật Form)
    ============================================================
    -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="font-size: 1.8em;">Tất cả Sản phẩm</h2>
        
        <form method="GET" action="<?php echo BASE_URL; ?>index.php" style="display: flex; gap: 10px; align-items: center;">
            
            <!-- THÊM MỚI 2 DÒNG: Giữ controller và action khi lọc -->
            <input type="hidden" name="controller" value="product">
            <input type="hidden" name="action" value="index">
            
            <div>
                <label for="price" style="font-weight: 500;">Giá:</label>
                <select id="price" name="price" class="form-control" style="width: auto;">
                    <option value="">Tất cả</option>
                    <option value="duoi-10" <?php echo ($price_range ?? '') == 'duoi-10' ? 'selected' : ''; ?>>
                        Dưới 10 triệu
                    </option>
                    <option value="10-20" <?php echo ($price_range ?? '') == '10-20' ? 'selected' : ''; ?>>
                        Từ 10 - 20 triệu
                    </option>
                    <option value="tren-20" <?php echo ($price_range ?? '') == 'tren-20' ? 'selected' : ''; ?>>
                        Trên 20 triệu
                    </option>
                </select>
            </div>
            
            <div>
                <label for="sort" style="font-weight: 500;">Sắp xếp:</label>
                <select id="sort" name="sort" class="form-control" style="width: auto;">
                    <option value="created_at DESC" <?php echo ($sort ?? '') == 'created_at DESC' ? 'selected' : ''; ?>>
                        Mới nhất
                    </option>
                    <option value="price ASC" <?php echo ($sort ?? '') == 'price ASC' ? 'selected' : ''; ?>>
                        Giá: Tăng dần
                    </option>
                    <option value="price DESC" <?php echo ($sort ?? '') == 'price DESC' ? 'selected' : ''; ?>>
                        Giá: Giảm dần
                    </option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Lọc</button>
        </form>
    </div>
    
    <hr style="margin-top: 0;">
    
    <!-- 
    ============================================================
     DANH SÁCH SẢN PHẨM CHÍNH (Giữ lại)
    ============================================================
    -->
    <?php if (empty($products)): ?>
        <p>Không tìm thấy sản phẩm nào phù hợp với bộ lọc của bạn.</p>
    <?php else: ?>
    
        <div class="product-grid">
            
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    
                    <div class="product-card-body">
                        <small class="brand"><?php echo htmlspecialchars($product['brand_name']); ?></small>
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        
                        <p style="font-size: 0.9em; color: #777; margin: 5px 0;">
                            Danh mục: <?php echo htmlspecialchars($product['category_name']); ?>
                        </p>

                        <p class="price">
                            <?php echo number_format($product['price']); ?> VND
                        </p>
                        
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>" 
                           class="btn btn-secondary" style="width: 100%; margin-bottom: 10px;">
                            Xem chi tiết</a>
                        
                        <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=add" class="cart-form-ajax" style="display: flex; gap: 5px;">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 70px;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Thêm vào giỏ</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

        </div> <!-- Hết .product-grid -->
        
        <!-- 
        ============================================================
         PHÂN TRANG (CẬP NHẬT LINK)
        ============================================================
        -->
        <hr>
        <div class="pagination" style="text-align: center; margin-top: 20px;">
            <?php if (isset($total_pages) && $total_pages > 1): ?>
                
                <?php
                // CẬP NHẬT: Thêm controller=product&action=index vào link
                $base_url = BASE_URL . "index.php?controller=product&action=index";
                $params = "&sort=" . urlencode($sort ?? '') . "&price=" . urlencode($price_range ?? '');
                ?>

                <!-- Link Trang trước -->
                <?php if ($current_page > 1): ?>
                    <a href="<?php echo $base_url; ?>&page=<?php echo $current_page - 1; ?><?php echo $params; ?>"
                       class="btn btn-secondary">&laquo; Trước</a>
                <?php endif; ?>

                <!-- Hiển thị các trang -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="<?php echo $base_url; ?>&page=<?php echo $i; ?><?php echo $params; ?>" 
                       class="btn <?php echo $i == $current_page ? 'btn-primary' : 'btn-secondary'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <!-- Link Trang sau -->
                <?php if ($current_page < $total_pages): ?>
                    <a href="<?php echo $base_url; ?>&page=<?php echo $current_page + 1; ?><?php echo $params; ?>"
                       class="btn btn-secondary">Sau &raquo;</a>
                <?php endif; ?>
                
            <?php endif; ?>
        </div>
        
    <?php endif; ?>
</div>