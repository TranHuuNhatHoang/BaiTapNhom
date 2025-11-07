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
<form method="POST" action="<?php echo $form_action; ?>" enctype="multipart/form-data">    
    <?php if ($is_edit_mode): ?>
        <input type="hidden" name="brand_id" value="<?php echo $brand['brand_id']; ?>">
    <?php endif; ?>
    
    <div style="margin-bottom: 10px;">
        <label>Tên Thương hiệu:</label><br>
        <input type="text" name="brand_name" value="<?php echo $is_edit_mode ? htmlspecialchars($brand['brand_name'] ?? '') : ''; ?>" required>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="logo">Logo Thương hiệu:</label><br>
        <input type="file" id="logo" name="logo"> 
        
        <?php 
            // SỬA: Kiểm tra biến $brand và trường 'logo' 
            if ($is_edit_mode && !empty($brand['logo'])): 
        ?>
            <br>
            <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($brand['logo']); ?>" height="100">
            <input type="hidden" name="current_logo" value="<?php echo htmlspecialchars($brand['logo']); ?>">
        <?php endif; ?>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>Mô tả:</label><br>
        <textarea name="description" style="width: 500px;"><?php echo $is_edit_mode ? htmlspecialchars($brand['description'] ?? '') : ''; ?></textarea>
    </div>
    
    <button type="submit" style="padding: 10px; background-color: blue; color: white;"><?php echo $is_edit_mode ? "Cập nhật" : "Lưu"; ?></button>
</form>