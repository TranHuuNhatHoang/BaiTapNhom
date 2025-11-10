<h1>Quên Mật khẩu</h1>
<p>Nhập email của bạn, chúng tôi sẽ gửi link đặt lại mật khẩu.</p>
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=auth&action=handleForgotPassword">
    <div style="margin-bottom: 10px;">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required style="width: 300px;">
    </div>
    <button type="submit">Gửi link Reset</button>
</form>