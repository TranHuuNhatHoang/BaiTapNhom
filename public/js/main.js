// Đợi cho toàn bộ trang tải xong
document.addEventListener("DOMContentLoaded", function() {
    
    // Tìm ô tìm kiếm và hộp kết quả (thêm vào navbar ở bước 3)
    const searchInput = document.getElementById('navbar-search-input');
    const searchResults = document.getElementById('navbar-search-results');
    
    if (searchInput && searchResults) {
        
        // 1. Khi người dùng GÕ
        searchInput.addEventListener('input', function() {
            const query = this.value;
            
            if (query.length < 2) { // Chỉ tìm khi gõ ít nhất 2 ký tự
                searchResults.innerHTML = ''; // Xóa kết quả
                searchResults.style.display = 'none';
                return;
            }
            
            // 2. Gọi AJAX đến Controller
            fetch(`index.php?controller=product&action=liveSearch&query=${query}`)
                .then(response => response.json())
                .then(data => {
                    // 3. Xử lý kết quả JSON
                    searchResults.innerHTML = ''; // Xóa kết quả cũ
                    
                    if (data.length > 0) {
                        searchResults.style.display = 'block'; // Hiển thị hộp
                        
                        data.forEach(product => {
                            // 4. Tạo mỗi dòng kết quả
                            const item = document.createElement('a');
                            item.href = `index.php?controller=product&action=detail&id=${product.product_id}`;
                            item.className = 'search-result-item';
                            item.innerHTML = `
                                <img src="public/uploads/${product.main_image}" alt="" height="40">
                                <span>${product.product_name}</span>
                                <strong style="margin-left: auto;">${Number(product.price).toLocaleString('vi-VN')} VND</strong>
                            `;
                            searchResults.appendChild(item);
                        });
                        
                    } else {
                        searchResults.style.display = 'none'; // Ẩn nếu không có
                    }
                })
                .catch(error => {
                    console.error('Lỗi Live Search:', error);
                    searchResults.style.display = 'none';
                });
        });
        
        // 5. Ẩn kết quả khi bấm ra ngoài
        document.addEventListener('click', function(e) {
            if (e.target !== searchInput) {
                searchResults.style.display = 'none';
            }
        });
    }
});

// (Code Live Search của GĐ 17 ở trên...)

/**
 * HÀM MỚI (Người 2 - GĐ19): Xử lý AJAX "Thêm vào giỏ"
 */
function initializeAjaxCartForms() {
    // 1. Tìm TẤT CẢ các form "Thêm vào giỏ"
    const cartForms = document.querySelectorAll('form[action*="controller=cart&action=add"]');
    
    // 2. Lặp qua từng form
    cartForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            // 3. Ngăn form gửi đi (ngăn tải lại trang)
            event.preventDefault(); 
            
            const formData = new FormData(this);
            const product_id = formData.get('product_id');
            const quantity = formData.get('quantity');
            
            // 4. Gửi yêu cầu AJAX
            fetch(`index.php?controller=cart&action=add&product_id=${product_id}&quantity=${quantity}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 5. CẬP NHẬT SỐ LƯỢNG TRÊN NAVBAR
                        // (Giả sử navbar có 1 id="cart-count")
                        const cartCountElement = document.getElementById('cart-count');
                        if (cartCountElement) {
                            cartCountElement.innerText = data.cart_count;
                        }
                        // (Bạn có thể thêm 1 thông báo "Đã thêm!" ở đây)
                    } else {
                        alert(data.message); // Báo lỗi
                    }
                })
                .catch(error => console.error('Lỗi AJAX Cart:', error));
        });
    });
}

// Gọi hàm này khi trang tải xong
// (Gộp nó vào DOMContentLoaded nếu bạn đã có)
document.addEventListener("DOMContentLoaded", function() {
    // (Code Live Search của bạn...)
    
    // Gọi hàm mới
    initializeAjaxCartForms();
});