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
        
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=register">Đăng ký</a></li>
        
        <li>
            <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=index">
                Giỏ hàng (<?php echo $cart_count; ?>)
            </a>
        </li>   
    </ul>
</nav>