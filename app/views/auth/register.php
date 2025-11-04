<h1>Đăng ký tài khoản</h1>
<p>Vui lòng điền thông tin bên dưới để tạo tài khoản.</p>

<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=auth&action=handleRegister">
    
    <div style="margin-bottom: 10px;">
        <label for="full_name">Họ và Tên:</label><br>
        <input type="text" id="full_name" name="full_name" required style="width: 300px;">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required style="width: 300px;">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="password">Mật khẩu:</label><br>
        <input type="password" id="password" name="password" required style="width: 300px;">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="password_confirm">Xác nhận mật khẩu:</label><br>
        <input type="password" id="password_confirm" name="password_confirm" required style="width: 300px;">
    </div>
    
    <button type="submit">Đăng ký</button>
</form>