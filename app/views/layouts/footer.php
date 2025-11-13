</main> <!-- (Thẻ <main> được MỞ trong header.php) -->
        
    </div> <!-- Hết class .container (của trang chính) -->

    <!-- 
    ============================================================
     CẬP NHẬT (Người 2 - GĐ21): Footer Mới
    ============================================================
    -->
    <footer class="site-footer">
        <div class="footer-container">
            
            <!-- Cột 1: Giới thiệu -->
            <div class="footer-column">
                <h4>Về Chúng Tôi</h4>
                <p>Cửa hàng Laptop chuyên cung cấp các sản phẩm laptop chính hãng từ các thương hiệu hàng đầu. Cam kết chất lượng, giá cả tốt nhất.</p>
            </div>
            
            <!-- Cột 2: Link nhanh -->
            <div class="footer-column">
                <h4>Chính Sách</h4>
                <ul>
                    <!-- (Link trỏ đến PageController của Người 1) -->
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=page&action=terms">Chính sách Bảo hành</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=page&action=terms">Chính sách Đổi trả</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=page&action=terms">Chính sách Bảo mật</a></li>
                </ul>
            </div>
            
            <!-- Cột 3: Liên hệ -->
            <div class="footer-column">
                <h4>Liên hệ</h4>
                <ul>
                    <!-- (Link trỏ đến PageController của Người 1) -->
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=page&action=about">Giới thiệu</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=page&action=contact">Liên hệ</a></li>
                    <li><p>Địa chỉ: 123 Đường ABC, TP. XYZ</p></li>
                    <li><p>Email: support@laptopstore.com</p></li>
                </ul>
            </div>

        </div> <!-- Hết .footer-container -->
        
        <!-- Dòng Copyright ở đáy -->
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> Bản quyền thuộc về BaiTapNhom (Designed by Nhom 3)
        </div>
    </footer>
    <!-- KẾT THÚC FOOTER MỚI -->

</body>
</html>