<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Đơn hàng</h2>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin">Quản lý Sản phẩm</a>
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
        </tr>
    </thead>
    <tbody>
        <?php if (empty($orders)): ?>
            <tr><td colspan="8">Chưa có đơn hàng nào.</td></tr>
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
                    <strong style="color: orange;"><?php echo htmlspecialchars($order['order_status']); ?></strong>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>