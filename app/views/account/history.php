<h1>Lịch sử Đơn hàng</h1>
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=index">Quay lại Tài khoản</a>
<hr>
<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead> <tr> <th>Mã ĐH</th> <th>Ngày đặt</th> <th>Tổng tiền</th> <th>Trạng thái</th> </tr> </thead>
    <tbody>
        <?php if (empty($orders)): ?>
            <tr><td colspan="4">Bạn chưa có đơn hàng nào.</td></tr>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['order_id']; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                <td><?php echo number_format($order['total_amount']); ?> VND</td>
                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>