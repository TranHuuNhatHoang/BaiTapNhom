<?php 
/*
 * File trang Admin - Quản lý Đơn hàng (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .table, .btn, .btn-primary, .btn-secondary, .form-control
 *
 * Các biến được truyền từ AdminController@listOrders:
 * $orders (mảng)
 */
?>

<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Đơn hàng</h2>

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
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders" class="btn btn-primary">Quản lý Đơn hàng</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers" class="btn btn-secondary">Quản lý Người dùng</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listReviews" class="btn btn-secondary">Đánh giá</a>
</div>
<hr>

<!-- 
============================================================
 BẢNG DANH SÁCH ĐƠN HÀNG (ĐÃ ÁP DỤNG CLASS .table)
============================================================
-->
<table class="table">
    <thead>
        <tr>
            <th>Mã ĐH</th>
            <th>Tên Khách hàng</th>
            <th>Email</th>
            <th>Điện thoại</th>
            <th>Địa chỉ</th>
            <th>Tổng tiền</th>
            <th style="min-width: 220px;">Trạng thái</th> <!-- Tăng độ rộng cột này -->
            <th>Ngày đặt</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($orders)): ?>
            <!-- Cập nhật colspan = 9 -->
            <tr><td colspan="9">Chưa có đơn hàng nào.</td></tr>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['order_id']; ?></td>
                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                <td><?php echo htmlspecialchars($order['email']); ?></td>
                <td><?php echo htmlspecialchars($order['shipping_phone']); ?></td>
                <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                <td style="white-space: nowrap;"><?php echo number_format($order['total_amount']); ?> VND</td>
                <td>
                    <!-- 
                    ============================================================
                     FORM CẬP NHẬT TRẠNG THÁI (ĐÃ ÁP DỤNG CLASS)
                    ============================================================
                    -->
                    <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=updateOrderStatus" 
                          style="margin: 0; display: flex; gap: 5px;">
                        
                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                        
                        <!-- Áp dụng class .form-control -->
                        <select name="new_status" class="form-control" style="padding: 5px; height: 36px;">
                            <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                            <option value="paid" <?php echo $order['order_status'] == 'paid' ? 'selected' : ''; ?>>Đã thanh toán</option>
                            <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Đang giao</option>
                            <option value="completed" <?php echo $order['order_status'] == 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                            <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                        </select>
                        
                        <!-- Áp dụng class .btn -->
                        <button type="submit" class="btn btn-primary" style="font-size: 0.9em; padding: 5px 10px;">Lưu</button>
                    </form>
                </td>
                <td style="white-space: nowrap;"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                
                <!-- CỘT HÀNH ĐỘNG (ĐÃ ÁP DỤNG CLASS .btn) -->
                <td>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=orderDetail&id=<?php echo $order['order_id']; ?>"
                       class="btn btn-secondary" style="font-size: 0.9em; padding: 5px;">
                        Xem
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>