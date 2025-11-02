<?php
$host = 'localhost';
$db   = 'laptop_store'; 
$user = 'root';        
$pass = '';    
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die("Lỗi kết nối CSDL: " . $e->getMessage());
}
// Biến $pdo đã sẵn sàng!

?>