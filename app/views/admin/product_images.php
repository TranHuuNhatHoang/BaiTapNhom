<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Thư viện ảnh cho: <?php echo htmlspecialchars($product['product_name']); ?></h2>
<a href="<?php echo BASE_URL; ?>index.php?controller=admin">Quay lại Danh sách Sản phẩm</a>
<hr>

<h3>Upload ảnh mới</h3>
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=uploadImage" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
    <input type="file" name="product_image_file" required>
    <button type="submit">Tải lên</button>
</form>
<hr>

<h3>Ảnh hiện tại</h3>
<div style="display: flex; flex-wrap: wrap; gap: 15px;">
    
    <div style="border: 2px solid blue; padding: 5px;">
        <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" height="150">
        <br><strong>(Ảnh chính)</strong>
    </div>

    <?php foreach ($images as $image): ?>
        <div style="border: 1px solid #ccc; padding: 5px;">
            <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($image['image_url']); ?>" height="150">
            <br>
            <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteImage&image_id=<?php echo $image['image_id']; ?>&product_id=<?php echo $product['product_id']; ?>"
               onclick="return confirm('Bạn có chắc muốn xóa ảnh này?');" 
               style="color: red; display: block; text-align: center; margin-top: 5px;">
                Xóa ảnh này
            </a>
        </div>
    <?php endforeach; ?>
</div>