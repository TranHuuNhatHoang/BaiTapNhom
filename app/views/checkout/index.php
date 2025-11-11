<h1>Thanh toán Đơn hàng</h1>

<div class="checkout-container">

    <div class="checkout-form-column">
        <h3>Thông tin Giao hàng</h3>
        <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=checkout&action=placeOrder">
            
            <div class="form-group">
                <label for="full_name">Họ và Tên:</label><br>
                <input type="text" id="full_name" name="full_name" required class="form-control"
                       value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required class="form-control"
                       value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Số điện thoại:</label><br>
                <input type="tel" id="phone" name="phone" required class="form-control"
                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Địa chỉ:</label><br>
                <input type="text" id="address" name="address" required class="form-control"
                       value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="province">Tỉnh/Thành phố:</label><br>
                <input type="text" id="province" name="province" class="form-control"
                       value="<?php echo htmlspecialchars($user['province'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="notes">Ghi chú (Tùy chọn):</label><br>
                <textarea id="notes" name="notes" rows="3" class="form-control"></textarea>
            </div>
            
            <hr>
            <h3>Phương thức Thanh toán</h3>
            <div class="form-group">
                <input type="radio" id="cod" name="payment_method" value="cod" checked>
                <label for="cod">Thanh toán khi nhận hàng (COD)</label>
            </div>
            <div class="form-group">
                <input type="radio" id="bank" name="payment_method" value="bank_transfer" disabled>
                <label for="bank">(Sắp có) Chuyển khoản ngân hàng</label>
            </div>

            <button type="submit" class="btn btn-primary">
                 Xác nhận Đặt hàng
            </button>
            
        </form>
    </div>

   <div class="checkout-summary-column">
        <h3>Tóm tắt Đơn hàng</h3>
        <hr>
        
        <?php foreach ($cart_items as $item): ?>
            <?php endforeach; ?>
        
        <hr>
        
        <h4>
            <span>Tổng tiền hàng:</span>
            <span><?php echo number_format($total_price); ?> VND</span>
        </h4>
        
        <?php if (isset($coupon_code) && $discount_amount > 0): ?>
            <h4 class="discount-applied">
                <span>Giảm giá (<?php echo htmlspecialchars($coupon_code); ?>):</span>
                <span>-<?php echo number_format($discount_amount); ?> VND</span>
            </h4>
        <?php endif; ?>
        
        <hr>
        <h3>
            <span>Tổng thanh toán:</span>
            <span class="price"><?php echo number_format($final_price); ?> VND</span>
        </h3>
        </div>

</div>