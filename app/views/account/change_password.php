<?php 
/*
 * File trang Đổi Mật khẩu (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .form-group, .form-control, .btn, .btn-primary
 */
?>

<h1>Đổi mật khẩu</h1>

<!-- Áp dụng class .btn -->
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=index" class="btn btn-secondary" style="margin-bottom: 15px;">
    &laquo; Quay lại Tài khoản
</a>
<hr>

<!-- (Form của GĐ 16 - Người 2) -->
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=account&action=handleChangePassword" style="max-width: 500px;">
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="old_password">Mật khẩu cũ:</label>
        <!-- Áp dụng class .form-control -->
        <input type="password" id="old_password" name="old_password" class="form-control" required>
    </div>
    
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
    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Lưu thay đổi</button>
</form>