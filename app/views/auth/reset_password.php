<?php 
/*
 * File trang Đặt lại Mật khẩu (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .form-group, .form-control, .btn, .btn-primary
 */
?>

<!-- (Biến $token được truyền từ Controller) -->
<h1>Đặt lại Mật khẩu Mới</h1>

<!-- (Form của GĐ 15 - Người 3) -->
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=auth&action=handleResetPassword" style="max-width: 500px;">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="new_password">Mật khẩu mới:</label>
        <!-- Áp dụng class .form-control -->
        <input type="password" id="new_password" name="new_password" class="form-control" required>
    </div>
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="confirm_password">Xác nhận mật khẩu mới:</label>
        <!-- Áp dụng class .form-control -->
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
    </div>
    
    <!-- Áp dụng class .btn .btn-primary -->
    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Lưu Mật khẩu</button>
</form>