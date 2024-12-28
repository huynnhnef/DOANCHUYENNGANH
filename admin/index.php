<?php
session_start();
require "../config.php";

if (!isset($_SESSION['user_email']) || !isset($_SESSION['RoleID'])) {
    header("Location: account/login.php");
    exit();
}

$roleID = isset($_SESSION["RoleID"]) ? $_SESSION["RoleID"] :"";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/product.css">
    <link rel="stylesheet" href="css/menuBrands.css">
    <link rel="stylesheet" href="css/role.css">
    <title>Phonevibe</title>
</head>
<body>
    <header>
        <div class="header">
            <div class="header-1">
                <a href="index.php" class="logo"><img src="./img/logo_PhoneVibe.png" alt=""><span>PhoneVibe</span></a>

                <form action="" class="search-form">
                    <input type="search" name="" placeholder="Search here ..." id="search-box">
                    <label for="search-box" class="fas fa-search" aria-hidden="true"></label>
                </form>

                <div class="icons icons-accept" style="padding: 0 16px;">
                    <div id="search-btn" class="fas fa-search" aria-hidden="true" ></div>
                    <a href="#" class="fas fa-heart" aria-hidden="true"></a>
                    <a href="#" class="fas fa-shopping-cart" aria-hidden="true"></a>
                    
                    <?php if (isset($_SESSION['user_email'])): ?>
                    <div class="user-icon">
                        <a href="#" id="user-btn" class="fas fa-user" aria-hidden="true"></a>
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
                    <span></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="left-sidebar">
            <div class="dropdown">
                <span class="dropdown-title">Quản lý</span>
                <div class="dropdown-content">
                    <a href="manager/user.php" class="<?php echo ($roleID != 5) ? 'disabled' : ''; ?>">Người dùng</a>
                    <a href="manager/orders.php">Đơn hàng</a>
                    <a href="manager/product.php" class="<?php echo ($roleID == 7) ? 'disabled' : ''; ?>">Sản phẩm</a>
                    <a href="manager/categories.php" class="<?php echo ($roleID == 7) ? 'disabled' : ''; ?>">Danh mục</a>
                    <a href="manager/brands.php" class="<?php echo ($roleID == 7) ? 'disabled' : ''; ?>">Thương hiệu</a>
                    <a href="manager/warranty.php" class="<?php echo ($roleID == 7) ? 'disabled' : ''; ?>">Đơn bảo hành</a>
                </div>
            </div>
        </div>

        <div class="right-content">
            
        </div>
    </div>

    <footer></footer>

    <script src="../js/logoutModal.js"></script>
</body>
</html>
