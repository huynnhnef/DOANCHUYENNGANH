<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Cập nhật mật khẩu</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/toggle-eye.css">
    <style>
        .message {
            color: red;
            font-size: 0.8rem;
           
        }
        .success {
            color: green;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container" id="loginForm">
        <h2>Cập nhật mật khẩu</h2>
        <?php
        $email = isset($_GET['email']) ? $_GET['email'] : '';
        ?>

        <form action="fcapnhatmk.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Nhập email của bạn" value="<?php echo htmlspecialchars($email); ?>" readonly style="background-color: aliceblue;">
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu mới</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i id="eye-icon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="form-group">
            <label for="confirm_password">Nhập lại mật khẩu</label>
                <div class="password-container">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập mật khẩu" required>
                    <button type="button" class="toggle-password" onclick="togglePasswordConfirm()">
                        <i id="eye-icon-confirm" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-submit" name="confirm_code">Xác nhận</button>
        </form>
        <br>
        <?php
            session_start();
            if (isset($_SESSION['message'])) {
                $message_type = $_SESSION['message_type'] == "success" ? "success" : "message";
                echo "<div class='$message_type'>".$_SESSION['message']."</div>";
                unset($_SESSION['message']); 
            }
        ?>
        <div class="switch">
            <span><a href="login.php" style="font-size: 1rem;">Trở lại</a></span>
        </div>
    </div>
    <script src="../js/toggle-eye.js"></script>
    <script>
        function togglePasswordConfirm(){
            const passwordConfirmInput=document.getElementById('confirm_password');
            const eyeIconConfirm=document.getElementById('eye-icon-confirm');
            if(passwordConfirmInput.type==='password'){
                passwordConfirmInput.type='text';
                eyeIconConfirm.classList.remove('fa-eye');
                eyeIconConfirm.classList.add('fa-eye-slash');
            }
            else{
                passwordConfirmInput.type='password';
                eyeIconConfirm.classList.remove('fa-eye-slash');
                eyeIconConfirm.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
