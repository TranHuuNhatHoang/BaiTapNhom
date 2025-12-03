<?php 
/*
 * File trang Admin - Quản lý Danh mục (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .table, .btn, .btn-primary, .btn-secondary, .btn-success, .btn-danger
 *
 * Các biến được truyền từ AdminController@listCategories:
 * $categories (mảng)
 */
?>

<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Danh mục</h2>

<!-- 
============================================================
 THANH ĐIỀU HƯỚNG ADMIN (ĐÃ ÁP DỤNG CLASS .btn)
============================================================
-->
<div class="admin-nav" style="margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 10px;">
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin" class="btn btn-secondary">Tổng quan</a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts" class="btn btn-secondary">Quản lý Sản phẩm</a> 
     <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCoupons" class="btn btn-secondary">Quản lý Mã Giảm Giá</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listContacts" class="btn btn-danger">
        Quản lý Liên hệ 
    </a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands" class="btn btn-secondary">Quản lý Thương hiệu</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCategories" class="btn btn-primary">Quản lý Danh mục</a>
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
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=createCategory" class="btn btn-success" style="margin-bottom: 15px;">
    + Thêm Danh mục mới
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
            <th>Tên Danh mục</th> 
            <th>Mô tả</th> 
            <th>Hành động</th> 
        </tr> 
    </thead>
    <tbody>
        <?php if (empty($categories)): ?>
            <tr><td colspan="4">Chưa có danh mục nào.</td></tr>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
            <tr>
                <td><?php echo $category['category_id']; ?></td>
                <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                <td><?php echo htmlspecialchars($category['description'] ?? ''); ?></td>
                <td>
                    <!-- Áp dụng class .btn -->
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=editCategory&id=<?php echo $category['category_id']; ?>" 
                       class="btn btn-secondary" style="font-size: 0.9em; padding: 5px;">
                       Sửa
                    </a>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteCategory&id=<?php echo $category['category_id']; ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa danh mục này?');" 
                       class="btn btn-danger" style="font-size: 0.9em; padding: 5px;">
                       Xóa
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>