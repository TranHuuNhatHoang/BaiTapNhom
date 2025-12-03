<h1 style="color: blue;">Tạo Mã Giảm Giá Mới</h1>

<form action="<?php echo BASE_URL; ?>index.php?controller=admin&action=storeCoupon" method="POST" style="max-width: 500px;">

    <div class="form-group">
        <label for="coupon_code">Mã Giảm Giá (Code):</label>
        <input type="text" name="coupon_code" id="coupon_code" class="form-control" required maxlength="50">
    </div>

    <div class="form-group">
        <label for="discount_type">Loại Giảm Giá:</label>
        <select name="discount_type" id="discount_type" class="form-control" required>
            <option value="fixed">Số tiền cố định (VND)</option>
            <option value="percent">Phần trăm (%)</option>
        </select>
    </div>
    
    <div class="form-group">
        <label for="discount_value">Giá Trị Giảm:</label>
        <input type="number" name="discount_value" id="discount_value" class="form-control" required min="1">
    </div>

    <div class="form-group">
        <label for="expires_at">Ngày Hết Hạn:</label>
        <input type="datetime-local" name="expires_at" id="expires_at" class="form-control" required>
    </div>
    
    <div class="form-group">
        <label for="max_usage">Số Lượt Dùng Tối Đa:</label>
        <input type="number" name="max_usage" id="max_usage" class="form-control" required min="1">
    </div>
    
    <div class="form-group">
        <label for="is_public">
            <input type="checkbox" name="is_public" id="is_public" value="1" checked>
            Công khai mã giảm giá? (Hiển thị cho người dùng biết)
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Tạo Mã Giảm Giá</button>
    <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCoupons" class="btn btn-secondary">Hủy</a>
</form>