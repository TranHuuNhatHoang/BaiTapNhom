<?php
// Kiểm tra xem đây là form SỬA hay form THÊM MỚI
// (Vì Controller 'edit' đã truyền biến $product vào)
$is_edit_mode = isset($product) && $product;
$page_title = $is_edit_mode ? "Sửa Sản phẩm" : "Thêm Sản phẩm mới";

// Đặt action cho form
$form_action = $is_edit_mode 
    ? (BASE_URL . "index.php?controller=admin&action=update") 
    : (BASE_URL . "index.php?controller=admin&action=store");
?>

<h1><?php echo $page_title; ?></h1>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin">Quay lại danh sách</a>
<hr>

<form method="POST" action="<?php echo $form_action; ?>">
    
    <?php if ($is_edit_mode): ?>
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
    <?php endif; ?>

    <div style="margin-bottom: 10px;">
        <label for="product_name">Tên sản phẩm:</label><br>
        <input type="text" id="product_name" name="product_name" required style="width: 500px;" 
               value="<?php echo $is_edit_mode ? htmlspecialchars($product['product_name']) : ''; ?>">
    </div>

    <div style="margin-bottom: 10px;">
        <label for="brand_id">Thương hiệu:</label><br>
        <select id="brand_id" name="brand_id" required>
            <option value="">-- Chọn thương hiệu --</option>
            <?php foreach ($brands as $brand): ?>
                <option value="<?php echo $brand['brand_id']; ?>"
                    <?php echo ($is_edit_mode && $brand['brand_id'] == $product['brand_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="category_id">Danh mục:</label><br>
        <select id="category_id" name="category_id" required>
            <option value="">-- Chọn danh mục --</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['category_id']; ?>"
                    <?php echo ($is_edit_mode && $category['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['category_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="price">Giá:</label><br>
        <input type="number" id="price" name="price" required 
               value="<?php echo $is_edit_mode ? $product['price'] : ''; ?>">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="quantity">Số lượng:</label><br>
        <input type="number" id="quantity" name="quantity" required 
               value="<?php echo $is_edit_mode ? $product['quantity'] : '10'; ?>">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="main_image">Tên file ảnh:</label><br>
        <input type="text" id="main_image" name="main_image" placeholder="Vi du: dell-xps-9320.jpg"
               value="<?php echo $is_edit_mode ? htmlspecialchars($product['main_image']) : ''; ?>">
    </div>
    
    <div style="margin-bottom: 10px;">
        <label for="description">Mô tả:</label><br>
        <textarea id="description" name="description" rows="5" style="width: 500px;"><?php echo $is_edit_mode ? htmlspecialchars($product['description']) : ''; ?></textarea>
    </div>
    
    <button type="submit" style="padding: 10px; background-color: blue; color: white;">
        <?php echo $is_edit_mode ? "Cập nhật Sản phẩm" : "Lưu Sản phẩm"; ?>
    </button>
</form>