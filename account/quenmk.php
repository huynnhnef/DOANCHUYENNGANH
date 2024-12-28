<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="../css/login.css">
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
        <h2>Quên mật khẩu</h2>
        <?php
        $email = isset($_GET['email']) ? $_GET['email'] : '';
        ?>

        <form action="fquenmk.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Nhập email của bạn" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group form-group_quenmk">
                <div class="form-group_left">
                    <label for="code">Mã xác thực</label>
                    <input type="number" id="code" name="code" placeholder="Nhập mã xác thực">
                </div>
                <div class="form-group_right">
                    <button type="submit" class="btn btn-sendcode" name="send_code">Gửi mã</button>
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
</body>
</html>
