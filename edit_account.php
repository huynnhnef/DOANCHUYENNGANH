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
    <title>Sửa Thông Tin Tài Khoản - Phonevibe</title>
    <style>
        .container {
            width: 80%;
            margin: 0 auto;
            padding-top: 20px;
            font-size: 1.6rem;
            margin-bottom: 24px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-box {
            border: 1px solid #ccc;
            padding: 20px;
            width: 40%;
            margin: 0 auto;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-box label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .form-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-box button {
            background-color: #fde800c3;
            color: black;
            width: 100%;
            font-size: 1.6rem;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .form-box button:hover {
            background-color: #fbff7a;
        }
    </style>
</head>
<body>
    <?php
    require_once("config.php");
    // Lấy thông tin người dùng
    $user_email = $_SESSION['user_email'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE Email = :user_email");
    $stmt->execute(['user_email' => $user_email]);
    $users = $stmt->fetch(PDO::FETCH_ASSOC);

    // Xử lý cập nhật thông tin
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = isset($_POST['Username']) ? $_POST['Username'] : '';
        $full_name = isset($_POST['FullName']) ? $_POST['FullName'] : '';
        $phone = isset($_POST['Phone']) ? $_POST['Phone'] : '';
        $address = isset($_POST['Address']) ? $_POST['Address'] : '';

        // Cập nhật thông tin trong cơ sở dữ liệu
        $stmt = $pdo->prepare("UPDATE users SET Username=:username ,FullName = :full_name, Phone = :phone, Address = :address WHERE Email = :user_email");
        $stmt->execute([
            'username'=>$username,
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'user_email' => $user_email
        ]);
        header("Location: account.php");
        exit(); 
    }
    
    include("include/header.php");
    ?>

    <div class="container">
        <h2>Sửa Thông Tin Tài Khoản</h2>
        <div class="form-box">
            <form method="POST" action="edit_account.php">
                <label for="Email">Email</label>
                <input style="background-color: azure;" type="email" id="Email" name="Email" value="<?php echo isset($users['Email']) ? htmlspecialchars($users['Email']) : ''; ?>" readonly>
                

                <label for="Username">Tên người dùng</label>
                <input type="text" id="Username" name="Username" value="<?php echo isset($users['Username']) ? htmlspecialchars($users['Username']) : ''; ?>" required>
                
                
                <label for="FullName">Họ và Tên</label>
                <input type="text" id="FullName" name="FullName" value="<?php echo isset($users['FullName']) ? htmlspecialchars($users['FullName']) : ''; ?>" required>
                
                <label for="Phone">Số Điện Thoại</label>
                <input type="text" id="Phone" name="Phone" value="<?php echo isset($users['Phone']) ? htmlspecialchars($users['Phone']) : ''; ?>" required>
                
                <label for="Address">Địa Chỉ</label>
                <input type="text" id="Address" name="Address" value="<?php echo isset($users['Address']) ? htmlspecialchars($users['Address']) : ''; ?>" required>
                
                <button type="submit">Cập nhật</button>
            </form>
        </div>
    </div>

    <?php
        include("include/footer.php");
    ?>
    <script src="js/logoutModal.js"></script> 
</body>
</html>
