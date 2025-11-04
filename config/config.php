<?php
// Bật báo cáo lỗi (để debug trong quá trình phát triển)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cấu hình múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// URL gốc của website (thay đổi cho phù hợp với XAMPP của bạn)
define('BASE_URL', 'http://localhost/BaiTapNhom/');

// Đường dẫn gốc của dự án trên ổ đĩa
define('ROOT_PATH', dirname(__DIR__)); 
?>