<?php
global $conn;
if (!class_exists('Category')) { require_once ROOT_PATH . '/app/models/Category.php'; }
$categoryModelNav = new Category($conn);
$navbar_categories = $categoryModelNav->getAllCategories();
if (!class_exists('Brand')) { require_once ROOT_PATH . '/app/models/Brand.php'; }
$brandModelNav = new Brand($conn);
$navbar_brands = $brandModelNav->getAllBrands();
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>

<!-- 
============================================================
 CẬP NHẬT (Người 2): Dọn dẹp HTML, xóa inline style/js
 Thêm class="dropdown"
============================================================
-->
<nav>
    <ul>
        <li><a href="<?php echo BASE_URL; ?>">Trang chủ</a></li>
        
        <!-- Dropdown Danh mục (Đã dọn dẹp) -->
        <li class="dropdown">
            <a href="#">Danh mục</a>
            <ul class="dropdown-menu">
                <?php foreach ($navbar_categories as $nav_cat): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=category&id=<?php echo $nav_cat['category_id']; ?>">
                            <?php echo htmlspecialchars($nav_cat['category_name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        
        <!-- Dropdown Thương hiệu (Đã dọn dẹp) -->
        <li class="dropdown">
            <a href="#">Thương hiệu</a>
            <ul class="dropdown-menu">
                <?php foreach ($navbar_brands as $nav_brand): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=brand&id=<?php echo $nav_brand['brand_id']; ?>">
                            <?php echo htmlspecialchars($nav_brand['brand_name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        
        <!-- Live Search (Đã có từ GĐ 17) -->
        <li style="position: relative;"> <!-- (Giữ lại style này vì nó đặc biệt) -->
            <form method="GET" action="<?php echo BASE_URL; ?>index.php">
                <input type="hidden" name="controller" value="product">
                <input type="hidden" name="action" value="search">
                <input type="text" id="navbar-search-input" name="query" placeholder="Tìm kiếm sản phẩm..." autocomplete="off">
            </form>
            <div id="navbar-search-results" style="display: none; position: absolute; ...">
                <!-- (CSS của Live Search vẫn giữ nguyên) -->
            </div>
            <!-- (Style của Live Search vẫn giữ nguyên) -->
        </li>

        <!-- (Code Đăng nhập/Tài khoản/Giỏ hàng... ở đây) -->
        <!-- (Phần code này nằm bên phải) -->
        <li style="margin-left: auto;"> <!-- Đẩy các mục sau sang phải -->
            <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=index">
                Giỏ hàng (<?php echo $cart_count; ?>)
            </a>
        </li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="dropdown">
                <a href="<?php echo BASE_URL; ?>index.php?controller=account&action=index">
                    Xin chào, <?php echo htmlspecialchars($_SESSION['user_full_name']); ?>!
                </a>
                <!-- Dropdown cho User -->
                <ul class="dropdown-menu">
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=account&action=index">Tài khoản của tôi</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=account&action=history">Lịch sử Đơn hàng</a></li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=admin">Admin Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=logout">Đăng xuất</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=login">Đăng nhập</a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=register">Đăng ký</a>
            </li>
        <?php endif; ?>
        
    </ul>
</nav>