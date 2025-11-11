<?php 
/*
 * File trang Chi tiết Đơn hàng (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .table, .price, .btn
 */
?>

<!-- (Biến $order và $order_details được truyền từ Controller) -->
<h1>Chi tiết Đơn hàng #<?php echo $order['order_id']; ?></h1>

<!-- Áp dụng class .btn -->
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=history" class="btn btn-secondary" style="margin-bottom: 15px;">
    &laquo; Quay lại Lịch sử
</a>
<hr>

<div class="order-detail-container" style="display: flex; flex-wrap: wrap; gap: 20px;">
    
    <!-- Cột trái: Thông tin giao hàng -->
    <div style="flex: 1; min-width: 300px; background-color: #f9f9f9; padding: 20px; border-radius: 8px;">
        <h3>Thông tin Đơn hàng</h3>
        
        <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
        <p><strong>Trạng thái:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
        <p><strong>Địa chỉ giao:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
        <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
        <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['notes'] ?? 'Không có'); ?></p>
        
        <hr>
        
        <!-- HIỂN THỊ CHI TIẾT THANH TOÁN (GĐ 18) -->
        <h4 style="display: flex; justify-content: space-between;">
            <span>Tiền hàng:</span>
            <!-- 
            Lưu ý: total_amount là TỔNG CUỐI (đã giảm).
            Tiền hàng = total_amount + discount_applied
            -->
            <span>
                <?php echo number_format($order['total_amount'] + $order['discount_applied']); ?> VND
            </span>
        </h4>
        
        <?php if (isset($order['coupon_code']) && $order['discount_applied'] > 0): ?>
            <h4 style="display: flex; justify-content: space-between; color: green;">
                <span>Giảm giá (<?php echo htmlspecialchars($order['coupon_code']); ?>):</span>
                <span>-<?php echo number_format($order['discount_applied']); ?> VND</span>
            </h4>
        <?php endif; ?>
        
        <h3 style="display: flex; justify-content: space-between; margin-top: 10px; border-top: 1px solid #ccc; padding-top: 10px;">
            <span>Tổng thanh toán:</span>
            <!-- Áp dụng class .price -->
            <span class="price"><?php echo number_format($order['total_amount']); ?> VND</span>
        </h3>
    </div>
    
    <!-- Cột phải: Sản phẩm đã mua -->
    <div style="flex: 2; min-width: 400px;">
        <h3>Các sản phẩm đã mua</h3>
        
        <!-- Áp dụng class .table -->
        <table class="table">
            <thead> 
                <tr> 
                    <th>Ảnh</th> 
                    <th>Tên sản phẩm</th> 
                    <th>Giá</th> 
                    <th>SL</th> 
                    <th>Thành tiền</th> 
                </tr> 
            </thead>
            <tbody>
                <?php foreach ($order_details as $item): ?>
                <tr>
                    <td>
                        <!-- (Giữ style cho ảnh nhỏ) -->
                        <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($item['main_image']); ?>" 
                             height="50" style="border-radius: 4px;">
                    </td>
                    <td>
                        <?php echo htmlspecialchars($item['product_name']); ?><br>
                        <small style="color: #555;"><?php echo htmlspecialchars($item['brand_name']); ?></small>
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