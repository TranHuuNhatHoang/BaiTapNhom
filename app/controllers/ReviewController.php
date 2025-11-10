<?php
// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/Review.php';
require_once ROOT_PATH . '/app/models/Order.php';

class ReviewController {

    /**
     * Action: Xử lý Gửi đánh giá
     * URL: (Form POST tới) index.php?controller=review&action=submit
     */
    public function submit() {
        global $conn;
        
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            die("Bạn phải đăng nhập để đánh giá.");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 2. Lấy dữ liệu
            $product_id = (int)$_POST['product_id'];
            $user_id = $_SESSION['user_id'];
            $rating = (int)$_POST['rating'];
            $comment = trim($_POST['comment']);

            if ($rating < 1 || $rating > 5) {
                die("Rating không hợp lệ.");
            }

            // 3. KIỂM TRA XEM USER ĐÃ MUA HÀNG CHƯA
            $orderModel = new Order($conn);
            $has_purchased = $orderModel->checkUserPurchase($user_id, $product_id);
            
            if (!$has_purchased) {
                die("Bạn chỉ có thể đánh giá sản phẩm bạn đã mua và đơn hàng đã hoàn thành.");
            }

            // 4. Tạo review
            $reviewModel = new Review($conn);
            if ($reviewModel->createReview($product_id, $user_id, $rating, $comment)) {
                // Thành công, quay lại trang sản phẩm
                header("Location: " . BASE_URL . "index.php?controller=product&action=detail&id=" . $product_id);
                exit;
            } else {
                die("Lỗi: Bạn chỉ được đánh giá sản phẩm này 1 lần.");
            }
        }
    }
}
?>