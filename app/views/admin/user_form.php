<?php 
/*
 * File trang Admin - Form Sửa Người dùng (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .form-group, .form-control, .btn, .btn-primary, .btn-secondary
 *
 * Các biến được truyền từ AdminController@editUser:
 * $user (mảng)
 */
?>

<h1>Sửa thông tin Người dùng</h1>

<!-- Áp dụng class .btn -->
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers" class="btn btn-secondary" style="margin-bottom: 15px;">
    &laquo; Quay lại danh sách
</a>
<hr>

<!-- 
============================================================
 FORM (ĐÃ ÁP DỤNG CLASS)
============================================================
-->
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=updateUser" style="max-width: 600px;">
    
    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="full_name">Họ và Tên:</label>
        <!-- Áp dụng class .form-control -->
        <input type="text" id="full_name" name="full_name" class="form-control" 
               value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" class="form-control" 
               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="phone">Số điện thoại:</label>
        <input type="tel" id="phone" name="phone" class="form-control" 
               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
    </div>
    
    <div class="form-group">
        <label for="address">Địa chỉ:</label>
        <input type="text" id="address" name="address" class="form-control" 
               value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
    </div>
    
    <div class="form-group">
        <label for="province">Tỉnh/Thành phố:</label>
        <input type="text" id="province" name="province" class="form-control" 
               value="<?php echo htmlspecialchars($user['province'] ?? ''); ?>">
    </div>
    
    <div class="form-group">
        <label for="role">Vai trò (Role):</label>
        <!-- Áp dụng class .form-control -->
        <select id="role" name="role" class="form-control" 
            <?php echo $user['user_id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
        <?php if($user['user_id'] == $_SESSION['user_id']): ?>
            <small>(Không thể tự đổi vai trò của mình)</small>
        <?php endif; ?>
    </div>
    
    <!-- Áp dụng class .btn .btn-primary -->
    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Cập nhật Người dùng</button>
</form>