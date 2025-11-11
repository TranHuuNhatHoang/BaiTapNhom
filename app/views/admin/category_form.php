<?php 
/*
 * File trang Admin - Form Thêm/Sửa Danh mục (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .form-group, .form-control, .btn, .btn-primary
 *
 * Các biến được truyền từ AdminController@createCategory hoặc AdminController@editCategory:
 * $category (mảng, chỉ tồn tại khi 'edit')
 */

// Kiểm tra $category có tồn tại không (được truyền từ action 'editCategory')
$is_edit_mode = isset($category) && $category;
$page_title = $is_edit_mode ? "Sửa Danh mục" : "Thêm Danh mục";
$form_action = $is_edit_mode 
    ? (BASE_URL . "index.php?controller=admin&action=updateCategory") 
    : (BASE_URL . "index.php?controller=admin&action=storeCategory");
?>

<h1><?php echo $page_title; ?></h1>

<!-- Áp dụng class .btn -->
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCategories" class="btn btn-secondary" style="margin-bottom: 15px;">
    &laquo; Quay lại danh sách
</a>
<hr>

<!-- 
============================================================
 FORM (ĐÃ ÁP DỤNG CLASS)
============================================================
-->
<form method="POST" action="<?php echo $form_action; ?>" style="max-width: 600px;">
    
    <!-- Nếu là form SỬA, phải gửi kèm ID -->
    <?php if ($is_edit_mode): ?>
        <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
    <?php endif; ?>
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="category_name">Tên Danh mục:</label>
        <!-- Áp dụng class .form-control -->
        <input type="text" id="category_name" name="category_name" class="form-control" 
               value="<?php echo $is_edit_mode ? htmlspecialchars($category['category_name'] ?? '') : ''; ?>" required>
    </div>
    
    <!-- Áp dụng class .form-group -->
    <div class="form-group">
        <label for="description">Mô tả:</label>
        <!-- Áp dụng class .form-control -->
        <textarea id="description" name="description" rows="4" class="form-control"><?php echo $is_edit_mode ? htmlspecialchars($category['description'] ?? '') : ''; ?></textarea>
    </div>
    
    <!-- Áp dụng class .btn .btn-primary -->
    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">
        <?php echo $is_edit_mode ? "Cập nhật Danh mục" : "Lưu Danh mục"; ?>
    </button>
</form>