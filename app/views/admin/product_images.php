<?php 
/*
 * File trang Admin - Quản lý Thư viện Ảnh
 * (ĐÃ DỌN DẸP CSS + SỬA LỖI PHÂN TRANG)
 *
 * Các biến được truyền từ AdminController@manageImages:
 * $product (mảng), $images (mảng), $return_url (string)
 */
?>

<h1 style="color: blue;">Trang Quản trị Admin</h1>
<h2>Quản lý Thư viện ảnh cho: <?php echo htmlspecialchars($product['product_name']); ?></h2>

<!-- 
============================================================
 SỬA LỖI 1/3: Link "Quay lại" (dùng $return_url)
 (Áp dụng class .btn)
============================================================
-->
<a href="<?php echo htmlspecialchars($return_url); ?>" class="btn btn-secondary" style="margin-bottom: 15px;">
    &laquo; Quay lại Danh sách Sản phẩm
</a>
<hr>

<!-- 
============================================================
 FORM UPLOAD (ĐÃ ÁP DỤNG CLASS + SỬA LỖI 2/3)
============================================================
-->
<h3>Upload ảnh mới</h3>
<form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=admin&action=uploadImage" 
      enctype="multipart/form-data" class="form-group" 
      style="display: flex; gap: 10px; max-width: 500px; align-items: flex-end;">
      
    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
    
    <!-- SỬA LỖI 2/3: Thêm Input ẩn để giữ URL quay lại -->
    <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($return_url); ?>">
    
    <div style="flex: 1;">
        <label for="product_image_file">Chọn file ảnh:</label>
        <!-- Áp dụng class .form-control -->
        <input type="file" id="product_image_file" name="product_image_file" class="form-control" required>
    </div>
    
    <!-- Áp dụng class .btn -->
    <button type="submit" class="btn btn-primary">Tải lên</button>
</form>
<hr>

<!-- 
============================================================
 DANH SÁCH ẢNH (ĐÃ ÁP DỤNG CLASS + SỬA LỖI 3/3)
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
    <?php if (empty($images)): ?>
        <p style="padding: 10px;">Sản phẩm này chưa có ảnh phụ.</p>
    <?php else: ?>
        <?php foreach ($images as $image): ?>
            <div class="image-thumbnail" style="border: 1px solid #ccc; padding: 5px; border-radius: 5px; text-align: center;">
                <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo htmlspecialchars($image['image_url']); ?>" 
                     alt="Ảnh phụ"
                     style="height: 150px; width: 150px; object-fit: cover; border-radius: 4px;">
                <br>
                
                <!-- 
                ============================================================
                 SỬA LỖI 3/3: Thêm &return_url=... vào link Xóa
                ============================================================
                -->
                <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=deleteImage&image_id=<?php echo $image['image_id']; ?>&product_id=<?php echo $product['product_id']; ?>&return_url=<?php echo urlencode($return_url); ?>"
                   onclick="return confirm('Bạn có chắc muốn xóa ảnh này?');" 
                   class="btn btn-danger" style="font-size: 0.8em; padding: 3px 8px; margin-top: 5px;">
                    Xóa ảnh này
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>