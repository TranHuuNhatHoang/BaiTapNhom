<h1>Đặt lại Mật khẩu Mới</h1>
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=auth&action=handleResetPassword">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    
    <div style="margin-bottom: 10px;">
        <label for="new_password">Mật khẩu mới:</label><br>
        <input type="password" id="new_password" name="new_password" required style="width: 300px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="confirm_password">Xác nhận mật khẩu mới:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required style="width: 300px;">
    </div>
    <button type="submit">Lưu Mật khẩu</button>
</form>