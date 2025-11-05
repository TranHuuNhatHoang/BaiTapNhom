<h1 style="color: blue;">Trang Quản trị Admin</h1>

<div style="margin-bottom: 20px;">
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders" 
       style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold;">
        📦 Quản lý Đơn hàng
    </a>
</div>
<h2>Quản lý Sản phẩm</h2>

<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=create" style="background-color: green; color: white; padding: 10px; text-decoration: none;">
    + Thêm Sản phẩm mới
</a>
<hr>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Tên Sản phẩm</th>
            <th>Thương hiệu</th>
            <th>Danh mục</th>
            <th>Giá</th>
            <th>SL</th>
            <th>Hành động</th>
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
                        <img src="<?php echo BASE_URL; ?>public/images/<?php echo htmlspecialchars($product['main_image']); ?>" height="50">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td><?php echo htmlspecialchars($product['brand_name']); ?></td>
                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                <td><?php echo number_format($product['price']); ?></td>
                <td><?php echo $product['quantity']; ?></td>
                <td>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=edit&id=<?php echo $product['product_id']; ?>">
        Sửa
    </a> | 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=delete&id=<?php echo $product['product_id']; ?>" 
        onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');" 
        style="color: red;">
        Xóa
    </a>
</td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>