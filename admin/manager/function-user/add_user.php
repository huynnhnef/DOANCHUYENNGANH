<?php
session_start();
require "../../../config.php";

// Thêm người dùng khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $roleID = $_POST['roleID'];
    $fullname=$_POST['FullName'];
    $phone=$_POST['Phone'];
    $Address=$_POST['Address'];
    $user_status="Đang hoạt động";

    // Mã hóa mật khẩu trước khi lưu vào cơ sở dữ liệu
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $errors = [];

    $stmtEmail = $pdo->prepare('SELECT * FROM users WHERE Email = :Email');
    $stmtEmail->execute([':Email' => $email]);


    $stmtUsername = $pdo->prepare('SELECT * FROM users WHERE Username = :Username');
    $stmtUsername->execute([':Username' => $username]);

    
    if ($stmtEmail->rowCount() > 0) {
        $errors[] = "Email đã tồn tại!";
    }


    if ($stmtUsername->rowCount() > 0) {
        $errors[] = "Tên người dùng đã tồn tại!";
    }
    if (!empty($errors)) {
        $_SESSION['adduser_errors'] = $errors;
        header("Location: add_user.php");
        exit();
    }
    else{
        try {
            $sql = "INSERT INTO users (Username, Email, Password, roleID, FullName, Phone, Address, user_status) VALUES (:username, :email, :password, :roleID, :FullName, :Phone, :Address, :user_status)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':roleID' => $roleID,
                ':FullName'=>$fullname,
                ':Phone'=>$phone,
                ':Address'=>$Address,
                ':user_status'=>$user_status
            ]);
            header("Location: ../user.php");
        } catch (PDOException $e) {
            echo "Lỗi khi thêm người dùng: " . $e->getMessage();
        }
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm tài khoản</title>
    <link rel="stylesheet" href="../../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/toggle-eye.css">
    <link rel="stylesheet" href="../../css/message.css">
    <style>
        a{
            text-decoration: none;
        }

        .btn-return{
            margin-top: 15px;
            font-size: 1rem;
        }
        .btn-return a:hover{
            color: red;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container" id="loginForm">
            <h2>Thêm người dùng mới</h2>

            <form method="post" action="add_user.php">
                <div class="form-group">
                    <label for="username">Tên người dùng</label>
                    <input type="text" id="username" name="username" placeholder="Nhập tên người dùng" required>
                </div>
                <div class="form-group">
                    <div class="password-container">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()" style="margin-top: 5px;">
                            <i id="eye-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Nhập email" required >
                </div>
                
                <div class="form-group">
                    <label for="FullName">Họ và tên</label>
                    <input type="text" id="FullName" name="FullName" placeholder="Nhập họ tên" required /><br><br>
                </div>
                <div class="form-group">
                    <label for="Phone">Số điện thoại</label>
                    <input type="number" id="Phone" name="Phone" placeholder="Nhập số điện thoại" required /><br><br>
                </div>
                <div class="form-group">
                    <label for="Address">Địa chỉ</label>
                    <input type="text" id="Address" name="Address" placeholder="Nhập địa chỉ" required /><br><br>
                </div>
                <div class="form-group">
                    <label for="roleID">Role</label>
                    <select name="roleID" required>
                        <option value="5">Admin</option>
                        <option value="6">Manager</option>
                        <option value="7">Employee</option>
                        <option value="8">Customer</option>
                    </select>
                    
                </div>

                <?php
                        if (isset($_SESSION['adduser_errors']) && !empty($_SESSION['adduser_errors'])):
                    ?>
                        <div class="message error">
                            <ul style="list-style-type: none; margin-bottom: 10px;">
                                <?php foreach ($_SESSION['adduser_errors'] as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php
                        unset($_SESSION['adduser_errors']);
                        endif;
                    ?>
                    
                <button type="submit" class="btn">Thêm người dùng</button>
                <p class="btn-return"><a href="../user.php">Quay lại</a></p>
            </form>



        <?php
        // Hiển thị thông báo lỗi nếu có
        if (isset($_SESSION['login_error'])) {
            echo "<div class='message'>".$_SESSION['login_error']."</div>";
            unset($_SESSION['login_error']);
        }

       
        ?>


    </div>

    <script src="../../js/toggle-eye.js"></script>
</body>
</html>