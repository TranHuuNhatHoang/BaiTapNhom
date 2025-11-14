<?php
/*
 * File trang Admin - Form Thêm/Sửa Sản phẩm (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .form-group, .form-control, .btn, .btn-primary, .btn-secondary
 *
 * Các biến được truyền từ AdminController@create hoặc AdminController@edit:
 * $brands (mảng), $categories (mảng)
 * $product (mảng, chỉ tồn tại khi 'edit')
 */

// Kiểm tra xem đây là form SỬA hay form THÊM MỚI
$is_edit_mode = isset($product) && $product;
$page_title = $is_edit_mode ? "Sửa Sản phẩm" : "Thêm Sản phẩm mới";

// (Lấy URL của trang trước đó, ví dụ: ...listProducts&page=3)
$return_url = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . 'index.php?controller=admin&action=listProducts');

// Đặt action cho form (trỏ đến 'update' hoặc 'store')
$form_action = $is_edit_mode 
    ? (BASE_URL . "index.php?controller=admin&action=update") 
    : (BASE_URL . "index.php?controller=admin&action=store");
?>

<h1><?php echo $page_title; ?></h1>

<!-- Áp dụng class .btn -->
<a href="<?php echo htmlspecialchars($return_url); ?>" class="btn btn-secondary" style="margin-bottom: 15px;">
    &laquo; Quay lại danh sách
</a>
<hr>

<!-- 
============================================================
 FORM (ĐÃ ÁP DỤNG CLASS)
============================================================
-->
<!-- Form phải có 'enctype' cho upload (GĐ 15) -->
<form method="POST" action="<?php echo $form_action; ?>" enctype="multipart/form-data" style="max-width: 700px;">
    
    <!-- Nếu là form SỬA, phải gửi kèm ID -->
    <?php if ($is_edit_mode): ?>
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
    <?php endif; ?>

    <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($return_url); ?>">

    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="product_name">Tên sản phẩm:</label>
        <!-- Áp dụng class .form-control -->
        <input type="text" id="product_name" name="product_name" class="form-control" required 
               value="<?php echo $is_edit_mode ? htmlspecialchars($product['product_name'] ?? '') : ''; ?>">
    </div>

    <!-- Dropdown Thương hiệu -->
    <div class="form-group">
        <label for="brand_id">Thương hiệu:</label>
        <!-- Áp dụng class .form-control -->
        <select id="brand_id" name="brand_id" class="form-control" required>
            <option value="">-- Chọn thương hiệu --</option>
            <?php foreach ($brands as $brand): ?>
                <option value="<?php echo $brand['brand_id']; ?>"
                    <?php echo ($is_edit_mode && isset($product['brand_id']) && $brand['brand_id'] == $product['brand_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Dropdown Danh mục -->
    <div class="form-group">
        <label for="category_id">Danh mục:</label>
        <!-- Áp dụng class .form-control -->
        <select id="category_id" name="category_id" class="form-control" required>
            <option value="">-- Chọn danh mục --</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['category_id']; ?>"
                    <?php echo ($is_edit_mode && isset($product['category_id']) && $category['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['category_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="price">Giá:</label>
        <!-- Áp dụng class .form-control -->
        <input type="number" id="price" name="price" class="form-control" required 
               value="<?php echo $is_edit_mode ? $product['price'] : ''; ?>">
    </div>
    
    <div class="form-group">
        <label for="quantity">Số lượng:</label>
        <!-- Áp dụng class .form-control -->
        <input type="number" id="quantity" name="quantity" class="form-control" required 
               value="<?php echo $is_edit_mode ? $product['quantity'] : '10'; ?>">
    </div>
    
    <!-- Input Upload Ảnh (GĐ 15) -->
    <div class="form-group">
        <label for="main_image">Ảnh đại diện:</label>
        <!-- Áp dụng class .form-control -->
        <input type="file" id="main_image" name="main_image" class="form-control" <?php echo $is_edit_mode ? '' : 'required'; ?>>
        
        <?php if ($is_edit_mode && !empty($product['main_image'])): ?>
            <br>
            <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
                 height="100" style="margin-top: 10px; border-radius: 5px;">
            <!-- Input ẩn để giữ ảnh cũ nếu không tải ảnh mới -->
            <input type="hidden" name="current_main_image" 
                   value="<?php echo htmlspecialchars($product['main_image']); ?>">
        <?php else: ?>
             <input type="hidden" name="current_main_image" value="">
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="description">Mô tả:</label>
        <!-- Áp dụng class .form-control -->
        <textarea id="description" name="description" rows="5" class="form-control"><?php echo $is_edit_mode ? htmlspecialchars($product['description'] ?? '') : ''; ?></textarea>
    </div>
    
    <!-- Áp dụng class .btn .btn-primary -->
    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">
        <?php echo $is_edit_mode ? "Cập nhật Sản phẩm" : "Lưu Sản phẩm"; ?>
    </button>
</form>