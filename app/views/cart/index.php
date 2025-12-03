<h1>Giỏ hàng của bạn</h1>

<?php display_flash_message(); ?>

<?php if (empty($cart_items)): ?>
    <p>Giỏ hàng của bạn đang rỗng.</p>
   <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="btn btn-primary">Tiếp tục mua sắm</a>
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
                        <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($item['main_image']); ?>" height="50">
                    </td>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo number_format($item['price']); ?> VND</td>
                    
                    <td>
                        <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=update">
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['quantity_in_cart']; ?>" min="0" class="form-control" style="width: 70px; display: inline-block;">
                            <button type="submit" class="btn btn-primary btn-sm">Cập nhật</button>
                        </form>
                    </td>
                    
                    <td><?php echo number_format($item['price'] * $item['quantity_in_cart']); ?> VND</td>
                    
                    <td>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=remove&id=<?php echo $item['product_id']; ?>"
                           onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');"
                           class="btn btn-danger btn-sm">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="cart-summary-container" style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 20px;">
        
        <div class="coupon-form" style="flex: 1; max-width: 350px; padding: 15px; border: 1px solid #ccc; border-radius: 5px;">
            <h4>Áp dụng Mã giảm giá</h4>
            <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=applyCoupon" style="display: flex; gap: 5px;">
                <input type="text" id="coupon_code" name="coupon_code" 
                       value="<?php echo htmlspecialchars($coupon_code ?? ''); ?>" 
                       placeholder="Nhập mã..." class="form-control" style="flex: 1;">
                <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                <button type="submit" class="btn btn-primary">Áp dụng</button>
            </form>
        </div>
        
        <div class="total-summary" style="text-align: right;">
            <h4>Tổng tiền hàng: 
                <span style="font-weight: normal;"><?php echo number_format($total_price); ?> VND</span>
            </h4>
            
            <?php if (isset($coupon_code) && $discount_amount > 0): ?>
                <h4 class="discount-applied" style="color: #d9534f; margin-top: 5px; margin-bottom: 5px;">
                    Giảm giá (<?php echo htmlspecialchars($coupon_code); ?>): 
                    <span style="font-weight: normal;">-<?php echo number_format($discount_amount); ?> VND</span>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=removeCoupon" 
                       class="btn btn-danger btn-sm" style="margin-left: 10px;">[Xóa]</a>
                </h4>
            <?php endif; ?>
            
            <h2 style="margin-top: 10px;">
                Tổng thanh toán: 
                <span class="price" style="color: #f0ad4e; font-size: 1.5em;">
                    <?php echo number_format($final_price); ?> VND
                </span>
            </h2>
            
            <a href="<?php echo BASE_URL; ?>index.php?controller=checkout&action=index" 
               class="btn btn-success btn-lg" style="margin-top: 15px;">
                Tiến hành Thanh toán
            </a>
        </div>
    </div>

<?php endif; ?>