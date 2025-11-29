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
                        alert("Đã thêm vào giỏ hàng thành công!");
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
 * HÀM MỚI (GĐ23): Khởi tạo Nút Kiểm tra Vận đơn
 * =================================================================
 */
function initializeTrackingButton() {
    const btn = document.getElementById('check-tracking-btn');
    const resultsDiv = document.getElementById('tracking-log-results');

    if (!btn || !resultsDiv) {
        return; 
    }

    btn.addEventListener('click', function() {
        const orderCode = this.dataset.orderCode; 
        resultsDiv.innerHTML = '<p>Đang tải lịch sử vận đơn...</p>';
        btn.disabled = true; 

        fetch(`index.php?controller=tracking&action=getOrderStatus&order_code=${orderCode}`)
            .then(response => response.json())
            .then(data => {
                btn.disabled = false; 

                if (data.success) {
                    // --- BẢNG DỊCH TRẠNG THÁI SANG TIẾNG VIỆT ---
                    const statusMap = {
                        'ready_to_pick': 'Chờ lấy hàng',
                        'picking': 'Shipper đang đến lấy',
                        'cancel': 'Hủy đơn hàng',
                        'picked': 'Đã lấy hàng',
                        'storing': 'Đang lưu kho',
                        'transporting': 'Đang luân chuyển',
                        'sorting': 'Đang phân loại',
                        'delivering': 'Đang giao hàng',
                        'money_collect_picking': 'Đang thu tiền người gửi',
                        'money_collect_delivering': 'Đang thu tiền người nhận',
                        'delivered': 'Giao hàng thành công',
                        'delivery_fail': 'Giao hàng thất bại',
                        'waiting_to_return': 'Chờ trả hàng',
                        'return': 'Đang trả hàng',
                        'returned': 'Đã trả hàng',
                        'exception': 'Ngoại lệ (Sự cố)',
                        'damage': 'Hàng bị hư hỏng',
                        'lost': 'Hàng bị mất'
                    };

                    // 1. Dịch trạng thái HIỆN TẠI (SỬA LỖI Ở ĐÂY)
                    let currentStatusText = statusMap[data.status] || data.status;
                    let html = `<p><strong>Trạng thái hiện tại:</strong> <span style="color:blue; font-weight:bold;">${currentStatusText}</span></p>`;
                    
                    html += '<ul style="padding-left: 20px; font-size: 0.9em;">';
                    
                    // 2. Hiển thị lịch sử
                    if (data.log && data.log.length > 0) {
                        data.log.forEach(logEntry => {
                            const date = new Date(logEntry.updated_date);
                            const formattedDate = date.toLocaleString('vi-VN', { 
                                day: '2-digit', month: '2-digit', year: 'numeric', 
                                hour: '2-digit', minute: '2-digit' 
                            });
                            
                            // Dịch trạng thái trong log
                            let logStatusText = statusMap[logEntry.status] || logEntry.status;
                            
                            let color = '#333';
                            if (logEntry.status === 'delivered') color = 'green';
                            if (logEntry.status === 'cancel' || logEntry.status === 'delivery_fail') color = 'red';
                            
                            html += `<li>${formattedDate}: <strong style="color: ${color}">${logStatusText}</strong></li>`;
                        });
                    }
                    html += '</ul>';
                    resultsDiv.innerHTML = html;
                    
                } else {
                    resultsDiv.innerHTML = `<p style="color: red;">Lỗi: ${data.message}</p>`;
                }
            })
            .catch(error => {
                btn.disabled = false; 
                console.error('Lỗi Tracking AJAX:', error);
                resultsDiv.innerHTML = '<p style="color: red;">Lỗi kết nối. Vui lòng thử lại.</p>';
            });
    });
}

/**
 * =================================================================
 * KHỞI CHẠY (Initialize)
 * =================================================================
 */
document.addEventListener("DOMContentLoaded", function() {
    initializeLiveSearch();
    initializeAjaxCartForms();
    initializeAddressDropdowns();
    initializeTrackingButton();
});