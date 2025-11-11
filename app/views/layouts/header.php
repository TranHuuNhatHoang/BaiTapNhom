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
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    
    <script src="<?php echo BASE_URL; ?>public/js/main.js" defer></script>
</head>
<body>
    <div class="container">
        
        <?php include 'navbar.php'; // Tải thanh điều hướng ?>
        
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
        <main>