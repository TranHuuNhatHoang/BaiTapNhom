<?php
// File: app/views/admin/dashboard.php
// Controller đã truyền các biến:
// $order_stats, $new_users, $new_contacts, 
// $chart_labels_json, $chart_values_json,
// $latest_orders, $latest_users
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h1 class="dashboard-title">✨ Tổng quan Quản trị (Admin Dashboard)</h1>
<hr class="dashboard-hr">

<div class="admin-nav" style="margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 10px;">
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin" class="btn btn-primary">Tổng quan</a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts" class="btn btn-secondary">Quản lý Sản phẩm</a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCoupons" class="btn btn-secondary">Quản lý Mã Giảm Giá</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listContacts" class="btn btn-danger">
        Quản lý Liên hệ (<?php echo $new_contacts ?? 0; ?> mới) 
    </a> 
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands" class="btn btn-secondary">Quản lý Thương hiệu</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCategories" class="btn btn-secondary">Quản lý Danh mục</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listOrders" class="btn btn-secondary">Quản lý Đơn hàng</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listUsers" class="btn btn-secondary">Quản lý Người dùng</a>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listReviews" class="btn btn-secondary">Đánh giá</a>
</div>

<h3 class="section-heading">Thống kê Nhanh</h3>
<div class="info-card-grid">
    
    <div class="info-card revenue">
        <i class="fas fa-money-bill-wave icon"></i>
        <div class="details">
            <p class="title">Tổng Doanh thu</p>
            <p class="value"><?php echo number_format($order_stats['total_revenue']); ?> ₫</p>
            <small>(Đơn đã Hoàn thành)</small>
        </div>
    </div>
    
    <div class="info-card orders">
        <i class="fas fa-box-open icon"></i>
        <div class="details">
            <p class="title">Đơn hàng mới</p>
            <p class="value"><?php echo $order_stats['new_orders']; ?></p>
            <small>(Đơn hàng 'Chờ xử lý')</small>
        </div>
    </div>
    
    <div class="info-card contacts">
        <i class="fas fa-headset icon"></i>
        <div class="details">
            <p class="title">Liên hệ mới</p>
            <p class="value"><?php echo $new_contacts ?? 0; ?></p>
            <small>(Chưa được xử lý)</small>
        </div>
    </div>
    
    <div class="info-card users">
        <i class="fas fa-user-plus icon"></i>
        <div class="details">
            <p class="title">Người dùng mới</p>
            <p class="value"><?php echo $new_users; ?></p>
            <small>(Đăng ký 7 ngày qua)</small>
        </div>
    </div>
</div>

<hr>

<h3 class="section-heading">📈 Biểu đồ Doanh thu 7 ngày qua</h3>
<div class="card chart-card">
    <div style="width: 100%; height: 300px;">
        <canvas id="revenueChart"></canvas>
    </div>
</div>
<script>
    // Lấy dữ liệu từ PHP (Controller đã truyền)
    const labels = <?php echo $chart_labels_json; ?>;
    const dataValues = <?php echo $chart_values_json; ?>;

    const data = {
        labels: labels,
        datasets: [{
            label: 'Doanh thu (VND)',
            backgroundColor: 'rgba(0, 123, 255, 0.7)',
            borderColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 2,
            data: dataValues,
            borderRadius: 5,
        }]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' ₫';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y.toLocaleString('vi-VN') + ' ₫';
                            }
                            return label;
                        }
                    }
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

<h3 class="section-heading">📋 Hoạt động Gần nhất</h3>
<div class="activity-grid">
    
    <div class="card activity-card">
        <h4>Đơn hàng mới nhất</h4>
        <div class="table-responsive">
            <table class="table table-compact">
                <thead> <tr> <th>ID</th> <th>Khách hàng</th> <th>Tổng tiền</th> <th>Trạng thái</th> </tr> </thead>
                <tbody>
                    <?php if (empty($latest_orders)): ?>
                        <tr><td colspan="4" style="text-align: center;">Không có đơn hàng nào gần đây.</td></tr>
                    <?php else: ?>
                        <?php foreach ($latest_orders as $order): ?>
                        <tr>
                            <td>
                                <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=orderDetail&id=<?php echo $order['order_id']; ?>">
                                    #<?php echo $order['order_id']; ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                            <td><?php echo number_format($order['total_amount']); ?> ₫</td>
                            <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card activity-card">
        <h4>Người dùng mới đăng ký</h4>
        <div class="table-responsive">
            <table class="table table-compact">
                <thead> <tr> <th>ID</th> <th>Họ Tên</th> <th>Email</th> <th>Ngày ĐK</th> </tr> </thead>
                <tbody>
                    <?php if (empty($latest_users)): ?>
                         <tr><td colspan="4" style="text-align: center;">Không có người dùng mới gần đây.</td></tr>
                    <?php else: ?>
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
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>