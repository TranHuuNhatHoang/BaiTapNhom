<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Người dùng</h2>

<a href="<?php echo BASE_URL; ?>index.php?controller=admin">Quản lý Sản phẩm</a> | 
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands">Quản lý Thương hiệu</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCategories">Quản lý Danh mục</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders">Quản lý Đơn hàng</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers" style="font-weight: bold;">Quản lý Người dùng</a>
<hr>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Họ Tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Vai trò (Role)</th>
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
                <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=updateUserRole" style="margin: 0;">
                    
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                    
                    <select name="role" 
                        <?php echo $user['user_id'] == $_SESSION['user_id'] ? 'disabled' : ''; // Admin không thể đổi vai trò của chính mình ?>>
                        
                        <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                    
                    <?php if($user['user_id'] != $_SESSION['user_id']): ?>
                        <button type="submit" style="font-size: 0.8em; padding: 2px 5px;">Lưu</button>
                    <?php endif; ?>
                </form>
            </td>
            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
            <td>
                <?php if($user['user_id'] != $_SESSION['user_id']): ?>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteUser&id=<?php echo $user['user_id']; ?>" 
                       onclick="return confirm('Cảnh báo: Bạn có chắc muốn XÓA user này? (Tác vụ không thể hoàn tác)');" 
                       style="color: red;">
                        Xóa
                    </a>
                <?php else: ?>
                    (Bạn)
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>