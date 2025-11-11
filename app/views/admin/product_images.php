<?php 
/*
 * File trang Admin - Quản lý Thư viện Ảnh (ĐÃ DỌN DẸP CSS)
 * Áp dụng: .btn, .btn-secondary, .btn-primary, .btn-danger, .form-control
 *
 * Các biến được truyền từ AdminController@manageImages:
 * $product (mảng), $images (mảng)
 */
?>

<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Thư viện ảnh cho: <?php echo htmlspecialchars($product['product_name']); ?></h2>

<!-- Áp dụng class .btn -->
<a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=listProducts" class="btn btn-secondary" style="margin-bottom: 15px;">
    &laquo; Quay lại Danh sách Sản phẩm
</a>
<hr>

<!-- 
============================================================
 FORM UPLOAD (ĐÃ ÁP DỤNG CLASS)
============================================================
-->
<h3>Upload ảnh mới</h3>
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=uploadImage" 
      enctype="multipart/form-data" class="form-group" 
      style="display: flex; gap: 10px; max-width: 500px;">
      
    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
    
    <!-- Áp dụng class .form-control -->
    <input type="file" name="product_image_file" class="form-control" required>
    
    <!-- Áp dụng class .btn -->
    <button type="submit" class="btn btn-primary">Tải lên</button>
</form>
<hr>

<!-- 
============================================================
 DANH SÁCH ẢNH (ĐÃ ÁP DỤNG CLASS)
============================================================
-->
<h3>Ảnh hiện tại</h3>
<div style="display: flex; flex-wrap: wrap; gap: 15px;">
    
    <!-- Ảnh chính (Giữ style tùy chỉnh) -->
    <div class="image-thumbnail" style="border: 2px solid #007bff; padding: 5px; border-radius: 5px; text-align: center;">
        <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($product['main_image']); ?>" 
             alt="Ảnh chính"
             style="height: 150px; width: 150px; object-fit: cover; border-radius: 4px;">
        <br>
        <strong style="font-size: 0.9em; color: #007bff;">(Ảnh chính)</strong>
    </div>

    <!-- Ảnh phụ (Lặp) -->
    <?php foreach ($images as $image): ?>
        <div class="image-thumbnail" style="border: 1px solid #ccc; padding: 5px; border-radius: 5px; text-align: center;">
            <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($image['image_url']); ?>" 
                 alt="Ảnh phụ"
                 style="height: 150px; width: 150px; object-fit: cover; border-radius: 4px;">
            <br>
            
            <!-- Áp dụng class .btn -->
            <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteImage&image_id=<?php echo $image['image_id']; ?>&product_id=<?php echo $product['product_id']; ?>"
               onclick="return confirm('Bạn có chắc muốn xóa ảnh này?');" 
               class="btn btn-danger" style="font-size: 0.8em; padding: 3px 8px; margin-top: 5px;">
                Xóa ảnh này
            </a>
        </div>
    <?php endforeach; ?>
</div>