<?php 
/*
 * File trang Tài khoản (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .form-group, .form-control, .btn, .btn-primary
 */
?>

<!-- (Biến $user được truyền từ Controller) -->
<h1>Tài khoản của tôi</h1>

<!-- Áp dụng class .btn cho các link điều hướng -->
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=history" class="btn btn-secondary">Xem Lịch sử Đơn hàng</a>
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=changePassword" class="btn btn-danger" style="margin-left: 10px;">Đổi mật khẩu</a>
<hr>

<!-- 
============================================================
 KHU VỰC AVATAR (ĐÃ DỌN DẸP)
============================================================
-->
<div class="avatar-section form-group">
    <h3>Ảnh đại diện</h3>
    <?php 
    // (Logic lấy avatar của GĐ 19)
    $avatar_path = 'public/uploads/avatars/' . ($user['avatar'] ?? 'default.png');
    if (!file_exists(ROOT_PATH . '/' . $avatar_path) || empty($user['avatar'])) {
        $avatar_path = 'public/images/default_avatar.png'; 
    }
    ?>
    
    <!-- (CSS cho avatar nên được chuyển vào style.css sau) -->
    <img src="<?php echo BASE_URL . $avatar_path; ?>" alt="Avatar" 
         style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #eee; margin-bottom: 15px;">
    
    <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=account&action=updateAvatar" enctype="multipart/form-data">
        <label for="avatar_file">Thay ảnh mới:</label><br>
        <input type="file" id="avatar_file" name="avatar_file" accept="image/*" required class="form-control" style="max-width: 300px; display: inline-block;">
        <button type="submit" class="btn btn-secondary">Tải lên</button>
    </form>
</div>
<hr>
<!-- KẾT THÚC AVATAR -->


<h3>Cập nhật Thông tin Cá nhân</h3>

<!-- Form Cập nhật Profile (ĐÃ DỌN DẸP) -->
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