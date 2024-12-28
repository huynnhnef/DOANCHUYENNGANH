function logout() {
    window.location.href = '../account/logout.php'; 
}

function closeModal() {
    document.getElementById('logoutModal').style.display = 'none'; 
}


document.getElementById('logout-btn').addEventListener('click', function(event) {
    event.preventDefault(); // Ngăn chặn chuyển hướng
    document.getElementById('logoutModal').style.display = 'block'; 
});

// Đóng hộp thoại khi click ra ngoài
window.onclick = function(event) {
    const modal = document.getElementById('logoutModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};