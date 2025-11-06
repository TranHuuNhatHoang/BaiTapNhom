<h1>Đổi mật khẩu</h1>
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=index">Quay lại Tài khoản</a>
<hr>

<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=account&action=handleChangePassword">
    <div style="margin-bottom: 10px;">
        <label for="old_password">Mật khẩu cũ:</label><br>
        <input type="password" id="old_password" name="old_password" required style="width: 300px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="new_password">Mật khẩu mới:</label><br>
        <input type="password" id="new_password" name="new_password" required style="width: 300px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="confirm_password">Xác nhận mật khẩu mới:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required style="width: 300px;">
    </div>
    <button type="submit">Lưu thay đổi</button>
</form>