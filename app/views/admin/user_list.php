<?php 
/*
 * File trang Admin - Quản lý Người dùng (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .table, .btn, .btn-primary, .btn-secondary, .btn-danger, .form-control
 *
 * Các biến được truyền từ AdminController@listUsers:
 * $users (mảng)
 */
?>

<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Người dùng</h2>

<!-- 
============================================================
 THANH ĐIỀU HƯỚNG ADMIN (ĐÃ ÁP DỤNG CLASS .btn)
============================================================
-->
<div class="admin-nav" style="margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 10px;">
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin" class="btn btn-secondary">Tổng quan</a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts" class="btn btn-secondary">Quản lý Sản phẩm</a> 
     <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCoupons" class="btn btn-secondary">Quản lý Mã Giảm Giá</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listContacts" class="btn btn-danger">
        Quản lý Liên hệ 
    </a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands" class="btn btn-secondary">Quản lý Thương hiệu</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCategories" class="btn btn-secondary">Quản lý Danh mục</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders" class="btn btn-secondary">Quản lý Đơn hàng</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers" class="btn btn-primary">Quản lý Người dùng</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listReviews" class="btn btn-secondary">Đánh giá</a>
</div>
<hr>

<!-- 
============================================================
 BẢNG DANH SÁCH (ĐÃ ÁP DỤNG CLASS .table)
============================================================
-->
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Họ Tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th style="min-width: 180px;">Vai trò (Role)</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['user_id']; ?></td>
            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['phone'] ?? ''); ?></td>
            <td>
                <!-- 
                ============================================================
                 FORM CẬP NHẬT VAI TRÒ (ĐÃ ÁP DỤNG CLASS)
                ============================================================
                -->
                <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=updateUserRole" 
                      style="margin: 0; display: flex; gap: 5px;">
                    
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                    
                    <!-- Áp dụng class .form-control -->
                    <select name="role" class="form-control" style="padding: 5px; height: 36px;"
                        <?php echo $user['user_id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                        
                        <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                    
                    <!-- Chỉ hiển thị nút Lưu cho user khác -->
                    <?php if($user['user_id'] != $_SESSION['user_id']): ?>
                        <!-- Áp dụng class .btn -->
                        <button type="submit" class="btn btn-primary" style="font-size: 0.9em; padding: 5px 10px;">Lưu</button>
                    <?php endif; ?>
                </form>
            </td>
            <td style="white-space: nowrap;"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
            <td>
                <!-- Áp dụng class .btn -->
                <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=editUser&id=<?php echo $user['user_id']; ?>"
                   class="btn btn-secondary" style="font-size: 0.9em; padding: 5px;">
                    Sửa
                </a>
                
                <?php if($user['user_id'] != $_SESSION['user_id']): ?>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteUser&id=<?php echo $user['user_id']; ?>" 
                       onclick="return confirm('Cảnh báo: Bạn có chắc muốn XÓA user này? (Tác vụ không thể hoàn tác)');" 
                       class="btn btn-danger" style="font-size: 0.9em; padding: 5px; margin-left: 5px;">
                        Xóa
                    </a>
                <?php else: ?>
                    <span style="font-size: 0.9em; margin-left: 10px;">(Bạn)</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>