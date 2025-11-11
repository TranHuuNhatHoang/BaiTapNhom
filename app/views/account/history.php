<?php 
/*
 * File trang Lịch sử Đơn hàng (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .table, .btn, .btn-secondary
 */
?>

<!-- (Biến $orders được truyền từ Controller) -->
<h1>Lịch sử Đơn hàng</h1>

<!-- Áp dụng class .btn -->
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=index" class="btn btn-secondary" style="margin-bottom: 15px;">
    &laquo; Quay lại Tài khoản
</a>
<hr>

<!-- Áp dụng class .table -->
<table class="table">
    <thead>
        <tr>
            <th>Mã ĐH</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Địa chỉ giao</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($orders)): ?>
            <!-- Cập nhật colspan = 6 -->
            <tr><td colspan="6">Bạn chưa có đơn hàng nào.</td></tr>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['order_id']; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                
                <!-- 
                Lưu ý: total_amount trong CSDL (theo GĐ 18) 
                đã là tổng tiền CUỐI CÙNG (đã trừ giảm giá)
                -->
                <td><?php echo number_format($order['total_amount']); ?> VND</td>
                
                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                <td>
                    <!-- Áp dụng class .btn -->
                    <a href="<?php echo BASE_URL; ?>index.php?controller=account&action=orderDetail&id=<?php echo $order['order_id']; ?>" 
                       class="btn btn-secondary" style="font-size: 0.9em;">
                        Xem chi tiết
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>