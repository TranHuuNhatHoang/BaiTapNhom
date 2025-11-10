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