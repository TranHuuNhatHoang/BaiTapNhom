<div class="static-page" style="max-width: 800px; margin: auto; padding: 20px;">
    <h1>Liên hệ & Hỗ trợ</h1>
    <p>Nếu bạn có bất kỳ thắc mắc nào, vui lòng điền vào form bên dưới. Chúng tôi cam kết phản hồi trong vòng 24 giờ.</p>
    
    <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=page&action=handleContact" style="max-width: 600px;">
        <div class="form-group">
            <label for="name">Họ và Tên:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Số điện thoại:</label>
            <input type="tel" id="phone" name="phone" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="message">Nội dung yêu cầu:</label>
            <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Gửi liên hệ</button>
    </form>
</div>