<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Danh mục</h2>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin">Tổng quan</a> | 
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts">Sản phẩm</a> | 
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands">Thương hiệu</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCategories" style="font-weight: bold;">Danh mục</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders">Đơn hàng</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers">Người dùng</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listReviews">Đánh giá</a>
<hr>

<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=createCategory" style="background-color: green; color: white; padding: 10px;">
    + Thêm Danh mục mới
</a>
<hr>
<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead> <tr> <th>ID</th> <th>Tên Danh mục</th> <th>Mô tả</th> <th>Hành động</th> </tr> </thead>
    <tbody>
        <?php foreach ($categories as $category): ?>
        <tr>
            <td><?php echo $category['category_id']; ?></td>
            <td><?php echo htmlspecialchars($category['category_name']); ?></td>
            <td><?php echo htmlspecialchars($category['description'] ?? ''); ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=editCategory&id=<?php echo $category['category_id']; ?>">Sửa</a> |
                <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteCategory&id=<?php echo $category['category_id']; ?>" onclick="return confirm('Bạn có chắc?');" style="color: red;">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>