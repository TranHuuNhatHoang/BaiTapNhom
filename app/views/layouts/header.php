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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'navbar.php'; // Tải thanh điều hướng ?>
        <main></main>