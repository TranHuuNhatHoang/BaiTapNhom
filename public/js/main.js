/**
 * =================================================================
 * ĐỊNH NGHĨA CÁC HÀM KHỞI TẠO (Define Functions)
 * =================================================================
 */

/**
 * 1. Khởi tạo Live Search (Tìm kiếm tức thời)
 */
function initializeLiveSearch() {
    const searchInput = document.getElementById('navbar-search-input');
    const searchResults = document.getElementById('navbar-search-results');
    
    if (searchInput && searchResults) {
        
        searchInput.addEventListener('input', function() {
            const query = this.value;
            
            if (query.length < 2) { 
                searchResults.innerHTML = ''; 
                searchResults.style.display = 'none';
                return;
            }
            
            fetch(`index.php?controller=product&action=liveSearch&query=${query}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = ''; 
                    
                    if (data.length > 0) {
                        searchResults.style.display = 'block'; 
                        
                        data.forEach(product => {
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
                        searchResults.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Lỗi Live Search:', error);
                    searchResults.style.display = 'none';
                });
        });
        
        document.addEventListener('click', function(e) {
            if (e.target !== searchInput) {
                searchResults.style.display = 'none';
            }
        });
    }
}

/**
 * 2. Khởi tạo Form "Thêm vào giỏ" (AJAX Cart)
 */
function initializeAjaxCartForms() {
    const cartForms = document.querySelectorAll('form[action*="controller=cart&action=add"]');
    
    cartForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); 
            
            const formData = new FormData(this);
            const product_id = formData.get('product_id');
            const quantity = formData.get('quantity');
            
            fetch(`index.php?controller=cart&action=add&product_id=${product_id}&quantity=${quantity}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cartCountElement = document.getElementById('cart-count');
                        if (cartCountElement) {
                            cartCountElement.innerText = data.cart_count;
                        }
                        // (Bạn có thể thêm 1 thông báo "Đã thêm!" popup ở đây)
                    } else {
                        alert(data.message); 
                    }
                })
                .catch(error => console.error('Lỗi AJAX Cart:', error));
        });
    });
}

/**
 * 3. Khởi tạo Dropdown Địa chỉ (Tỉnh/Quận/Phường)
 */
function initializeAddressDropdowns() {
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    
    // Khi người dùng CHỌN TỈNH/THÀNH
    if (provinceSelect) {
        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            
            districtSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            
            if (!provinceId) {
                districtSelect.innerHTML = '<option value="">-- Vui lòng chọn Tỉnh/Thành trước --</option>';
                return;
            }

            fetch(`index.php?controller=address&action=getDistricts&province_id=${provinceId}`)
                .then(response => response.json())
                .then(data => {
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    data.forEach(district => {
                        districtSelect.innerHTML += `<option value="${district.district_id}">${district.district_name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Lỗi khi tải Quận/Huyện:', error);
                    districtSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
                });
        });
    }

    // Khi người dùng CHỌN QUẬN/HUYỆN
    if (districtSelect) {
        districtSelect.addEventListener('change', function() {
            const districtId = this.value;
            
            wardSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
            
            if (!districtId) {
                wardSelect.innerHTML = '<option value="">-- Vui lòng chọn Quận/Huyện trước --</option>';
                return;
            }

            fetch(`index.php?controller=address&action=getWards&district_id=${districtId}`)
                .then(response => response.json())
                .then(data => {
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    data.forEach(ward => {
                        wardSelect.innerHTML += `<option value="${ward.ward_code}">${ward.ward_name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Lỗi khi tải Phường/Xã:', error);
                    wardSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
                });
        });
    }
}
/**
 * =================================================================
 * HÀM MỚI (BƯỚC 3 - GĐ23): Khởi tạo Nút Kiểm tra Vận đơn
 * =================================================================
 */
function initializeTrackingButton() {
    const btn = document.getElementById('check-tracking-btn');
    const resultsDiv = document.getElementById('tracking-log-results');

    if (!btn || !resultsDiv) {
        return; // Chỉ chạy nếu nút và div tồn tại (tức là ở trang Chi tiết Đơn hàng)
    }

    btn.addEventListener('click', function() {
        const orderCode = this.dataset.orderCode; // Lấy mã từ data-order-code
        resultsDiv.innerHTML = '<p>Đang tải lịch sử vận đơn...</p>';
        btn.disabled = true; // Vô hiệu hóa nút

        // 1. Gọi AJAX đến TrackingController (đã tạo ở Bước 2)
        fetch(`index.php?controller=tracking&action=getOrderStatus&order_code=${orderCode}`)
            .then(response => response.json())
            .then(data => {
                btn.disabled = false; // Bật lại nút

                if (data.success) {
                    // 2. Thành công: Hiển thị lịch sử (log)
                    let html = `<p><strong>Trạng thái hiện tại:</strong> ${data.status}</p>`;
                    html += '<ul style="padding-left: 20px; font-size: 0.9em;">';
                    
                    // (Tài liệu GHN nói 'log' là một mảng)
                    if (data.log && data.log.length > 0) {
                        data.log.forEach(logEntry => {
                            // Định dạng lại ngày (vd: "2020-05-29T14:40:46.934Z")
                            const date = new Date(logEntry.updated_date);
                            const formattedDate = date.toLocaleString('vi-VN', { 
                                day: '2-digit', 
                                month: '2-digit', 
                                year: 'numeric', 
                                hour: '2-digit', 
                                minute: '2-digit' 
                            });
                            
                            // (Tùy vào API GHN, status có thể là text hoặc mã)
                            let statusText = logEntry.status;
                            // (Bạn có thể thêm 1 switch case ở đây để dịch status)
                            
                            html += `<li>${formattedDate}: <strong>${statusText}</strong></li>`;
                        });
                    }
                    html += '</ul>';
                    resultsDiv.innerHTML = html;
                    
                } else {
                    // 3. Thất bại (vd: 401, 403, 404)
                    resultsDiv.innerHTML = `<p style="color: red;">Lỗi: ${data.message}</p>`;
                }
            })
            .catch(error => {
                btn.disabled = false; // Bật lại nút
                console.error('Lỗi Tracking AJAX:', error);
                resultsDiv.innerHTML = '<p style="color: red;">Lỗi kết nối. Vui lòng thử lại.</p>';
            });
    });
}

/**
 * =================================================================
 * KHỞI CHẠY (Initialize)
 * (Chỉ dùng MỘT 'DOMContentLoaded' listener duy nhất)
 * =================================================================
 */
document.addEventListener("DOMContentLoaded", function() {
    
    // Khởi chạy tất cả các tính năng
    initializeLiveSearch();
    initializeAjaxCartForms();
    initializeAddressDropdowns();
    initializeTrackingButton();
});