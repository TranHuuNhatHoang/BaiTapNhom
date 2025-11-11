<h1>Giỏ hàng của bạn</h1>

<?php if (empty($cart_items)): ?>
    <p>Giỏ hàng của bạn đang rỗng.</p>
    <a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Tiếp tục mua sắm</a>
<?php else: ?>

    <table class="table">
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
                            <input type="number" name="quantity" value="<?php echo $item['quantity_in_cart']; ?>" min="0" class="form-control">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </form>
                    </td>
                    
                    <td><?php echo number_format($item['price'] * $item['quantity_in_cart']); ?> VND</td>
                    
                    <td>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=remove&id=<?php echo $item['product_id']; ?>"
                           onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');"
                           class="btn btn-danger">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="cart-summary-container">
        
        <div class="coupon-form">
            <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=applyCoupon">
                <label for="coupon_code">Mã giảm giá:</label><br>
                <input type="text" id="coupon_code" name="coupon_code" 
                       value="<?php echo htmlspecialchars($coupon_code ?? ''); ?>" 
                       placeholder="Nhập mã..." class="form-control">
                <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                <button type="submit" class="btn btn-primary">Áp dụng</button>
            </form>
        </div>
        
        <div class="total-summary">
            <h4>Tổng tiền hàng: <?php echo number_format($total_price); ?> VND</h4>
            
            <?php if (isset($coupon_code) && $discount_amount > 0): ?>
                <h4 class="discount-applied">
                    Giảm giá (<?php echo htmlspecialchars($coupon_code); ?>): 
                    -<?php echo number_format($discount_amount); ?> VND
                    <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=removeCoupon" 
                       class="btn btn-danger btn-sm">[Xóa]</a>
                </h4>
            <?php endif; ?>
            
            <h2>
                Tổng thanh toán: 
                <span class="price"><?php echo number_format($final_price); ?> VND</span>
            </h2>
            
            <a href="<?php echo BASE_URL; ?>index.php?controller=checkout&action=index" 
               class="btn btn-primary btn-lg">
                Tiến hành Thanh toán
            </a>
        </div>
    </div>

<?php endif; ?>