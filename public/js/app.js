// public/js/app.js
document.addEventListener('DOMContentLoaded', () => {
    
    // --- XỬ LÝ DARK MODE ---
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;

    // Kiểm tra theme đã lưu trong localStorage
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'dark') {
        body.classList.add('dark-mode');
        if (themeToggle) themeToggle.innerHTML = '☀️';
    }

    // Thêm sự kiện click cho nút
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            
            let theme = 'light';
            if (body.classList.contains('dark-mode')) {
                theme = 'dark';
                themeToggle.innerHTML = '☀️';
            } else {
                themeToggle.innerHTML = '🌙';
            }
            // Lưu lựa chọn
            localStorage.setItem('theme', theme);
        });
    }

});
// public/js/app.js

document.addEventListener('DOMContentLoaded', () => {
    
    // ... (Code dark mode ở trên) ...


    // --- XỬ LÝ FORM LOADING ---
    // Tìm TẤT CẢ các form (auth, tạo task, v.v.)
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => {
            
            // Bỏ qua form chat (sẽ xử lý bằng AJAX ở dưới)
            if (form.id === 'chat-form') {
                return;
            }

            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.classList.add('btn-loading');
                submitButton.disabled = true;
            }
        });
    });

});