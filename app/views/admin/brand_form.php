<?php 
/*
 * File trang Admin - Form Thêm/Sửa Thương hiệu (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .form-group, .form-control, .btn, .btn-primary
 *
 * Các biến được truyền từ AdminController@createBrand hoặc AdminController@editBrand:
 * $brand (mảng, chỉ tồn tại khi 'edit')
 */

// Kiểm tra $brand có tồn tại không (được truyền từ action 'editBrand')
$is_edit_mode = isset($brand) && $brand;
$page_title = $is_edit_mode ? "Sửa Thương hiệu" : "Thêm Thương hiệu";
$form_action = $is_edit_mode 
    ? (BASE_URL . "index.php?controller=admin&action=updateBrand") 
    : (BASE_URL . "index.php?controller=admin&action=storeBrand");
?>

<h1><?php echo $page_title; ?></h1>

<!-- Áp dụng class .btn -->
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands" class="btn btn-secondary" style="margin-bottom: 15px;">
    &laquo; Quay lại danh sách
</a>
<hr>

<!-- 
============================================================
 FORM (ĐÃ ÁP DỤNG CLASS)
============================================================
-->
<!-- Form phải có 'enctype' cho upload (GĐ 15) -->
<form method="POST" action="<?php echo $form_action; ?>" enctype="multipart/form-data" style="max-width: 600px;">
    
    <!-- Nếu là form SỬA, phải gửi kèm ID -->
    <?php if ($is_edit_mode): ?>
        <input type="hidden" name="brand_id" value="<?php echo $brand['brand_id']; ?>">
    <?php endif; ?>
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="brand_name">Tên Thương hiệu:</label>
        <!-- Áp dụng class .form-control -->
        <input type="text" id="brand_name" name="brand_name" class="form-control" 
               value="<?php echo $is_edit_mode ? htmlspecialchars($brand['brand_name'] ?? '') : ''; ?>" required>
    </div>
    
    <!-- Input Upload Ảnh (GĐ 15) -->
    <div class="form-group">
        <label for="logo">Logo (Tên file hoặc Upload):</label>
        <!-- Áp dụng class .form-control -->
        <input type="file" id="logo" name="logo" class="form-control">
        
        <?php if ($is_edit_mode && !empty($brand['logo'])): ?>
            <br>
            <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($brand['logo']); ?>" 
                 alt="<?php echo htmlspecialchars($brand['brand_name']); ?>"
                 height="50" style="margin-top: 10px; border-radius: 5px; background: #eee; padding: 5px;">
            <!-- Input ẩn để giữ logo cũ nếu không tải logo mới -->
            <input type="hidden" name="current_logo" 
                   value="<?php echo htmlspecialchars($brand['logo']); ?>">
        <?php else: ?>
             <input type="hidden" name="current_logo" value="">
        <?php endif; ?>
    </div>
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="description">Mô tả:</label>
        <!-- Áp dụng class .form-control -->
        <textarea id="description" name="description" rows="4" class="form-control"><?php echo $is_edit_mode ? htmlspecialchars($brand['description'] ?? '') : ''; ?></textarea>
    </div>
    
    <!-- Áp dụng class .btn .btn-primary -->
    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">
        <?php echo $is_edit_mode ? "Cập nhật Thương hiệu" : "Lưu Thương hiệu"; ?>
    </button>
</form>