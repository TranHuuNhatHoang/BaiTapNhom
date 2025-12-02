<?php 
/*
 * File trang Admin - Form Sửa Người dùng (ĐÃ CẬP NHẬT DROPDOWN ĐỊA CHỈ)
 * Các biến được truyền: $user, $provinces, $districts, $wards
 */
?>

<h1>Sửa thông tin Người dùng</h1>

<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers" class="btn btn-secondary" style="margin-bottom: 15px;">
    &laquo; Quay lại danh sách
</a>
<hr>

<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=updateUser" style="max-width: 600px;">
    
    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
    
    <div class="form-group">
        <label for="full_name">Họ và Tên:</label>
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
    
    <!-- 
    ============================================================
     CẬP NHẬT: 3 DROPDOWN ĐỊA CHỈ (Có ID để JS hoạt động)
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
        <input type="text" id="address" name="address" class="form-control" 
               value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" placeholder="VD: 123 Nguyễn Trãi">
    </div>
    <!-- KẾT THÚC CẬP NHẬT -->
    
    <div class="form-group">
        <label for="role">Vai trò (Role):</label>
        <select id="role" name="role" class="form-control" 
            <?php echo $user['user_id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
        <?php if($user['user_id'] == $_SESSION['user_id']): ?>
            <small>(Không thể tự đổi vai trò của mình)</small>
        <?php endif; ?>
    </div>
    
    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Cập nhật Người dùng</button>
</form>