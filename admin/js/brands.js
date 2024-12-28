
    document.querySelectorAll('.brand-link').forEach(link => {
        link.addEventListener('click', function() {
            // Gỡ bỏ lớp active từ tất cả các liên kết
            document.querySelectorAll('.brand-link').forEach(l => l.classList.remove('active'));
            // Thêm lớp active cho liên kết đang được nhấp
            this.classList.add('active');
        });
    });

