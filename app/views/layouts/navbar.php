<?php
// Tính tổng số lượng sản phẩm trong giỏ
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    // array_sum cộng tổng các value (số lượng) trong mảng
    $cart_count = array_sum($_SESSION['cart']);
}
?>

<nav>
    <ul>
        <li><a href="<?php echo BASE_URL; ?>">Trang chủ</a></li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            
            <li>
                <a>Xin chào, <?php echo htmlspecialchars($_SESSION['user_full_name']); ?>!</a>
            </li>
            
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li><a href="<?php echo BASE_URL; ?>index.php?controller=admin">Admin</a></li>
            <?php endif; ?>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=logout">Đăng xuất</a>
            </li>

        <?php else: ?>
            
            <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=login">Đăng nhập</a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=register">Đăng ký</a>
            </li>

        <?php endif; ?>

        <li><a href="#">Giỏ hàng</a></li>
    </ul>
</nav>