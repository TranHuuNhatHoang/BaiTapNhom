<?php 
/*
 * File trang Đăng ký (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .form-group, .form-control, .btn, .btn-primary
 */
?>

<h1>Đăng ký tài khoản</h1>
<p>Vui lòng điền thông tin bên dưới để tạo tài khoản.</p>

<!-- (Form của GĐ 19 - Người 2) -->
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=auth&action=handleRegister" style="max-width: 500px;">
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="full_name">Họ và Tên:</label>
        <!-- Áp dụng class .form-control -->
        <input type="text" id="full_name" name="full_name" class="form-control" required>
    </div>
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="email">Email:</label>
        <!-- Áp dụng class .form-control -->
        <input type="email" id="email" name="email" class="form-control" required>
    </div>
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="password">Mật khẩu:</label>
        <!-- Áp dụng class .form-control -->
        <input type="password" id="password" name="password" class="form-control" required>
    </div>
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="password_confirm">Xác nhận mật khẩu:</label>
        <!-- Áp dụng class .form-control -->
        <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
    </div>
    
    <!-- Áp dụng class .btn .btn-primary -->
    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Đăng ký</button>
</form>