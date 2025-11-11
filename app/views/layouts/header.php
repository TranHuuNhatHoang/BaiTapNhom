<?php 
    // Tải file config 1 lần duy nhất
    require_once dirname(__DIR__, 3) . '/config/config.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng Laptop</title>
    
    <!-- Link CSS chính -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    
    <!-- Link JS (defer: chạy sau khi HTML tải xong) -->
    <!-- (JavaScript của Người 2 (AJAX) và Người 3 (Flash) cần file này) -->
    <script src="<?php echo BASE_URL; ?>public/js/main.js" defer></script>
</head>
<body>
    <div class="container">
        
        <?php include 'navbar.php'; // Tải thanh điều hướng ?>
        
        <!-- 
        ============================================================
         THÊM MỚI (Người 3 - GĐ19): Hiển thị Thông báo Flash
        ============================================================
        -->
        <?php
        // Đảm bảo functions.php đã được tải (an toàn)
        // (Đường dẫn này tính từ header.php -> /views/ -> /app/ -> / (ROOT_PATH))
        if (!function_exists('display_flash_message')) {
            require_once dirname(__DIR__, 3) . '/config/functions.php';
        }
        
        // Hiển thị thông báo (nếu có)
        // Hàm này sẽ tự kiểm tra $_SESSION['flash_message']
        // và tự xóa sau khi hiển thị
        display_flash_message();
        ?>
        <!-- KẾT THÚC THÊM MỚI -->

        <main> <!-- Thẻ <main> nên MỞ ở đây -->
        <!-- (Và thẻ </main> sẽ được ĐÓNG trong footer.php) -->