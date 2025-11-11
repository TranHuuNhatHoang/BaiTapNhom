<h1>Giỏ hàng của bạn</h1>

<?php if (empty($cart_items)): ?>
    <p>Giỏ hàng của bạn đang rỗng.</p>
    <a href="<?php echo BASE_URL; ?>">Tiếp tục mua sắm</a>
<?php else: ?>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Tên Sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
                <th>Xóa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td>
                        <img src="<?php echo BASE_URL; ?>public/images/<?php echo htmlspecialchars($item['main_image']); ?>" height="50">
                    </td>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo number_format($item['price']); ?> VND</td>
                    
                    <td>
                        <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=update">
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['quantity_in_cart']; ?>" min="0" style="width: 60px;">
                            <button type="submit">Cập nhật</button>
                        </form>
                    </td>
                    
                    <td><?php echo number_format($item['price'] * $item['quantity_in_cart']); ?> VND</td>
                    
                    <td>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=remove&id=<?php echo $item['product_id']; ?>"
                           onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');"
                           style="color: red;">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- CẬP NHẬT (Người 3): Thêm Form Coupon và Tổng tiền mới -->
    <div style="display: flex; justify-content: space-between; margin-top: 20px;">
        
        <!-- Cột trái: Form Mã giảm giá -->
        <div>
            <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=applyCoupon">
                <label for="coupon_code">Mã giảm giá:</label><br>
                <input type="text" id="coupon_code" name="coupon_code" 
                       value="<?php echo htmlspecialchars($coupon_code ?? ''); ?>" 
                       placeholder="Nhập mã...">
                <!-- Gửi tổng tiền để tính % -->
                <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                <button type="submit">Áp dụng</button>
            </form>
        </div>
        
        <!-- Cột phải: Chi tiết Tổng tiền -->
        <div style="text-align: right;">
            <h4>Tổng tiền hàng: <?php echo number_format($total_price); ?> VND</h4>
            
            <?php if (isset($coupon_code) && $discount_amount > 0): ?>
                <h4 style="color: green;">
                    Giảm giá (<?php echo htmlspecialchars($coupon_code); ?>): 
                    -<?php echo number_format($discount_amount); ?> VND
                    <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=removeCoupon" 
                       style="color: red; text-decoration: none;">[Xóa]</a>
                </h4>
            <?php endif; ?>
            
            <h2 style="margin-top: 10px;">
                Tổng thanh toán: 
                <span style="color: red;"><?php echo number_format($final_price); ?> VND</span>
            </h2>
            
            <a href="<?php echo BASE_URL; ?>index.php?controller=checkout&action=index" 
               style="background-color: blue; color: white; padding: 15px; text-decoration: none; margin-top: 10px; display: inline-block;">
                Tiến hành Thanh toán
            </a>
        </div>
    </div>

<?php endif; ?>