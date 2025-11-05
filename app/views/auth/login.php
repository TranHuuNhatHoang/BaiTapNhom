<h1>Đăng nhập</h1>

<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=auth&action=handleLogin">
    
    <div style="margin-bottom: 10px;">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required style="width: 300px;">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="password">Mật khẩu:</label><br>
        <input type="password" id="password" name="password" required style="width: 300px;">
    </div>
    
    <button type="submit">Đăng nhập</button>
</form>