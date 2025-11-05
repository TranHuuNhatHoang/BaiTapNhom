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

    <div style="text-align: right; margin-top: 20px;">
        <h2>Tổng tiền: <span style="color: red;"><?php echo number_format($total_price); ?> VND</span></h2>
        
        <a href="<?php echo BASE_URL; ?>index.php?controller=checkout&action=index" 
           style="background-color: blue; color: white; padding: 15px; text-decoration: none;">
            Tiến hành Thanh toán
        </a>
    </div>

<?php endif; ?>