<?php
$is_edit_mode = isset($category) && $category;
$page_title = $is_edit_mode ? "Sửa Danh mục" : "Thêm Danh mục";
$form_action = $is_edit_mode 
    ? (BASE_URL . "index.php?controller=admin&action=updateCategory") 
    : (BASE_URL . "index.php?controller=admin&action=storeCategory");
?>
<h1><?php echo $page_title; ?></h1>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listCategories">Quay lại danh sách</a>
<hr>
<form method="POST" action="<?php echo $form_action; ?>">
    <?php if ($is_edit_mode): ?>
        <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
    <?php endif; ?>
    
    <div>
        <label>Tên Danh mục:</label><br>
        <input type="text" name="category_name" value="<?php echo $is_edit_mode ? htmlspecialchars($category['category_name'] ?? '') : ''; ?>" required>
    </div>
    <div>
        <label>Mô tả:</label><br>
        <textarea name="description"><?php echo $is_edit_mode ? htmlspecialchars($category['description'] ?? '') : ''; ?></textarea>
    </div>
    <button type="submit"><?php echo $is_edit_mode ? "Cập nhật" : "Lưu"; ?></button>
</form>