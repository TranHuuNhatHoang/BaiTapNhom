<h1 style="color: blue;">Quản lý Mã Giảm Giá</h1>

<div class="admin-nav" style="margin-bottom: 15px;">
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=createCoupon" class="btn btn-success">
        + Tạo Mã Giảm Giá Mới
    </a>
</div>
<hr>

<?php display_flash_message(); ?>

<table class="table" style="width: 100%;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Mã Code</th>
            <th>Loại Giảm</th>
            <th>Giá trị</th>
            <th>Hạn sử dụng</th>
            <th>Đã dùng/Tối đa</th>
            <th>Công khai</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($coupons as $coupon): 
            $is_expired = strtotime($coupon['expires_at']) < time();
            $status_class = $is_expired ? 'text-danger' : '';
        ?>
        <tr class="<?php echo $status_class; ?>">
            <td><?php echo $coupon['coupon_id']; ?></td>
            <td><strong><?php echo htmlspecialchars($coupon['coupon_code']); ?></strong></td>
            <td><?php echo $coupon['discount_type'] == 'fixed' ? 'Tiền mặt' : 'Phần trăm'; ?></td>
            <td>
                <?php 
                    echo number_format($coupon['discount_value']);
                    echo $coupon['discount_type'] == 'fixed' ? ' VND' : ' %';
                ?>
            </td>
            <td><?php echo $is_expired ? '<span class="text-danger">ĐÃ HẾT HẠN</span>' : date('d/m/Y H:i', strtotime($coupon['expires_at'])); ?></td>
            <td><?php echo $coupon['usage_count'] . ' / ' . $coupon['max_usage']; ?></td>
            <td><?php echo $coupon['is_public'] ? '✅ CÔNG KHAI' : '❌ Riêng tư'; ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteCoupon&id=<?php echo $coupon['coupon_id']; ?>" 
                   onclick="return confirm('Xác nhận xóa mã này?');" class="btn btn-danger btn-sm">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>