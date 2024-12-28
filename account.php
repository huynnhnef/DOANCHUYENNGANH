<?php
session_start();    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="img/android-icon-36x36.png" type="image/png">
    <title>Thông tin tài khoản - Phonevibe</title>
    <style>
        .container {
            width: 80%;
            margin: 32px auto;
            padding-top: 20px;
            
        }
        h2 {
            font-size: 2rem;
            text-align: center;
            color: #333;
        }
        .info-box {
            width: 50%;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .info-box p {
            font-size: 16px;
            margin: 10px 0;
        }
        .info-box label {
            font-weight: bold;
        }
        .edit-button {
            display: block;
            text-align: center;
            margin-top: 60px;
            background-color: #fde800c3;
            color: black;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
        .edit-button:hover {
            background-color: #fbff7a;
        }
    </style>
</head>
<body>
    <?php
        include("include/header.php");
        // Lấy thông tin người dùng từ bảng users
        $stmt = $pdo->prepare("SELECT * FROM users WHERE Email = :user_email");
        $stmt->execute(['user_email' => $user_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="container">
        <h2>Thông Tin Tài Khoản</h2>

        <div class="info-box">
            <p><label>Tên Đăng Nhập: </label>&nbsp;&nbsp;
            <?php echo $user['Username']; ?></p>

            <p><label>Họ và Tên: </label>&nbsp;&nbsp;
            <?php echo $user['FullName'];?></p>

            <p><label>Email: </label>&nbsp;&nbsp;
            <?php echo $user['Email'];?></p>

            <p><label>Số Điện Thoại: </label>&nbsp;&nbsp;
            <?php echo $user['Phone'];?></p>

            <p><label>Địa Chỉ: </label>&nbsp;&nbsp;
            <?php echo $user['Address'];?></p>
            <a href="edit_account.php" class="edit-button">Sửa Thông Tin</a>
        </div>
        
    </div>
    
    <?php
        include("include/footer.php");
    ?>
</body>
</html>
