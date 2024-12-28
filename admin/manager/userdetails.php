<?php
// Kết nối cơ sở dữ liệu
require "../../config.php";

// Kiểm tra xem có tham số 'id' trong URL không
if (isset($_GET['id'])) {
    $username = $_GET['id'];

    try {
        // Truy vấn lấy thông tin người dùng từ bảng 'users' và 'roles'
        $sql = "
            SELECT u.Username, u.FullName, u.Email, u.Phone, u.Address, u.RoleID, r.RoleName, u.user_status
            FROM users u
            JOIN roles r ON u.RoleID = r.ID
            WHERE u.Username = :username
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo "Không tìm thấy người dùng.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Lỗi truy vấn: " . $e->getMessage();
        exit;
    }
} else {
    echo "Không có tham số 'id'.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/menuBrands.css">
    <link rel="stylesheet" href="../css/page.css">
    <title>Details User</title>

    <style>

    .container {
        display: flex;
        margin-top: -10px;
    }


    .right-content {
        flex: 1;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #34495e;
        font-size: 36px;
        margin-bottom: 40px;
    }

    form {
        max-width: 700px;
        margin: 0 auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th, table td {
        padding: 12px 20px;
        text-align: left;
        font-size: 16px;
        border: 1px solid #ddd;
    }

    table th {
        background-color: #34495e;
        color: white;
    }

    table td {
        background-color: #f9f9f9;
    }

    table td:hover {
        background-color: #ecf0f1;
    }

    a {
        text-decoration: none;
        color: #1abc9c;
    }

    a:hover {
        
    }

    .btn {
        display: inline-block;
        padding: 12px 24px;
        background-color: #fde800c3;
        color: black;
        font-size: 16px;
        text-align: center;
        border-radius: 5px;
        text-decoration: none;
        margin-top: 20px;
    }

    .btn:hover {
        background-color: #fbff7a;
    }

    </style>
</head>
<body>

    <?php include "../include/header.php"; ?>

    <div class="container">
        <div class="left-sidebar">
            <div class="dropdown">
                <span class="dropdown-title">Quản lý</span>
                <div class="dropdown-content">
                    <a href="user.php" style="background-color: #fbff7a;">Người dùng</a>
                    <a href="order.php">Đơn hàng</a>
                    <a href="product.php">Sản phẩm</a>
                    <a href="category.php">Danh mục</a>
                    <a href="brand.php">Thương hiệu</a>
                    <a href="warranty.php">Đơn bảo hành</a>
                </div>
            </div>
        </div>

        <div class="right-content">
            <h1>Thông tin người dùng</h1>

            <!-- Form hiển thị thông tin người dùng -->
            <form>
                <table>
                    <tr>
                        <th>Tên người dùng:</th>
                        <td><?php echo isset($user['Username']) ? $user['Username'] : 'Chưa có thông tin'; ?></td>
                    </tr>
                    <tr>
                        <th>Họ tên:</th>
                        <<td><?php echo !empty($user['FullName']) ? $user['FullName'] : 'Chưa có thông tin'; ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo isset($user['Email']) ? $user['Email'] : 'Chưa có thông tin'; ?></td>
                    </tr>
                    <tr>
                        <th>Số điện thoại:</th>
                        <td><?php echo isset($user['Phone']) ? $user['Phone'] : 'Chưa có thông tin'; ?></td>
                    </tr>
                    <tr>
                        <th>Địa chỉ:</th>
                        <td><?php echo isset($user['Address']) ? $user['Address'] : 'Chưa có thông tin'; ?></td>
                    </tr>
                    <tr>
                        <th>Vai trò:</th>
                        <td><?php echo isset($user['RoleName']) ? $user['RoleName'] : 'Chưa có thông tin'; ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái:</th>
                        <td><p style="color: red;"><?php echo isset($user['user_status']) ? $user['user_status'] : 'Chưa có thông tin'; ?></p></td>
                    </tr>
                </table>

                <div style="text-align: center; margin-top: 20px;">
                    <a href="user.php" class="btn">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
    <script src="../js/logoutModal.js"></script>
</body>
</html>
