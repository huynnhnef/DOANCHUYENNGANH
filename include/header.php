<?php
require "config.php";
// Kiểm tra nếu người dùng đã đăng nhập
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

// Lấy thông tin người dùng
$stmt = $pdo->prepare("SELECT * FROM users a JOIN roles r on a.RoleID=r.id where a.Email=:Email");
$stmt->execute([":Email" => $user_email]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy số lượng sản phẩm trong giỏ hàng của người dùng
if (isset($_SESSION['user_email'])) {
    // Lấy user_id từ session
    $user_email = $_SESSION['user_email'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $user_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $user_id = $user['id'];

        // Sửa lại tên cột nếu cần
        $stmt = $pdo->prepare("SELECT COUNT(*) as cart_count FROM carts WHERE CustomerID = :user_id"); // Chỉnh sửa cột nếu cần
        $stmt->execute(['user_id' => $user_id]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        $cart_count = $cart['cart_count'];  // Số lượng sản phẩm trong giỏ hàng
    } else {
        $cart_count = 0;
    }
} else {
    $cart_count = 0;
}
?>

<header>
    <div class="header">
        <div class="header-1">
            <a href="index.php" class="logo">
                <img src="./img/logo_PhoneVibe.png" alt=""><span>PhoneVibe</span>
            </a>

            <form method="GET" action="product.php" class="search-form">
                <input type="search" name="search" id="search-box" placeholder="Tìm kiếm ..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <label for="search-box" class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></label>
            </form>
            

            <div class="icons icons-accept" style="padding: 0 16px;">
                <a href="../cart.php" class="fas fa-shopping-cart" aria-hidden="true">
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-count"><?= $cart_count; ?></span>
                    <?php else: ?>
                        <span class="cart-count">0</span>
                    <?php endif; ?>
                </a>

                <?php if (isset($_SESSION['user_email'])): ?>
                    <div class="user-icon">
                        <a href="#" id="user-btn" class="fas fa-user" aria-hidden="true"></a>
                        <?php
                            foreach ($result as $row){
                                ?>
                                <span style="font-size: 1.2rem;"><?php echo $row['Username']; ?></span>
                                <?php
                            }
                        ?>
                        <div class="dropdown">
                            <a href="account.php">Thông tin tài khoản</a>
                            <a href="#" id="logout-btn">Đăng xuất</a>
                        </div>
                    </div>
                    <div id="logoutModal" class="modal">
                        <div class="modal-content">
                            <span class="close" onclick="closeModal()">&times;</span>
                            <i class="fas fa-exclamation-circle"></i>
                            <p>Bạn có muốn đăng xuất không?</p>
                            <div class="btn-logout-cancel">
                                <button id="confirm-logout" onclick="logout()">Đăng xuất</button>
                                <button id="cancel-logout" onclick="closeModal()">Trở lại</button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="account/login.php" id="login-btn" class="fas fa-user" aria-hidden="true"></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>



<div class="menu">
    <ul>
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="">Danh mục</a>
        <ul class="dropdown">
            <?php 
                $stm = $pdo->prepare("SELECT id, CategoryName FROM categories");
                $stm->execute();
                $stmt = $stm->fetchAll(PDO::FETCH_ASSOC);
                foreach($stmt as $cat){
                    ?>
                        <li><a href="product.php?category_id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['CategoryName']); ?></a></li>
                    <?php
                }
            ?>
        </ul>
           
        </li>
        <li><a href="">Thương hiệu</a>
            <ul class="dropdown">
                <?php 
                    $stm = $pdo->prepare("SELECT id, BrandName FROM brands");
                    $stm->execute();
                    $stmt = $stm->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach($stmt as $brand){
                        ?>
                            <li><a href="product.php?category_id=4&brand_id=<?php echo $brand['id']; ?>"><?php echo htmlspecialchars($brand['BrandName']); ?></a></li>
                        <?php
                    }
                ?>
            </ul>
        </li>
        <li><a href="../product.php?category_id=4">Sản phẩm</a></li>
        <li><a href="../order.php">Đơn hàng</a></li>
        <li><a href="../warranty.php">Yêu cầu bảo hành</a></li>
    </ul>
</div>

<div class="menu-hamburger">
    <div class="hamburger" onclick="toggleHamburgerMenu()">
        <i class="fas fa-bars"></i>
    </div>
    <ul>
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="#">Danh mục</a>
            <ul class="dropdown">
                <?php 
                    $stm = $pdo->prepare("SELECT id, CategoryName FROM categories");
                    $stm->execute();
                    $stmt = $stm->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($stmt as $cat) {
                        echo "<li><a href='product.php?category_id={$cat['id']}'>" . htmlspecialchars($cat['CategoryName']) . "</a></li>";
                    }
                ?>
            </ul>
        </li>
        <li><a href="#">Thương hiệu</a>
            <ul class="dropdown">
                <?php 
                    $stm = $pdo->prepare("SELECT id, BrandName FROM brands");
                    $stm->execute();
                    $stmt = $stm->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($stmt as $brand) {
                        echo "<li><a href='product.php?category_id=4&brand_id={$brand['id']}'>" . htmlspecialchars($brand['BrandName']) . "</a></li>";
                    }
                ?>
            </ul>
        </li>
        <li><a href="../product.php?category_id=4">Sản phẩm</a></li>
        <li><a href="../order.php">Đơn hàng</a></li>
        <li><a href="../warranty.php">Yêu cầu bảo hành</a></li>
    </ul>
</div>

<script>
    function searchProduct() {
        let query = document.getElementById('search-box').value;
        if (query.length > 0) {
            fetch('search_suggestions.php?q=' + query)
                .then(response => response.json())
                .then(data => {
                    let suggestionList = document.getElementById('suggestions');
                    suggestionList.innerHTML = '';
                    data.forEach(product => {
                        let listItem = document.createElement('li');
                        listItem.textContent = product.ProductName;
                        listItem.onclick = function() {
                            window.location.href = 'product.php?id=' + product.id;
                        };
                        suggestionList.appendChild(listItem);
                    });
                });
        } else {
            document.getElementById('suggestions').innerHTML = '';
        }
    }

  // Hàm để mở và đóng menu hamburger
function toggleHamburgerMenu() {
    const menu = document.querySelector('.menu-hamburger ul');
    menu.classList.toggle('active');  // Thêm hoặc xóa class 'active' để hiển thị hoặc ẩn menu
}

// Thêm sự kiện click để hiển thị dropdown trong menu hamburger
document.querySelectorAll('.menu-hamburger > ul > li').forEach(item => {
    item.addEventListener('click', function(event) {
        // Ngừng lan truyền sự kiện click (để tránh việc đóng menu chính khi mở dropdown)
        event.stopPropagation(); 

        const dropdown = this.querySelector('.dropdown');
        if (dropdown) {
            // Toggle class 'active' để hiển thị hoặc ẩn dropdown
            this.classList.toggle('active'); 
        }
    });
});

// Đảm bảo rằng khi click vào bất kỳ đâu ngoài menu, sẽ đóng menu hamburger và dropdown
document.addEventListener('click', function(event) {
    const isClickInsideMenu = document.querySelector('.menu-hamburger').contains(event.target);
    if (!isClickInsideMenu) {
        document.querySelector('.menu-hamburger ul').classList.remove('active');
        document.querySelectorAll('.menu-hamburger li').forEach(item => {
            item.classList.remove('active');
        });
    }
});
</script>
