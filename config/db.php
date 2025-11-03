<?php
$host = 'localhost';
$db   = 'laptop_store'; 
$user = 'root';        
$pass = '';
$port = 3307;    
try {
     $pdo = new mysqli($host,$user,$pass,$db,$port);
} catch (\PDOException $e) {
     die("Lỗi kết nối CSDL: " . $e->getMessage());
}
// Biến $pdo đã sẵn sàng!

?>