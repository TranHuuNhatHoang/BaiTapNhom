<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Chi tiết Đơn hàng #<?php echo $order['order_id']; ?></h2>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders">Quay lại Danh sách Đơn hàng</a>
<hr>

<div style="display: flex; gap: 20px;">
    <div style="flex: 1;">
        <h3>Thông tin Đơn hàng</h3>
        <p><strong>Khách hàng:</strong> <?php echo htmlspecialchars($order['full_name']); ?> (<?php echo htmlspecialchars($order['email']); ?>)</p>
        <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
        <p><strong>Trạng thái:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
        <p><strong>Tổng tiền:</strong> <strong style="color: red;"><?php echo number_format($order['total_amount']); ?> VND</strong></p>
        <p><strong>Địa chỉ giao:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
        <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
        <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['notes'] ?? ''); ?></p>
    </div>
    
    <div style="flex: 2;">
        <h3>Các sản phẩm trong đơn</h3>
        <table border="1" style="width: 100%; border-collapse: collapse;">
            <thead> <tr> <th>Ảnh</th> <th>Tên sản phẩm</th> <th>Giá</th> <th>SL</th> <th>Thành tiền</th> </tr> </thead>
            <tbody>
                <?php foreach ($order_details as $item): ?>
                <tr>
                    <td><img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($item['main_image']); ?>" height="50"></td>
                    <td>
                        <?php echo htmlspecialchars($item['product_name']); ?><br>
                        <small><?php echo htmlspecialchars($item['brand_name']); ?></small>
                    </td>
                    <td><?php echo number_format($item['unit_price']); ?> VND</td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo number_format($item['unit_price'] * $item['quantity']); ?> VND</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>