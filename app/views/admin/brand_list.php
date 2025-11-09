<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Thương hiệu</h2>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin">Quản lý Sản phẩm</a> | 
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders">Quản lý Đơn hàng</a>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers">Quản lý Người dùng</a>
<hr>

<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=createBrand" style="background-color: green; color: white; padding: 10px;">
    + Thêm Thương hiệu mới
</a>
<hr>
<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead> <tr> <th>ID</th> <th>Logo</th> <th>Tên</th> <th>Hành động</th> </tr> </thead>
    <tbody>
        <?php foreach ($brands as $brand): ?>
        <tr>
            <td><?php echo $brand['brand_id']; ?></td>
            <td>
                <?php if(!empty($brand['logo'])): ?>
                    <img src="<?php echo BASE_URL; ?>public/images/brands/<?php echo $brand['logo']; ?>" height="30">
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($brand['brand_name']); ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=editBrand&id=<?php echo $brand['brand_id']; ?>">Sửa</a> |
                <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteBrand&id=<?php echo $brand['brand_id']; ?>" onclick="return confirm('Bạn có chắc?');" style="color: red;">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>