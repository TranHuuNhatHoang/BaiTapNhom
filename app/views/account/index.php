<?php 
/*
 * File trang Tài khoản (ĐÃ CẬP NHẬT: Thêm logic Xóa Avatar)
 */
?>

<h1>Tài khoản của tôi</h1>
<div class="account-actions">
    <a href="<?php echo BASE_URL; ?>index.php?controller=account&action=history" class="btn btn-secondary">Xem Lịch sử Đơn hàng</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=account&action=changePassword" class="btn btn-danger" style="margin-left: 10px;">Đổi mật khẩu</a>
</div>
<hr>

<div class="avatar-section form-group">
    <h3>Ảnh đại diện</h3>
    <?php 
    // Tên file avatar hiện tại từ CSDL (thường là 'default.png' nếu chưa upload)
    $avatar_filename = $user['avatar'] ?? 'default.png';
    $default_avatar = 'default.png';
    
    // 1. Đường dẫn tới file avatar trong thư mục uploads
    $uploaded_avatar_path = 'public/uploads/avatars/' . $avatar_filename;
    
    // 2. Kiểm tra file có tồn tại thật trong thư mục uploads không
    if (file_exists(ROOT_PATH . '/' . $uploaded_avatar_path) && $avatar_filename !== $default_avatar) {
        $display_path = BASE_URL . $uploaded_avatar_path;
        $is_custom_avatar = true;
    } else {
        // Nếu không tồn tại hoặc là default, dùng ảnh mặc định trong thư mục images
        $display_path = BASE_URL . 'public/images/default_avatar.png';
        $is_custom_avatar = false;
    }
    ?>
    <img src="<?php echo $display_path; ?>" alt="Avatar" 
        style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #eee; margin-bottom: 15px;">
        
    <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=account&action=updateAvatar" enctype="multipart/form-data">
        <label for="avatar_file">Thay ảnh mới:</label><br>
        <input type="file" id="avatar_file" name="avatar_file" accept="image/jpeg, image/png, image/gif, image/webp" required class="form-control" style="max-width: 300px; display: inline-block;">
        <button type="submit" class="btn btn-primary">Tải lên</button>
        
        <?php if ($is_custom_avatar): ?>
            <a href="<?php echo BASE_URL; ?>index.php?controller=account&action=deleteAvatar" 
               class="btn btn-danger" 
               onclick="return confirm('Bạn có chắc chắn muốn xóa ảnh đại diện và đặt lại mặc định?');"
               style="margin-left: 10px;">
                Xóa ảnh đại diện
            </a>
        <?php endif; ?>
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
        <label for="province">Tỉnh/Thành phố:</label>
        <select id="province" name="province_id" class="form-control">
            <option value="">-- Chọn Tỉnh/Thành --</option>
            <?php foreach ($provinces as $province): ?>
                <option value="<?php echo $province['province_id']; ?>"
                    <?php echo ($user['province_id'] == $province['province_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($province['province_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="district">Quận/Huyện:</label>
        <select id="district" name="district_id" class="form-control">
            <option value="">-- Chọn Quận/Huyện --</option>
            <?php if (!empty($districts)): ?>
                <?php foreach ($districts as $district): ?>
                    <option value="<?php echo $district['district_id']; ?>"
                        <?php echo ($user['district_id'] == $district['district_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($district['district_name']); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="ward">Phường/Xã:</label>
        <select id="ward" name="ward_code" class="form-control">
            <option value="">-- Chọn Phường/Xã --</option>
            <?php if (!empty($wards)): ?>
                <?php foreach ($wards as $ward): ?>
                    <option value="<?php echo $ward['ward_code']; ?>"
                        <?php echo ($user['ward_code'] == $ward['ward_code']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($ward['ward_name']); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="address">Địa chỉ cụ thể (Số nhà, Tên đường):</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" class="form-control" placeholder="VD: 123 Nguyễn Trãi">
    </div>
    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Cập nhật thông tin</button>
</form>