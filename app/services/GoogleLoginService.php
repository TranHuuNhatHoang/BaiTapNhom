<?php
class GoogleLoginService {
    // ====================================================
    // CẤU HÌNH GOOGLE OAUTH (ĐÃ ĐIỀN ĐẦY ĐỦ)
    // ====================================================
    
    // 1. Client ID (Bạn gửi trước đó)
    private $client_id = '554607942940-o6ktko3v0nir3i41vstrhnup6iaj3aob.apps.googleusercontent.com';
    
    // 2. Client Secret (Bạn vừa gửi)
    private $client_secret = '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';
    
    // 3. Đường dẫn Callback (Phải khớp 100% với cái bạn điền trên Google Console)
    private $redirect_uri = 'http://localhost/BaiTapNhom/index.php?controller=auth&action=googleCallback';
    
    // ====================================================

    /**
     * 1. Tạo đường link để người dùng nhấn vào đăng nhập
     */
    public function getAuthUrl() {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];
        return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
    }

    /**
     * 2. Lấy thông tin User từ Google sau khi họ đăng nhập xong
     */
    public function getUserInfo($code) {
        // A. Đổi Code lấy Token
        $token_url = 'https://oauth2.googleapis.com/token';
        $post_data = [
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Tắt SSL cho localhost
        $response = curl_exec($ch);
        curl_close($ch);
        
        $token_data = json_decode($response, true);

        if (!isset($token_data['access_token'])) {
            // Ghi log lỗi nếu cần
            return null; 
        }

        // B. Dùng Token lấy Thông tin User
        $user_info_url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $token_data['access_token'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $user_info_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $user_response = curl_exec($ch);
        curl_close($ch);

        return json_decode($user_response, true); // Trả về mảng: id, email, name, picture...
    }
}
?>