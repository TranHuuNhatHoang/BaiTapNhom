<!-- (Biến $order được truyền từ Controller) -->
<div style="text-align: center; padding: 50px;">
    
    <h1 style="color: orange;">Kết quả Thanh toán</h1>
    <p>Đơn hàng <strong>#<?php echo $order['order_id']; ?></strong> hiện đang ở trạng thái: 
        <strong style="text-transform: uppercase;"><?php echo htmlspecialchars($order['order_status']); ?></strong>
    </p>
    
    <div style="margin: 30px 0; padding: 20px; background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; border-radius: 5px;">
        <p><strong>Nếu bạn đã thanh toán thành công:</strong></p>
        <p>Vui lòng đợi vài phút để hệ thống cập nhật, sau đó nhấn "Kiểm tra lại".</p>
        
        <p style="margin-top: 15px;"><strong>Nếu bạn đã HỦY thanh toán:</strong></p>
        <p>Bạn có thể thanh toán lại hoặc chọn phương thức khác trong Lịch sử đơn hàng.</p>
        <!-- 
        ============================================================
         NÚT HỖ TRỢ DEMO (Chỉ dùng cho Localhost/Test)
         Nút này giúp bạn giả lập việc ZaloPay gọi Callback thành công
        ============================================================
        -->
        <div style="margin-top: 20px; border-top: 1px dashed #999; padding-top: 10px;">
            <p><em>(Dành cho Developer/Demo)</em></p>
            <a href="<?php echo BASE_URL; ?>test_zalopay.php?order_id=<?php echo $order['order_id']; ?>" 
               target="_blank" 
               class="btn btn-success" style="font-size: 0.9em;">
               🚀 Giả lập Thanh toán Thành công
            </a>
        </div>
    </div>

    <!-- Nút tải lại trang để kiểm tra xem trạng thái đã đổi sang 'paid' chưa -->
    <a href="<?php echo BASE_URL; ?>index.php?controller=checkout&action=paymentResult&order_id=<?php echo $order['order_id']; ?>" 
       class="btn btn-primary">
       🔄 Kiểm tra lại trạng thái
    </a>

    <a href="<?php echo BASE_URL; ?>index.php?controller=account&action=history" 
       class="btn btn-secondary" style="margin-left: 10px;">
       Về Lịch sử Đơn hàng
    </a>
</div>