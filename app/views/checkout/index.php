<h1>Thanh toán Đơn hàng</h1>

<div style="display: flex; gap: 20px;">

    <div style="flex: 2;">
        <h3>Thông tin Giao hàng</h3>
        <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=checkout&action=placeOrder">
            
            <div style="margin-bottom: 10px;">
                <label for="full_name">Họ và Tên:</label><br>
                <input type="text" id="full_name" name="full_name" required style="width: 100%;"
                       value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>
            
            <div style="margin-bottom: 10px;">
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required style="width: 100%;"
                       value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>
            
            <div style="margin-bottom: 10px;">
                <label for="phone">Số điện thoại:</label><br>
                <input type="tel" id="phone" name="phone" required style="width: 100%;"
                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
            
            <div style="margin-bottom: 10px;">
                <label for="address">Địa chỉ:</label><br>
                <input type="text" id="address" name="address" required style="width: 100%;"
                       value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
            </div>
            
            <div style="margin-bottom: 10px;">
                <label for="province">Tỉnh/Thành phố:</label><br>
                <input type="text" id="province" name="province" style="width: 100%;"
                       value="<?php echo htmlspecialchars($user['province'] ?? ''); ?>">
            </div>

            <div style="margin-bottom: 10px;">
                <label for="notes">Ghi chú (Tùy chọn):</label><br>
                <textarea id="notes" name="notes" rows="3" style="width: 100%;"></textarea>
            </div>
            
            <hr>
            <h3>Phương thức Thanh toán</h3>
            <div>
                <input type="radio" id="cod" name="payment_method" value="cod" checked>
                <label for="cod">Thanh toán khi nhận hàng (COD)</label>
            </div>
            <div>
                <input type="radio" id="bank" name="payment_method" value="bank_transfer" disabled>
                <label for="bank">(Sắp có) Chuyển khoản ngân hàng</label>
            </div>

            <button type="submit" style="padding: 15px; background-color: green; color: white; margin-top: 20px;">
                 Xác nhận Đặt hàng
            </button>
            
        </form>
    </div>

   <!-- Cột phải: Tóm tắt giỏ hàng -->
    <div style="flex: 1; background-color: #f4f4f4; padding: 15px;">
        <h3>Tóm tắt Đơn hàng</h3>
        <hr>
        
        <?php foreach ($cart_items as $item): ?>
            <!-- (Code lặp qua sản phẩm...) -->
        <?php endforeach; ?>
        
        <hr>
        
        <!-- THÊM MỚI (Người 1): Hiển thị chi tiết giá -->
        <h4 style="display: flex; justify-content: space-between;">
            <span>Tổng tiền hàng:</span>
            <span><?php echo number_format($total_price); ?> VND</span>
        </h4>
        
        <?php if (isset($coupon_code) && $discount_amount > 0): ?>
            <h4 style="display: flex; justify-content: space-between; color: green;">
                <span>Giảm giá (<?php echo htmlspecialchars($coupon_code); ?>):</span>
                <span>-<?php echo number_format($discount_amount); ?> VND</span>
            </h4>
        <?php endif; ?>
        
        <hr>
        <h3 style="display: flex; justify-content: space-between;">
            <span>Tổng thanh toán:</span>
            <span style="color: red;"><?php echo number_format($final_price); ?> VND</span>
        </h3>
        <!-- KẾT THÚC THÊM MỚI -->
    </div>

</div>