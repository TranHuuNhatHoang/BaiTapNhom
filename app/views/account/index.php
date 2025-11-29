<?php 
/*
 * File trang Tài khoản (ĐÃ CẬP NHẬT: Thêm Dropdown Địa chỉ)
 */
?>

<!-- (Phần Header, Avatar... giữ nguyên) -->
<h1>Tài khoản của tôi</h1>
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=history" class="btn btn-secondary">Xem Lịch sử Đơn hàng</a>
<a href="<?php echo BASE_URL; ?>index.php?controller=account&action=changePassword" class="btn btn-danger" style="margin-left: 10px;">Đổi mật khẩu</a>
<hr>

<!-- Khu vực Avatar -->
<div class="avatar-section form-group">
    <h3>Ảnh đại diện</h3>
    <?php 
    $avatar_path = 'public/uploads/avatars/' . ($user['avatar'] ?? 'default.png');
    if (!file_exists(ROOT_PATH . '/' . $avatar_path) || empty($user['avatar'])) {
        $avatar_path = 'public/images/default_avatar.png'; 
    }
    ?>
    <img src="<?php echo BASE_URL . $avatar_path; ?>" alt="Avatar" 
         style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #eee; margin-bottom: 15px;">
    <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=account&action=updateAvatar" enctype="multipart/form-data">
        <label for="avatar_file">Thay ảnh mới:</label><br>
        <input type="file" id="avatar_file" name="avatar_file" accept="image/*" required class="form-control" style="max-width: 300px; display: inline-block;">
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
    
    <!-- 
    ============================================================
     CẬP NHẬT: 3 DROPDOWN ĐỊA CHỈ (Có tự động chọn)
     (Sử dụng id="province", "district", "ward" để main.js hoạt động)
    ============================================================
    -->
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
            <!-- Hiển thị danh sách nếu đã có data từ Controller (vì user đã chọn trước đó) -->
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
    <!-- KẾT THÚC CẬP NHẬT -->
    
    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Cập nhật thông tin</button>
</form>