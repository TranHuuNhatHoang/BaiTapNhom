<h1>Thêm Sản phẩm mới</h1>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin">Quay lại danh sách</a>
<hr>

<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=store">
    
    <div style="margin-bottom: 10px;">
        <label for="product_name">Tên sản phẩm:</label><br>
        <input type="text" id="product_name" name="product_name" required style="width: 500px;">
    </div>

    <div style="margin-bottom: 10px;">
        <label for="brand_id">Thương hiệu:</label><br>
        <select id="brand_id" name="brand_id" required>
            <option value="">-- Chọn thương hiệu --</option>
            <?php foreach ($brands as $brand): ?>
                <option value="<?php echo $brand['brand_id']; ?>">
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
                <option value="<?php echo $category['category_id']; ?>">
                    <?php echo htmlspecialchars($category['category_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="price">Giá:</label><br>
        <input type="number" id="price" name="price" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="quantity">Số lượng:</label><br>
        <input type="number" id="quantity" name="quantity" required value="10">
    </div>

    <div style="margin-bottom: 10px;">
        <label for="main_image">Tên file ảnh:</label><br>
        <input type="text" id="main_image" name="main_image" placeholder="Vi du: dell-xps-9320.jpg">
        <small>(Tạm thời nhập tên file. Upload ảnh thật sẽ làm sau)</small>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="description">Mô tả:</label><br>
        <textarea id="description" name="description" rows="5" style="width: 500px;"></textarea>
    </div>
    
    <button type="submit" style="padding: 10px; background-color: blue; color: white;">Lưu Sản phẩm</button>
</form>