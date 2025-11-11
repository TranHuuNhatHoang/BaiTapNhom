<?php
// Tải các Model cần thiết
require_once ROOT_PATH . '/app/models/Review.php';
require_once ROOT_PATH . '/app/models/Order.php';
// Tải file functions (để dùng set_flash_message)
require_once ROOT_PATH . '/config/functions.php';

class ReviewController {

    /**
     * Action: Xử lý Gửi đánh giá
     * URL: (Form POST tới) index.php?controller=review&action=submit
     */
    public function submit() {
        global $conn;
        
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            // die("Bạn phải đăng nhập để đánh giá."); // CŨ
            set_flash_message("Bạn phải đăng nhập để đánh giá.", 'error'); // MỚI
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 2. Lấy dữ liệu
            $product_id = (int)$_POST['product_id'];
            $user_id = $_SESSION['user_id'];
            $rating = (int)$_POST['rating'];
            $comment = trim($_POST['comment']);

            // (Tạo link quay về trang sản phẩm để dùng khi báo lỗi)
            $redirect_url = BASE_URL . "index.php?controller=product&action=detail&id=" . $product_id;

            if ($rating < 1 || $rating > 5) {
                // die("Rating không hợp lệ."); // CŨ
                set_flash_message("Rating (số sao) không hợp lệ.", 'error'); // MỚI
                header("Location: " . $redirect_url);
                exit;
            }

            // 3. KIỂM TRA XEM USER ĐÃ MUA HÀNG (VÀ ĐÃ HOÀN THÀNH) CHƯA
            $orderModel = new Order($conn);
            $has_purchased = $orderModel->checkUserPurchase($user_id, $product_id);
            
            if (!$has_purchased) {
                // die("Bạn chỉ có thể đánh giá sản phẩm bạn đã mua..."); // CŨ
                set_flash_message("Bạn chỉ có thể đánh giá sản phẩm bạn đã mua và đơn hàng đã hoàn thành.", 'error'); // MỚI
                header("Location: " . $redirect_url);
                exit;
            }

            // 4. Tạo review
            $reviewModel = new Review($conn);
            if ($reviewModel->createReview($product_id, $user_id, $rating, $comment)) {
                // Thành công, quay lại trang sản phẩm
                set_flash_message("Gửi đánh giá thành công! Đánh giá của bạn đang chờ phê duyệt.", 'success'); // MỚI
                header("Location: " . $redirect_url);
                exit;
            } else {
                // die("Lỗi: Bạn chỉ được đánh giá sản phẩm này 1 lần."); // CŨ
                // (Lỗi này xảy ra do CSDL có UNIQUE(user_id, product_id))
                set_flash_message("Lỗi: Bạn chỉ được đánh giá sản phẩm này 1 lần.", 'error'); // MỚI
                header("Location: " . $redirect_url);
                exit;
            }
        }
    }
}
?>