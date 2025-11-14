<?php 
/*
 * File trang Tài khoản
 * Áp dụng: .form-group, .form-control, .btn, .btn-primary
 */
?>

<h1>Tài khoản của tôi</h1>

<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=history" class="btn btn-secondary">Xem Lịch sử Đơn hàng</a>
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=changePassword" class="btn btn-danger" style="margin-left: 10px;">Đổi mật khẩu</a>
<hr>

<div class="avatar-section form-group">
    <h3>Ảnh đại diện</h3>
    <?php 
    // Logic lấy avatar (Dùng dữ liệu từ CSDL $user['avatar'])
    $avatar_file_name = $user['avatar'] ?? '';
    $avatar_path = 'public/images/default_avatar.png'; // Mặc định
    
    if (!empty($avatar_file_name)) {
        $full_avatar_path = ROOT_PATH . '/public/uploads/avatars/' . $avatar_file_name;
        if (file_exists($full_avatar_path)) {
            // Nếu có trong CSDL VÀ file tồn tại, dùng đường dẫn này
            $avatar_path = 'public/uploads/avatars/' . $avatar_file_name;
        }
    }
    // Gán lại BASE_URL vào đầu đường dẫn để hiển thị trên trình duyệt
    $display_avatar_url = BASE_URL . $avatar_path;
    ?>
    
    <img src="<?php echo $display_avatar_url; ?>" alt="Avatar" 
          style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #eee; margin-bottom: 15px;">
    
    <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=account&action=updateAvatar" enctype="multipart/form-data">
        <label for="avatar_file">Thay ảnh mới:</label><br>
        <input type="file" id="avatar_file" name="avatar_file" accept="image/jpeg,image/png,image/gif,image/webp" required class="form-control" style="max-width: 300px; display: inline-block;">
        <button type="submit" class="btn btn-secondary">Tải lên</button>
    </form>
</div>
<hr>
<h3>Cập nhật Thông tin Cá nhân</h3>

<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=account&action=updateProfile" style="max-width: 600px;">
    
    <div class="form-group">
        <label>Email (Không thể đổi):</label>
        <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="form-control" disabled>
    </div>
    
    <div class="form-group">
        <label for="full_name">Họ và Tên:</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required class="form-control">
    </div>
    
    <div class="form-group">
        <label for="phone">Số điện thoại:</label>
        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="form-control">
    </div>
    
    <div class="form-group">
        <label for="address">Địa chỉ:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" class="form-control">
    </div>
    
    <div class="form-group">
        <label for="province">Tỉnh/Thành phố:</label>
        <input type="text" id="province" name="province" value="<?php echo htmlspecialchars($user['province'] ?? ''); ?>" class="form-control">
    </div>
    
    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Cập nhật thông tin</button>
</form>