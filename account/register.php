<?php

require "../config.php";
require "addAccount.php";  

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/toggle-eye.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/message.css">
</head>
<body>
    <div class="container" id="registerForm">
        <h2>Đăng Ký</h2>
        <form action="addAccount.php" method="POST">
            <div class="form-group">
                <label for="username">Tên người dùng</label>
                <input type="text" id="username" name="username" placeholder="Nhập tên người dùng" required value="<?php echo isset($_SESSION['post_data']['username']) ? $_SESSION['post_data']['username'] : ''; ?>">
        
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Nhập email của bạn" required value="<?php echo isset($_SESSION['post_data']['email']) ? $_SESSION['post_data']['email'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i id="eye-icon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="fullname">Họ và tên</label>
                <input type="text" id="fullname" name="fullname" placeholder="Nhập họ và tên" required value="<?php echo isset($_SESSION['post_data']['fullname']) ? $_SESSION['post_data']['fullname'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="number" id="phone" name="phone" placeholder="Nhập số điện thoại" required value="<?php echo isset($_SESSION['post_data']['phone']) ? $_SESSION['post_data']['phone'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <input type="text" id="address" name="address" placeholder="Nhập địa chỉ của bạn" required value="<?php echo isset($_SESSION['post_data']['address']) ? $_SESSION['post_data']['address'] : ''; ?>">
            </div>
            
            <button type="submit" class="btn btn-register">Đăng Ký</button>
        </form>
        <br>
        <?php
            if (isset($_SESSION['register_errors']) && !empty($_SESSION['register_errors'])):
        ?>
            <div class="message error">
                <ul style="list-style-type: none;">
                    <?php foreach ($_SESSION['register_errors'] as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php
    
            unset($_SESSION['register_errors']);
            endif;
        ?>
        <?php
        if (isset($_SESSION['register_success'])) {
            echo "<div class='message success'>" . $_SESSION['register_success'] . "</div>";
            unset($_SESSION['register_success']);
        }
        ?>

        <div class="switch">
            <span>Đã có tài khoản? <a href="login.php">Đăng Nhập</a></span>
        </div>
    </div>  

    <script src="../js/toggle-eye.js"></script>
</body>
</html>
