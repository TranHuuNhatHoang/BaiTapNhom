<?php 
/*
 * File trang Admin - Chi tiết Đơn hàng (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .table, .btn, .btn-secondary, .price
 *
 * Các biến được truyền từ AdminController@orderDetail:
 * $order (mảng), $order_details (mảng)
 */
?>

<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Chi tiết Đơn hàng #<?php echo $order['order_id']; ?></h2>

<!-- Áp dụng class .btn -->
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders" class="btn btn-secondary" style="margin-bottom: 15px;">
    &laquo; Quay lại Danh sách Đơn hàng
</a>
<hr>

<!-- 
============================================================
 LAYOUT CHIA 2 CỘT (Giữ style flex)
============================================================
-->
<div class="order-detail-container" style="display: flex; flex-wrap: wrap; gap: 20px;">
    
    <!-- Cột trái: Thông tin giao hàng -->
    <div style="flex: 1; min-width: 300px; background-color: #f9f9f9; padding: 20px; border-radius: 8px;">
        <h3>Thông tin Đơn hàng</h3>
        
        <p><strong>Khách hàng:</strong> <?php echo htmlspecialchars($order['full_name']); ?> (<?php echo htmlspecialchars($order['email']); ?>)</p>
        <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
        <p><strong>Trạng thái:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
        <p><strong>Địa chỉ giao:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
        <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
        <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['notes'] ?? 'Không có'); ?></p>
        
        <hr>
        
        <!-- HIỂN THỊ CHI TIẾT THANH TOÁN (GĐ 18) -->
        <h4 style="display: flex; justify-content: space-between;">
            <span>Tiền hàng:</span>
            <!-- (Tiền hàng = tổng cuối + tiền giảm giá) -->
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
        <h3>Các sản phẩm trong đơn</h3>
        
        <!-- 
        ============================================================
         BẢNG SẢN PHẨM (ĐÃ ÁP DỤNG CLASS .table)
        ============================================================
        -->
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
                             alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                             height="50" style="border-radius: 4px; object-fit: cover; width: 50px;">
                    </td>
                    <td>
                        <?php echo htmlspecialchars($item['product_name']); ?><br>
                        <small style="color: #555;"><?php echo htmlspecialchars($item['brand_name']); ?></small>
                    </td>
                    <td style="white-space: nowrap;"><?php echo number_format($item['unit_price']); ?> VND</td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td style="white-space: nowrap;"><?php echo number_format($item['unit_price'] * $item['quantity']); ?> VND</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>