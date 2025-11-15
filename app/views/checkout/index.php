<?php 
/*
 * File trang Checkout (ĐÃ NÂNG CẤP VỚI 3 DROPDOWN)
 *
 * Các biến cũ được truyền từ CheckoutController@index:
 * $user, $cart_items, $total_price, $coupon_code, $discount_amount, $final_price
 *
 * BIẾN MỚI CẦN THÊM (từ logic bên dưới):
 * $provinces (mảng Tỉnh/Thành)
 */

// --- THÊM MỚI (BƯỚC 6.1): Lấy danh sách Tỉnh/Thành ---
global $conn;
if (!class_exists('Address')) {
    // (Tải model Address chúng ta vừa tạo ở Bước 5)
    require_once ROOT_PATH . '/app/models/Address.php';
}
$addressModel = new Address($conn);
$provinces = $addressModel->getProvinces(); // Lấy Tỉnh/Thành từ CSDL
?>

<h1>Thanh toán Đơn hàng</h1>

<div style="display: flex; flex-wrap: wrap; gap: 20px;">

    <!-- 
    ============================================================
     CỘT TRÁI: FORM (ĐÃ CẬP NHẬT ĐỊA CHỈ)
    ============================================================
    -->
    <div style="flex: 2; min-width: 400px;">
        <h3>Thông tin Giao hàng</h3>
        
        <!-- (Form này sẽ POST tới action=placeOrder) -->
        <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=checkout&action=placeOrder">
            
            <div class="form-group">
                <label for="full_name">Họ và Tên:</label>
                <input type="text" id="full_name" name="full_name" class="form-control" required
                       value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="tel" id="phone" name="phone" class="form-control" required
                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>

            <!-- 
            ============================================================
             CẬP NHẬT (BƯỚC 6.1): Thay thế Địa chỉ Text bằng Dropdown
            ============================================================
            -->
            
            <!-- 1. Dropdown Tỉnh/Thành phố -->
            <div class="form-group">
                <label for="province">Tỉnh/Thành phố:</label>
                <select id="province" name="province_id" class="form-control" required>
                    <option value="">-- Chọn Tỉnh/Thành --</option>
                    <?php foreach ($provinces as $province): ?>
                        <option value="<?php echo $province['province_id']; ?>">
                            <?php echo htmlspecialchars($province['province_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- 2. Dropdown Quận/Huyện (Sẽ được JS nạp vào) -->
            <div class="form-group">
                <label for="district">Quận/Huyện:</label>
                <select id="district" name="district_id" class="form-control" required>
                    <option value="">-- Vui lòng chọn Tỉnh/Thành trước --</option>
                </select>
            </div>
            
            <!-- 3. Dropdown Phường/Xã (Sẽ được JS nạp vào) -->
            <div class="form-group">
                <label for="ward">Phường/Xã:</label>
                <select id="ward" name="ward_code" class="form-control" required>
                    <option value="">-- Vui lòng chọn Quận/Huyện trước --</option>
                </select>
            </div>
            
            <!-- 4. Ô nhập Số nhà/Đường (Địa chỉ cụ thể) -->
            <div class="form-group">
                <label for="address">Địa chỉ (Số nhà, Tên đường):</label>
                <input type="text" id="address" name="address" class="form-control" required
                       placeholder="Ví dụ: 123 Nguyễn Trãi"
                       value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
            </div>
            <!-- KẾT THÚC CẬP NHẬT ĐỊA CHỈ -->

            <div class="form-group">
                <label for="notes">Ghi chú (Tùy chọn):</label>
                <textarea id="notes" name="notes" rows="3" class="form-control"></textarea>
            </div>
            
            <!-- Phương thức Thanh toán (Giữ nguyên) -->
            <div class="form-group">
                <h3>Phương thức Thanh toán</h3>
                <div>
                    <input type="radio" id="cod" name="payment_method" value="cod" checked>
                    <label for="cod">Thanh toán khi nhận hàng (COD)</label>
                </div>
                <div>
                    <input type="radio" id="bank" name="payment_method" value="bank_transfer" disabled>
                    <label for="bank">(Sắp có) Chuyển khoản ngân hàng</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 20px;">
                Xác nhận Đặt hàng
            </button>
            
        </form>
    </div>

    <!-- 
    ============================================================
     CỘT PHẢI: TÓM TẮT ĐƠN HÀNG (Giữ nguyên)
    ============================================================
    -->
    <div style="flex: 1; min-width: 300px; background-color: #f9f9f9; padding: 20px; border-radius: 8px;">
        <h3>Tóm tắt Đơn hàng</h3>
        <hr>
        
        <?php foreach ($cart_items as $item): ?>
            <div style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($item['main_image']); ?>" height="50" style="border-radius: 4px; width: 50px; object-fit: cover;">
                <div style="flex: 1;">
                    <?php echo htmlspecialchars($item['product_name']); ?><br>
                    <small>Số lượng: <?php echo $item['quantity_in_cart']; ?></small>
                </div>
                <div style="margin-left: auto; font-weight: bold; white-space: nowrap;">
                    <?php echo number_format($item['price'] * $item['quantity_in_cart']); ?> VND
                </div>
            </div>
        <?php endforeach; ?>
        
        <hr>
        
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
            <span class="price"><?php echo number_format($final_price); ?> VND</span>
        </h3>
    </div>

</div>