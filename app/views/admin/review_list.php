<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Đánh giá (Reviews)</h2>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin">Tổng quan</a> | 
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts">Sản phẩm</a> | 
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands">Thương hiệu</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCategories">Danh mục</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders">Đơn hàng</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers">Người dùng</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listReviews" style="font-weight: bold;">Đánh giá</a>
<hr>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Sản phẩm</th>
            <th>Người đánh giá</th>
            <th>Rating (sao)</th>
            <th>Bình luận</th>
            <th>Ngày</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($reviews)): ?>
            <tr><td colspan="7">Chưa có đánh giá nào.</td></tr>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
            <tr>
                <td><?php echo $review['review_id']; ?></td>
                <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                <td><?php echo htmlspecialchars($review['full_name']); ?></td>
                <td><strong style="color: orange;"><?php echo $review['rating']; ?> ★</strong></td>
                <td><?php echo htmlspecialchars($review['comment']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></td>
                <td>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteReview&id=<?php echo $review['review_id']; ?>" 
                       onclick="return confirm('Bạn có chắc muốn XÓA đánh giá này?');" style="color: red;">
                        Xóa
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>