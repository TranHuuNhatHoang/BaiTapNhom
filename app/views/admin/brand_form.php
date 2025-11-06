<?php
// Kiểm tra $brand có tồn tại không (được truyền từ action 'editBrand')
$is_edit_mode = isset($brand) && $brand;
$page_title = $is_edit_mode ? "Sửa Thương hiệu" : "Thêm Thương hiệu";
$form_action = $is_edit_mode 
    ? (BASE_URL . "index.php?controller=admin&action=updateBrand") 
    : (BASE_URL . "index.php?controller=admin&action=storeBrand");
?>
<h1><?php echo $page_title; ?></h1>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listBrands">Quay lại danh sách</a>
<hr>
<form method="POST" action="<?php echo $form_action; ?>">
    <?php if ($is_edit_mode): ?>
        <input type="hidden" name="brand_id" value="<?php echo $brand['brand_id']; ?>">
    <?php endif; ?>
    
    <div>
        <label>Tên Thương hiệu:</label><br>
        <input type="text" name="brand_name" value="<?php echo $is_edit_mode ? htmlspecialchars($brand['brand_name'] ?? '') : ''; ?>" required>
    </div>
    <div>
        <label>Tên file Logo:</label><br>
        <input type="text" name="logo" value="<?php echo $is_edit_mode ? htmlspecialchars($brand['logo'] ?? '') : ''; ?>" placeholder="vi du: dell.png">
    </div>
    <div>
        <label>Mô tả:</label><br>
        <textarea name="description"><?php echo $is_edit_mode ? htmlspecialchars($brand['description'] ?? '') : ''; ?></textarea>
    </div>
    <button type="submit"><?php echo $is_edit_mode ? "Cập nhật" : "Lưu"; ?></button>
</form>