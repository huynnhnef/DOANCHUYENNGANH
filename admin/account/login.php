<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/toggle-eye.css">
    <link rel="stylesheet" href="../css/message.css">
</head>
<body>
    <div class="container" id="loginForm">
        <h2>Đăng Nhập Admin</h2>
        <form action="flogin.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Nhập email của bạn" required>
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
            <button type="submit" class="btn btn-login">Đăng Nhập</button>
        </form>

        <?php
        if (isset($_SESSION['login_error'])) {
            echo "<div class='message'>".$_SESSION['login_error']."</div>";
            unset($_SESSION['login_error']);
        }
            
        if (isset($_SESSION['check_status'])) {
            echo "<div class='message'>".$_SESSION['check_status']."</div>";
            unset($_SESSION['check_status']);
        }
        ?>

    </div>

    <script src="../js/toggle-eye.js"></script>
</body>
</html>
