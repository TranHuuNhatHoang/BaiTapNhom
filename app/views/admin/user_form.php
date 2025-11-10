<!-- (Biến $user được truyền từ Controller) -->
<h1>Sửa thông tin Người dùng</h1>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers">Quay lại danh sách</a>
<hr>

<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=updateUser">
    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
    
    <div style="margin-bottom: 10px;">
        <label for="full_name">Họ và Tên:</label><br>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required style="width: 300px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required style="width: 300px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="phone">Số điện thoại:</label><br>
        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" style="width: 300px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="address">Địa chỉ:</label><br>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" style="width: 300px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="province">Tỉnh/Thành phố:</label><br>
        <input type="text" id="province" name="province" value="<?php echo htmlspecialchars($user['province'] ?? ''); ?>" style="width: 300px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="role">Vai trò (Role):</label><br>
        <select id="role" name="role" <?php echo $user['user_id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
        <?php if($user['user_id'] == $_SESSION['user_id']): ?>
            <small>(Không thể tự đổi vai trò của mình)</small>
        <?php endif; ?>
    </div>
    <button type="submit">Cập nhật Người dùng</button>
</form>