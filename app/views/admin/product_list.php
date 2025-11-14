<?php 
/*
 * File trang Admin - Quản lý Sản phẩm (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .table, .btn, .btn-success, .btn-secondary, .btn-danger, .pagination
 *
 * Các biến được truyền từ AdminController@listProducts:
 * $products (mảng), $total_pages (int), $current_page (int)
 */
?>

<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Sản phẩm</h2>

<!-- 
============================================================
 THANH ĐIỀU HƯỚNG ADMIN (ĐÃ ÁP DỤNG CLASS .btn)
============================================================
-->
<div class="admin-nav" style="margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 10px;">
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin" class="btn btn-secondary">Tổng quan</a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts" class="btn btn-primary">Quản lý Sản phẩm</a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands" class="btn btn-secondary">Quản lý Thương hiệu</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCategories" class="btn btn-secondary">Quản lý Danh mục</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders" class="btn btn-secondary">Quản lý Đơn hàng</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers" class="btn btn-secondary">Quản lý Người dùng</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listReviews" class="btn btn-secondary">Đánh giá</a>
</div>
<hr>

<!-- 
============================================================
 NÚT THÊM MỚI (ĐÃ ÁP DỤNG CLASS .btn)
============================================================
-->
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=create" class="btn btn-success" style="margin-bottom: 15px;">
    + Thêm Sản phẩm mới
</a>

<!-- 
============================================================
 BẢNG DANH SÁCH SẢN PHẨM (ĐÃ ÁP DỤNG CLASS .table)
============================================================
-->
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Tên Sản phẩm</th>
            <th>Thương hiệu</th>
            <th>Danh mục</th>
            <th>Giá</th>
            <th>SL</th>
            <th style="min-width: 140px;">Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($products)): ?>
            <tr><td colspan="8">Không có sản phẩm nào.</td></tr>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo $product['product_id']; ?></td>
                <td>
                    <?php if (!empty($product['main_image'])): ?>
                        <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td><?php echo htmlspecialchars($product['brand_name']); ?></td>
                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                <td><?php echo number_format($product['price']); ?></td>
                <td><?php echo $product['quantity']; ?></td>
                
                <!-- CỘT HÀNH ĐỘNG (ĐÃ ÁP DỤNG CLASS .btn) -->
                <td style="display: flex; flex-direction: column; gap: 5px; justify-content: center;">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=edit&id=<?php echo $product['product_id']; ?>" 
                       class="btn btn-secondary" style="font-size: 0.9em; padding: 5px;">
                        Sửa
                    </a>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=delete&id=<?php echo $product['product_id']; ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');" 
                       class="btn btn-danger" style="font-size: 0.9em; padding: 5px;">
                        Xóa
                    </a>
                    <?php
                    // Lấy URL hiện tại (bao gồm cả ?page=3) và mã hóa nó
                    $current_list_url = urlencode($_SERVER['REQUEST_URI']);
                    ?>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=manageImages&product_id=<?php echo $product['product_id']; ?>&return_url=<?php echo $current_list_url; ?>" 
                    class="btn btn-primary" style="font-size: 0.9em; padding: 5px;">Quản lý Ảnh</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- 
============================================================
 PHÂN TRANG (PAGINATION) (ĐÃ ÁP DỤNG CLASS .btn)
 (Code này lấy từ AdminController@listProducts của GĐ trước)
============================================================
-->
<hr>
<div class="pagination" style="text-align: center; margin-top: 20px;">
    <?php if (isset($total_pages) && $total_pages > 1): ?>
        
        <!-- Link Trang trước -->
        <?php if ($current_page > 1): ?>
            <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts&page=<?php echo $current_page - 1; ?>"
               class="btn btn-secondary">&laquo; Trước</a>
        <?php endif; ?>

        <!-- Hiển thị các trang -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts&page=<?php echo $i; ?>" 
               class="btn <?php echo $i == $current_page ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
        
        <!-- Link Trang sau -->
        <?php if ($current_page < $total_pages): ?>
            <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts&page=<?php echo $current_page + 1; ?>"
               class="btn btn-secondary">Sau &raquo;</a>
        <?php endif; ?>
        
    <?php endif; ?>
</div>