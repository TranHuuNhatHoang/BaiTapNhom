<?php 
// File: app/views/admin/contact_list.php
// Controller đã truyền: $contacts
?>

<h1>Quản lý Liên hệ Khách hàng</h1>

<div class="admin-nav" style="margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 10px;">
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin" class="btn btn-secondary">Tổng quan</a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listContacts" class="btn btn-primary">Quản lý Liên hệ</a> 
</div>
<hr>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên Khách hàng</th>
            <th>Email</th>
            <th>SĐT</th> <th>Nội dung Tóm tắt</th>
            <th>Ngày gửi</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($contacts)): ?>
            <tr><td colspan="8" style="text-align: center;">Không có liên hệ nào.</td></tr>
        <?php else: ?>
            <?php foreach ($contacts as $contact): ?>
                <tr>
                    <td>#<?php echo $contact['contact_id']; ?></td>
                    <td><?php echo htmlspecialchars($contact['name']); ?></td>
                    <td><a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a></td>
                    <td><a href="tel:<?php echo htmlspecialchars($contact['phone']); ?>"><?php echo htmlspecialchars($contact['phone']); ?></a></td> <td>
                        <?php 
                        // Hiển thị tóm tắt nội dung
                        echo htmlspecialchars(substr($contact['message'], 0, 80)) . (strlen($contact['message']) > 80 ? '...' : '');
                        ?>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo htmlspecialchars($contact['status']); ?>">
                            <?php 
                                if ($contact['status'] == 'new') echo 'Mới';
                                elseif ($contact['status'] == 'pending') echo 'Đang xử lý';
                                else echo 'Đã phản hồi';
                            ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=updateContactStatus" style="display: flex; gap: 5px;">
                            <input type="hidden" name="contact_id" value="<?php echo $contact['contact_id']; ?>">
                            <select name="new_status" class="form-control" style="width: auto;">
                                <option value="new" <?php echo ($contact['status'] == 'new') ? 'selected' : ''; ?>>Mới</option>
                                <option value="pending" <?php echo ($contact['status'] == 'pending') ? 'selected' : ''; ?>>Đang xử lý</option>
                                <option value="resolved" <?php echo ($contact['status'] == 'resolved') ? 'selected' : ''; ?>>Đã phản hồi</option>
                            </select>
                            <button type="submit" class="btn btn-small btn-primary">Lưu</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<style>
/* (Giữ nguyên CSS cho status-badge đã cung cấp trước đó) */
.status-badge { padding: 3px 8px; border-radius: 4px; font-size: 0.9em; font-weight: bold; }
.status-new { background-color: #dc3545; color: white; }
.status-pending { background-color: #ffc107; color: #333; }
.status-resolved { background-color: #28a745; color: white; }
</style>