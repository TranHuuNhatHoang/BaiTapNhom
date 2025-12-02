<?php 
/*
 * File trang Đăng nhập (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .form-group, .form-control, .btn, .btn-primary
 */
?>

<h1>Đăng nhập</h1>

<!-- (Form của GĐ 13) -->
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=auth&action=handleLogin" style="max-width: 500px;">
    
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
    
    <!-- Áp dụng class .btn .btn-primary -->
    <button type="submit" class="btn btn-primary">Đăng nhập</button>
</form>
<!-- THÊM NÚT GOOGLE -->
<div style="text-align: center; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
    <p>Hoặc đăng nhập nhanh bằng:</p>
    <a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=loginGoogle" 
       class="btn" 
       style="background-color: #db4437; color: white; display: inline-block; text-decoration: none; padding: 10px 20px; border-radius: 5px;">
       <span style="font-weight: bold;">G</span> Đăng nhập bằng Google
    </a>
</div>

<!-- Link Quên mật khẩu (GĐ 15) -->
<p style="margin-top: 15px;">
    <a href="<?php echo BASE_URL; ?>index.php?controller=auth&action=forgotPassword">
        Quên mật khẩu?
    </a>
</p>