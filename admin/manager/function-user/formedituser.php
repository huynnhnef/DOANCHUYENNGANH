<?php
session_start();
require "update_user.php";
// Kiểm tra nếu có tham số 'id' trong URL
if (isset($_GET['id'])) {
    $username = $_GET['id'];



    // Lấy thông tin người dùng từ cơ sở dữ liệu
    try {
        $sql = "SELECT * FROM users WHERE Username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Lỗi truy vấn: " . $e->getMessage();
        exit();
    }
} else {
    die("Thiếu tham số ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật</title>
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
        <h2>Cập nhật</h2>

        <form method="post" action="update_user.php?id=<?php echo $user['Username'] ?? ''; ?>">
            <div class="form-group">
                <label for="username">Tên người dùng:</label>
                <input type="text" id="username" name="username" value="<?php echo $user['Username'] ?? ''; ?>" disabled /><br><br>

                <input type="hidden" name="username" value="<?php echo $user['Username'] ?? ''; ?>" />
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $user['Email'] ?? ''; ?>" required /><br><br>
            </div>
            <div class="form-group">
                <label for="FullName">Họ tên:</label>
                <input type="text" id="FullName" name="FullName" value="<?php echo $user['FullName'] ?? ''; ?>" required /><br><br>
            </div>
            <div class="form-group">
                <label for="Phone">Số điện thoại:</label>
                <input type="number" id="Phone" name="Phone" value="<?php echo $user['Phone'] ?? ''; ?>" required /><br><br>
            </div>
            <div class="form-group">
                <label for="Address">Địa chỉ:</label>
                <input type="text" id="Address" name="Address" value="<?php echo $user['Address'] ?? ''; ?>" required /><br><br>
            </div>
            <div class="form-group">
                <label for="RoleID">Vai trò:</label>
                <select name="RoleID" required>
                    <option value="5" <?php echo (isset($user['RoleID']) && $user['RoleID'] == 5) ? 'selected' : ''; ?>>Admin</option>
                    <option value="6" <?php echo (isset($user['RoleID']) && $user['RoleID'] == 6) ? 'selected' : ''; ?>>Manager</option>
                    <option value="7" <?php echo (isset($user['RoleID']) && $user['RoleID'] == 7) ? 'selected' : ''; ?>>Employee</option>
                    <option value="8" <?php echo (isset($user['RoleID']) && $user['RoleID'] == 8) ? 'selected' : ''; ?>>Customer</option>
                </select><br><br>
            </div>
            <div class="form-group">
                <label for="user_status">Trạng thái:</label>
                <select name="user_status" required>
                    <option value="Đang hoạt động" <?php echo (isset($user['user_status']) && $user['user_status'] == "Đang hoạt động") ? 'selected' : ''; ?>>Đang hoạt động</option>
                    <option value="Ngưng hoạt động" <?php echo (isset($user['user_status']) && $user['user_status'] == "Ngưng hoạt động") ? 'selected' : ''; ?>>Ngưng hoạt động</option>
                </select><br><br>
            </div>
            <button type="submit" class="btn">Lưu thay đổi</button>
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
