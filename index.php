<?php
// Bắt đầu session để có thể dùng cho đăng nhập, giỏ hàng
session_start();

// Tải file config chung
require_once 'config/config.php';
// Tải file kết nối CSDL ($pdo)
require_once 'config/db.php'; 

// ----- BỘ ĐIỀU TUYẾN (ROUTER) ĐƠN GIẢN -----

// 1. Lấy controller (mặc định là 'product' nếu không có)
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'product';

// 2. Lấy action (mặc định là 'index' nếu không có)
$actionName = isset($_GET['action']) ? $_GET['action'] : 'index';

// 3. Chuyển đổi tên (vd: 'product' -> 'ProductController')
$controllerClassName = ucfirst($controllerName) . 'Controller';

// 4. Xây dựng đường dẫn file controller
$controllerFilePath = ROOT_PATH . '/app/controllers/' . $controllerClassName . '.php';

// 5. Kiểm tra file controller có tồn tại không
if (file_exists($controllerFilePath)) {
    
    require_once $controllerFilePath; // Tải file controller

    // 6. Kiểm tra class controller có tồn tại không
    if (class_exists($controllerClassName)) {
        $controller = new $controllerClassName();

        // 7. Kiểm tra phương thức (action) có tồn tại trong class đó không
        if (method_exists($controller, $actionName)) {
            
            // 8. Gọi phương thức
            $controller->$actionName();
            
        } else {
            echo "Lỗi: Action '$actionName' không tồn tại trong controller '$controllerClassName'.";
        }
    } else {
        echo "Lỗi: Class '$controllerClassName' không tồn tại.";
    }
} else {
    echo "Lỗi: Controller '$controllerClassName' không tìm thấy tại '$controllerFilePath'.";
}
// ----- KẾT THÚC ROUTER -----
?>