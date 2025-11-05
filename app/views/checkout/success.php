<div style="text-align: center; padding: 50px;">
    <h1 style="color: green;">Đặt hàng thành công!</h1>
    <p>Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.</p>
    
    <?php if (isset($_GET['order_id']) && (int)$_GET['order_id'] > 0): ?>
        <p>Mã đơn hàng của bạn là: <strong>#<?php echo (int)$_GET['order_id']; ?></strong></p>
    <?php endif; ?>
    
    <a href="<?php echo BASE_URL; ?>" style="padding: 10px 20px; background-color: blue; color: white; text-decoration: none;">
        Tiếp tục mua sắm
    </a>
</div>