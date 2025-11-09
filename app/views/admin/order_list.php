<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Đơn hàng</h2>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin">Quản lý Sản phẩm</a>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers">Quản lý Người dùng</a>
<hr>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>Mã ĐH</th>
            <th>Tên Khách hàng</th>
            <th>Email</th>
            <th>Điện thoại</th>
            <th>Địa chỉ</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Ngày đặt</th>
            
            <th>Hành động</th>
            
        </tr>
    </thead>
    <tbody>
        <?php if (empty($orders)): ?>
            <tr><td colspan="9">Chưa có đơn hàng nào.</td></tr>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['order_id']; ?></td>
                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                <td><?php echo htmlspecialchars($order['email']); ?></td>
                <td><?php echo htmlspecialchars($order['shipping_phone']); ?></td>
                <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                <td><?php echo number_format($order['total_amount']); ?> VND</td>
                <td>
                    <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=updateOrderStatus" style="margin: 0;">
                       <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                       <select name="new_status">
                       <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Chờ xử lý (Pending)</option>
                       <option value="paid" <?php echo $order['order_status'] == 'paid' ? 'selected' : ''; ?>>Đã thanh toán (Paid)</option>
                       <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Đang giao (Shipped)</option>
                       <option value="completed" <?php echo $order['order_status'] == 'completed' ? 'selected' : ''; ?>>Hoàn thành (Completed)</option>
                       <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Đã hủy (Cancelled)</option>
                       </select>
                       <button type="submit" style="font-size: 0.8em;">Lưu</button>
                       </form>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                
                <td>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=orderDetail&id=<?php echo $order['order_id']; ?>">
                        Xem chi tiết
                    </a>
                </td>
                
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>