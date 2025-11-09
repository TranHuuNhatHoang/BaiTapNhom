<?php
// Tải Category Model để lấy danh sách cho navbar
global $conn; // Lấy $conn (từ index.php)

// Kiểm tra xem class Category đã được tải chưa
if (!class_exists('Category')) { 
    require_once ROOT_PATH . '/app/models/Category.php';
}
$categoryModelNav = new Category($conn);
$navbar_categories = $categoryModelNav->getAllCategories();

// --- THÊM MỚI (Người 2) ---
// Tải Brand Model để lấy danh sách cho navbar
if (!class_exists('Brand')) {
    require_once ROOT_PATH . '/app/models/Brand.php';
}
$brandModelNav = new Brand($conn);
$navbar_brands = $brandModelNav->getAllBrands();
// --- KẾT THÚC THÊM MỚI ---

// Tính toán giỏ hàng (code này đã có từ GĐ trước)
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>

<nav>
    <ul>
        <li><a href="<?php echo BASE_URL; ?>">Trang chủ</a></li>
        
        <li style="position: relative; display: inline-block;" 
            onmouseover="this.querySelector('.dropdown-menu').style.display='block';"
            onmouseout="this.querySelector('.dropdown-menu').style.display='none';">
            
            <a href="#">Danh mục</a>
            
            <ul class="dropdown-menu" 
                style="display: none; position: absolute; background-color: #f9f9f9; 
                       min-width: 160px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); 
                       padding: 12px 16px; z-index: 1; list-style: none; margin: 0;">
                
                <?php foreach ($navbar_categories as $nav_cat): ?>
                    <li style="padding: 5px 0;">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=category&id=<?php echo $nav_cat['category_id']; ?>">
                            <?php echo htmlspecialchars($nav_cat['category_name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        
        <li style="position: relative; display: inline-block;" 
            onmouseover="this.querySelector('.dropdown-menu').style.display='block';"
            onmouseout="this.querySelector('.dropdown-menu').style.display='none';">
            
            <a href="#">Thương hiệu</a>
            <ul class="dropdown-menu" 
                style="display: none; position: absolute; background-color: #f9f9f9; 
                       min-width: 160px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); 
                       padding: 12px 16px; z-index: 1; list-style: none; margin: 0;">
                
                <?php foreach ($navbar_brands as $nav_brand): ?>
                    <li style="padding: 5px 0;">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=brand&id=<?php echo $nav_brand['brand_id']; ?>">
                            <?php echo htmlspecialchars($nav_brand['brand_name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=product&action=search">Tìm kiếm</a></li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=account&action=index">
                    Xin chào, <?php echo htmlspecialchars($_SESSION['user_full_name']); ?>!
                </a>
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

        <li>
            <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=index">
                Giỏ hàng (<?php echo $cart_count; ?>)
            </a>
        </li>
    </ul>
</nav>