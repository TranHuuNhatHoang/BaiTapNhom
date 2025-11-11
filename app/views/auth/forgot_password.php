<?php 
/*
 * File trang Quên Mật khẩu (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .form-group, .form-control, .btn, .btn-primary
 */
?>

<h1>Quên Mật khẩu</h1>
<p>Nhập email của bạn, chúng tôi sẽ gửi link đặt lại mật khẩu.</p>

<!-- (Form của GĐ 15 - Người 3) -->
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=auth&action=handleForgotPassword" style="max-width: 500px;">
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="email">Email:</label>
        <!-- Áp dụng class .form-control -->
        <input type="email" id="email" name="email" class="form-control" required>
    </div>
    
    <!-- Áp dụng class .btn .btn-primary -->
    <button type="submit" class="btn btn-primary">Gửi link Reset</button>
</form>