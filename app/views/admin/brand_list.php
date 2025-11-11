<?php 
/*
 * File trang Admin - Quản lý Thương hiệu (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .table, .btn, .btn-primary, .btn-secondary, .btn-success, .btn-danger
 *
 * Các biến được truyền từ AdminController@listBrands:
 * $brands (mảng)
 */
?>

<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Thương hiệu</h2>

<!-- 
============================================================
 THANH ĐIỀU HƯỚNG ADMIN (ĐÃ ÁP DỤNG CLASS .btn)
============================================================
-->
<div class="admin-nav" style="margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 10px;">
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin" class="btn btn-secondary">Tổng quan</a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts" class="btn btn-secondary">Quản lý Sản phẩm</a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands" class="btn btn-primary">Quản lý Thương hiệu</a>
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
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=createBrand" class="btn btn-success" style="margin-bottom: 15px;">
    + Thêm Thương hiệu mới
</a>

<!-- 
============================================================
 BẢNG DANH SÁCH (ĐÃ ÁP DỤNG CLASS .table)
============================================================
-->
<table class="table">
    <thead> 
        <tr> 
            <th>ID</th> 
            <th>Logo</th> 
            <th>Tên Thương hiệu</th> 
            <th>Mô tả</th>
            <th>Hành động</th> 
        </tr> 
    </thead>
    <tbody>
        <?php if (empty($brands)): ?>
            <tr><td colspan="5">Chưa có thương hiệu nào.</td></tr>
        <?php else: ?>
            <?php foreach ($brands as $brand): ?>
            <tr>
                <td><?php echo $brand['brand_id']; ?></td>
                <td>
                    <?php if(!empty($brand['logo'])): ?>
                        <!-- (Giữ style cho ảnh nhỏ) -->
                        <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($brand['logo']); ?>" 
                             alt="<?php echo htmlspecialchars($brand['brand_name']); ?>"
                             height="30" style="object-fit: contain;">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($brand['brand_name']); ?></td>
                <td><?php echo htmlspecialchars($brand['description'] ?? ''); ?></td>
                <td>
                    <!-- Áp dụng class .btn -->
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=editBrand&id=<?php echo $brand['brand_id']; ?>" 
                       class="btn btn-secondary" style="font-size: 0.9em; padding: 5px;">
                       Sửa
                    </a>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteBrand&id=<?php echo $brand['brand_id']; ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa thương hiệu này?');" 
                       class="btn btn-danger" style="font-size: 0.9em; padding: 5px;">
                       Xóa
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>