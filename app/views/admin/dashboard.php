<?php
// (This file is app/views/admin/dashboard.php)
// Controller đã truyền các biến:
// $order_stats, $new_users, 
// $chart_labels_json, $chart_values_json,
// $latest_orders, $latest_users
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Tổng quan (Dashboard)</h2>

<a href="<?php echo BASE_URL; ?>index.php?controller=admin" style="font-weight: bold;">Tổng quan</a> | 
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts">Quản lý Sản phẩm</a> | 
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands">Quản lý Thương hiệu</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCategories">Quản lý Danh mục</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders">Quản lý Đơn hàng</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers">Quản lý Người dùng</a> |
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listReviews">Đánh giá</a>
<hr>

<h3>Thống kê Nhanh</h3>
<div style="display: flex; gap: 20px;">
    <div style="border: 1px solid #ccc; padding: 20px; background: #f9f9f9;">
        <h4>Tổng Doanh thu</h4>
        <p style="font-size: 24px; color: green; margin: 0;">
            <?php echo number_format($order_stats['total_revenue']); ?> VND
        </p>
        <small>(Chỉ tính các đơn đã Hoàn thành)</small>
    </div>
    <div style="border: 1px solid #ccc; padding: 20px; background: #f9f9f9;">
        <h4>Đơn hàng mới</h4>
        <p style="font-size: 24px; color: orange; margin: 0;">
            <?php echo $order_stats['new_orders']; ?>
        </p>
        <small>(Đơn hàng đang 'Chờ xử lý')</small>
    </div>
    <div style="border: 1px solid #ccc; padding: 20px; background: #f9f9f9;">
        <h4>Người dùng mới</h4>
        <p style="font-size: 24px; color: blue; margin: 0;">
            <?php echo $new_users; ?>
        </p>
        <small>(Đăng ký trong 7 ngày qua)</small>
    </div>
</div>

<hr>
<h3>Doanh thu 7 ngày qua (Đơn đã hoàn thành)</h3>
<div style="width: 100%; max-width: 800px;">
    <canvas id="revenueChart"></canvas>
</div>

<script>
    // Lấy dữ liệu từ PHP (Controller đã truyền)
    const labels = <?php echo $chart_labels_json; ?>;
    const dataValues = <?php echo $chart_values_json; ?>;

    const data = {
        labels: labels,
        datasets: [{
            label: 'Doanh thu (VND)',
            backgroundColor: 'rgba(0, 123, 255, 0.2)',
            borderColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 1,
            data: dataValues,
        }]
    };

    const config = {
        type: 'bar', // Kiểu biểu đồ (bar, line, pie...)
        data: data,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    // Vẽ biểu đồ
    new Chart(
        document.getElementById('revenueChart'),
        config
    );
</script>

<hr>
<div style="display: flex; gap: 20px; margin-top: 20px;">
    
    <div style="flex: 1;">
        <h3>Đơn hàng mới nhất</h3>
        <table border="1" style="width: 100%; border-collapse: collapse;">
            <thead> <tr> <th>ID</th> <th>Khách hàng</th> <th>Tổng tiền</th> <th>Trạng thái</th> </tr> </thead>
            <tbody>
                <?php foreach ($latest_orders as $order): ?>
                <tr>
                    <td>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=orderDetail&id=<?php echo $order['order_id']; ?>">
                            #<?php echo $order['order_id']; ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                    <td><?php echo number_format($order['total_amount']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div style="flex: 1;">
        <h3>Người dùng mới nhất</h3>
        <table border="1" style="width: 100%; border-collapse: collapse;">
            <thead> <tr> <th>ID</th> <th>Họ Tên</th> <th>Email</th> <th>Ngày ĐK</th> </tr> </thead>
            <tbody>
                <?php foreach ($latest_users as $user): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=editUser&id=<?php echo $user['user_id']; ?>">
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>