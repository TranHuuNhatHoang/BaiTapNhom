<?php 
/*
 * File trang Admin - Quản lý Đánh giá (ĐÃ DỌN DẸP CSS)
 * (Phiên bản ĐÚNG - KHÔNG có tính năng Phê duyệt)
 * Áp dụng: .table, .btn, .btn-primary, .btn-secondary, .btn-danger
 *
 * Các biến được truyền từ AdminController@listReviews:
 * $reviews (mảng)
 */
?>

<h1>Trang Quản trị Admin</h1>
<h2>Quản lý Đánh giá (Reviews)</h2>

<!-- 
============================================================
 THANH ĐIỀU HƯỚNG ADMIN (ĐÃ ÁP DỤNG CLASS .btn)
============================================================
-->
<div class="admin-nav" style="margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 10px;">
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin" class="btn btn-secondary">Tổng quan</a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts" class="btn btn-secondary">Quản lý Sản phẩm</a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands" class="btn btn-secondary">Quản lý Thương hiệu</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCategories" class="btn btn-secondary">Quản lý Danh mục</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders" class="btn btn-secondary">Quản lý Đơn hàng</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers" class="btn btn-secondary">Quản lý Người dùng</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listReviews" class="btn btn-primary">Đánh giá</a>
</div>
<hr>

<!-- 
============================================================
 BẢNG DANH SÁCH (ĐÃ ÁP DỤNG CLASS .table)
 (Đã xóa cột Trạng thái bị lỗi)
============================================================
-->
<table class="table">
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
                <td style="white-space: nowrap;"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></td>
                <td>
                    <!-- 
                    ============================================================
                     CỘT HÀNH ĐỘNG (ĐÃ ÁP DỤNG CLASS .btn)
                    ============================================================
                    -->
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteReview&id=<?php echo $review['review_id']; ?>" 
                       onclick="return confirm('Bạn có chắc muốn XÓA đánh giá này?');" 
                       class="btn btn-danger" style="font-size: 0.9em; padding: 5px;">
                        Xóa
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>