<?php
// --- Tải Model cho Dropdowns ---
global $conn; // Lấy $conn (từ index.php)

// Tải Category Model
if (!class_exists('Category')) { 
    require_once ROOT_PATH . '/app/models/Category.php';
}
$categoryModelNav = new Category($conn);
$navbar_categories = $categoryModelNav->getAllCategories();

// Tải Brand Model
if (!class_exists('Brand')) {
    require_once ROOT_PATH . '/app/models/Brand.php';
}
$brandModelNav = new Brand($conn);
$navbar_brands = $brandModelNav->getAllBrands();

// --- Tính toán Giỏ hàng ---
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}

// --- Lấy Avatar User (nếu có) ---
$nav_avatar_path = 'public/images/default_avatar.png'; // Ảnh mặc định
if (isset($_SESSION['user_avatar']) && !empty($_SESSION['user_avatar'])) {
    $avatar_file = ROOT_PATH . '/public/uploads/avatars/' . $_SESSION['user_avatar'];
    if (file_exists($avatar_file)) {
        $nav_avatar_path = 'public/uploads/avatars/' . $_SESSION['user_avatar'];
    }
}
?>

<!-- 
============================================================
 CẬP NHẬT (Người 2 - GĐ18):
 - Đã xóa style inline và onmouseover
 - Đã thêm class="dropdown" và class="dropdown-menu"
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
        
        <!-- Live Search (GĐ 17 - Giữ nguyên) -->
        <li style="position: relative;"> 
            <form method="GET" action="<?php echo BASE_URL; ?>index.php">
                <input type="hidden" name="controller" value="product">
                <input type="hidden" name="action" value="search">
                
                <input type="text" id="navbar-search-input" name="query" 
                       placeholder="Tìm kiếm sản phẩm..." autocomplete="off">
            </form>
            
            <!-- Hộp kết quả (CSS cơ bản) -->
            <div id="navbar-search-results" 
                 style="display: none; position: absolute; top: 100%; left: 0; 
                        background: white; border: 1px solid #ccc; 
                        min-width: 400px; z-index: 1000;">
                <!-- JavaScript sẽ điền kết quả vào đây -->
            </div>
            
            <!-- (Style của Live Search vẫn giữ nguyên) -->
            <style>
                .search-result-item {
                    display: flex; align-items: center; gap: 10px;
                    padding: 8px 12px; text-decoration: none;
                    color: black; border-bottom: 1px solid #eee;
                }
                .search-result-item:hover { background-color: #f4f4f4; }
                .search-result-item span {
                    white-space: nowrap; overflow: hidden;
                    text-overflow: ellipsis;
                }
            </style>
        </li>

        <!-- Các mục bên phải -->
        
        <!-- Giỏ hàng (Đẩy sang phải) -->
        <li style="margin-left: auto;">
           <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=index">
             Giỏ hàng (<span id="cart-count"><?php echo $cart_count; ?></span>)
           </a>
        </li>
        
        <!-- Logic Đăng nhập / Tài khoản -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Đã đăng nhập -->
            <li class="dropdown">
                <!-- Link Tài khoản (đã có avatar) -->
                <a href="<?php echo BASE_URL; ?>index.php?controller=account&action=index" 
                   style="display: flex; align-items: center; gap: 8px;">
                    <img src="<?php echo BASE_URL . $nav_avatar_path; ?>" 
                         style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                    <span>Xin chào, <?php echo htmlspecialchars($_SESSION['user_full_name']); ?>!</span>
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
            <!-- Chưa đăng nhập -->
            <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=login">Đăng nhập</a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=register">Đăng ký</a>
            </li>
        <?php endif; ?>
        
    </ul>
</nav>