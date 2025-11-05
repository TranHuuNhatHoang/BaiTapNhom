<h1>Tài khoản của tôi</h1>
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=history">Xem Lịch sử Đơn hàng</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=changePassword" style="color: red;">Đổi mật khẩu</a>
<hr>

<h3>Cập nhật Thông tin Cá nhân</h3>
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=account&action=updateProfile">
    
    <div style="margin-bottom: 10px;">
        <label>Email (Không thể đổi):</label><br>
        <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled style="width: 300px;">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="full_name">Họ và Tên:</label><br>
        <input type="text" id="full_name" name="full_name" 
               value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required style="width: 300px;">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="phone">Số điện thoại:</label><br>
        <input type="tel" id="phone" name="phone" 
               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" style="width: 300px;">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="address">Địa chỉ:</label><br>
        <input type="text" id="address" name="address" 
               value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" style="width: 300px;">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="province">Tỉnh/Thành phố:</label><br>
        <input type="text" id="province" name="province" 
               value="<?php echo htmlspecialchars($user['province'] ?? ''); ?>" style="width: 300px;">
    </div>
    
    <button type="submit">Cập nhật</button>
</form>