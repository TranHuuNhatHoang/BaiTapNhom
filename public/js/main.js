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