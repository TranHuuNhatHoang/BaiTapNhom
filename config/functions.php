<?php
// (Các hàm cũ của bạn nếu có)

/**
 * HÀM MỚI (Người 3 - GĐ19): Đặt một thông báo Flash
 * $type: 'success', 'error', 'info'
 */
function set_flash_message($message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * HÀM MỚI (Người 3 - GĐ19): Hiển thị và Xóa thông báo Flash
 */
function display_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        $message = $flash['message'];
        $type = $flash['type'];
        
        // CSS cho thông báo
        $style = '';
        if ($type == 'success') {
            $style = 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;';
        } elseif ($type == 'error') {
            $style = 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;';
        } else {
            $style = 'background-color: #cce5ff; color: #004085; border: 1px solid #b8daff;';
        }
        
        // In ra HTML
        echo '<div class="flash-message" style="' . $style . ' padding: 15px; margin-bottom: 20px; border-radius: 5px;">';
        echo $message;
        echo '</div>';
        
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['flash_message']);
    }
}
?>