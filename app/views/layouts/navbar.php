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
// ĐÃ SỬA LỖI LOGIC: Kiểm tra file tồn tại trước khi gán
$nav_avatar_path = 'public/images/default_avatar.png'; // Ảnh mặc định

if (isset($_SESSION['user_avatar']) && !empty($_SESSION['user_avatar'])) {
    $avatar_file_name = $_SESSION['user_avatar'];
    $avatar_file_full_path = ROOT_PATH . '/public/uploads/avatars/' . $avatar_file_name;
    
    // Chỉ sử dụng avatar trong Session nếu file thực sự tồn tại
    if (file_exists($avatar_file_full_path)) {
        $nav_avatar_path = 'public/uploads/avatars/' . $avatar_file_name;
    } 
    // Nếu file không tồn tại, nó sẽ dùng ảnh mặc định ('public/images/default_avatar.png')
}
?>

<nav>
    <div class="navbar-container">
        <!-- Hamburger Icon for mobile -->
        <button class="navbar-toggle" id="navbar-toggle" aria-label="Mở menu" style="display:none">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    <ul id="main-navbar" class="main-navbar">
        <?php
        // Xác định controller/action hiện tại để set active
        $current_controller = isset($_GET['controller']) ? $_GET['controller'] : '';
        $current_action = isset($_GET['action']) ? $_GET['action'] : '';
        $current_id = isset($_GET['id']) ? $_GET['id'] : '';
        ?>
        <li<?php if ($current_controller == '' || $current_controller == 'home') echo ' class="active"'; ?>>
            <a href="<?php echo BASE_URL; ?>" class="<?php if ($current_controller == '' || $current_controller == 'home') echo 'active'; ?>">Trang chủ</a>
        </li>
        <li<?php if ($current_controller == 'product' && ($current_action == 'index' || $current_action == '')) echo ' class="active"'; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="<?php if ($current_controller == 'product' && ($current_action == 'index' || $current_action == '')) echo 'active'; ?>">
                Sản phẩm
            </a>
        </li>
        <li class="dropdown<?php if ($current_controller == 'product' && $current_action == 'category') echo ' active'; ?>">
            <a href="#" class="<?php if ($current_controller == 'product' && $current_action == 'category') echo 'active'; ?>">Danh mục</a>
            <ul class="dropdown-menu">
                <?php foreach ($navbar_categories as $nav_cat): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=category&id=<?php echo $nav_cat['category_id']; ?>" class="<?php if ($current_controller == 'product' && $current_action == 'category' && $current_id == $nav_cat['category_id']) echo 'active'; ?>">
                            <?php echo htmlspecialchars($nav_cat['category_name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        
        <li class="dropdown<?php if ($current_controller == 'product' && $current_action == 'brand') echo ' active'; ?>">
            <a href="#" class="<?php if ($current_controller == 'product' && $current_action == 'brand') echo 'active'; ?>">Thương hiệu</a>
            <ul class="dropdown-menu">
                <?php foreach ($navbar_brands as $nav_brand): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=brand&id=<?php echo $nav_brand['brand_id']; ?>" class="<?php if ($current_controller == 'product' && $current_action == 'brand' && $current_id == $nav_brand['brand_id']) echo 'active'; ?>">
                            <?php echo htmlspecialchars($nav_brand['brand_name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        
        <li style="position: relative;"> 
            <form method="GET" action="<?php echo BASE_URL; ?>index.php">
                <input type="hidden" name="controller" value="product">
                <input type="hidden" name="action" value="search">
                
                <input type="text" id="navbar-search-input" name="query" 
                        placeholder="Tìm kiếm sản phẩm..." autocomplete="off">
            </form>
            
            <div id="navbar-search-results" 
                  style="display: none; position: absolute; top: 100%; left: 0; 
                         background: white; border: 1px solid #ccc; 
                         min-width: 400px; z-index: 1000;">
                </div>
            
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

                <li style="margin-left: auto;"<?php if ($current_controller == 'cart') echo ' class="active"'; ?>>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=index" class="<?php if ($current_controller == 'cart') echo 'active'; ?>">
                            Giỏ hàng (<span id="cart-count"><?php echo $cart_count; ?></span>)
                        </a>
                </li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="dropdown<?php if ($current_controller == 'account') echo ' active'; ?>">
                <a href="<?php echo BASE_URL; ?>index.php?controller=account&action=index" 
                   style="display: flex; align-items: center; gap: 8px;" class="<?php if ($current_controller == 'account') echo 'active'; ?>">
                    <img src="<?php echo BASE_URL . $nav_avatar_path; ?>" 
                          style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                    <span>Xin chào, <?php echo htmlspecialchars($_SESSION['user_full_name']); ?>!</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=account&action=index" class="<?php if ($current_controller == 'account' && ($current_action == 'index' || $current_action == '')) echo 'active'; ?>">Tài khoản của tôi</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=account&action=history" class="<?php if ($current_controller == 'account' && $current_action == 'history') echo 'active'; ?>">Lịch sử Đơn hàng</a></li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=admin" class="<?php if ($current_controller == 'admin') echo 'active'; ?>">Admin Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=logout">Đăng xuất</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li<?php if ($current_controller == 'auth' && $current_action == 'login') echo ' class="active"'; ?>>
                <a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=login" class="<?php if ($current_controller == 'auth' && $current_action == 'login') echo 'active'; ?>">Đăng nhập</a>
            </li>
            <li<?php if ($current_controller == 'auth' && $current_action == 'register') echo ' class="active"'; ?>>
                <a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=register" class="<?php if ($current_controller == 'auth' && $current_action == 'register') echo 'active'; ?>">Đăng ký</a>
            </li>
        <?php endif; ?>
        
    </ul>
    </div>
</nav>
<script>
// Hamburger menu toggle
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('navbar-toggle');
    var nav = document.getElementById('main-navbar');
    if (toggle && nav) {
        toggle.addEventListener('click', function() {
            nav.classList.toggle('open');
        });
    }
});
</script>