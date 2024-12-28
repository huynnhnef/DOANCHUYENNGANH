<?php

// Kết nối cơ sở dữ liệu
include "../../config.php";

if (isset($_GET['warrantyID'])) {
    $warrantyID = $_GET['warrantyID'];

    try {
        // Truy vấn chi tiết bảo hành và thông tin khách hàng
        $sql = "
            SELECT wa.id, wa.BillSeri, wa.WarrantyDate, wa.Description, wa.Reason, wa.Status, u.FullName, u.Email, u.Phone
            FROM warranty_accepts wa
            JOIN users u ON u.id = wa.CustomerID
            WHERE wa.id = :warrantyID
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':warrantyID' => $warrantyID]);
        $warrantyDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$warrantyDetails) {
            die("Không tìm thấy bảo hành.");
        }
    } catch (PDOException $e) {
        die("Lỗi khi truy vấn chi tiết bảo hành: " . $e->getMessage());
    }
} else {
    die("Dữ liệu không hợp lệ.");
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/menuBrands.css">
    <link rel="stylesheet" href="../css/page.css">
    <link rel="stylesheet" href="../css/role.css">
    <title>Chi tiết bảo hành</title>
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
    <?php
        include "../include/header.php";
        $roleID = isset($_SESSION["RoleID"]) ? $_SESSION["RoleID"] : "";
    ?>
<div class="container">
    <div class="left-sidebar">
        <div class="dropdown">
            <span class="dropdown-title">Quản lý</span>
            <div class="dropdown-content">
                <a href="user.php" class="<?php echo ($roleID != 5) ? 'disabled' : '' ?>">Người dùng</a>
                <a href="order.php" >Đơn hàng</a>
                <a href="product.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Sản phẩm</a>
                <a href="category.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Danh mục</a>
                <a href="brand.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Thương hiệu</a>
                <a href="warranty.php" style="background-color: #fbff7a;" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Đơn bảo hành</a>
            </div>
        </div>
    </div>
    <div class="right-content">

    <h1>Chi tiết đơn bảo hành</h1>

    <div class="details">
        <table>
            <tr>
                <th>Seri hóa đơn</th>
                <td><?php echo $warrantyDetails['BillSeri']; ?></td>
            </tr>
            <tr>
                <th>Ngày bảo hành</th>
                <td><?php echo $warrantyDetails['WarrantyDate']; ?></td>
            </tr>
            <tr>
                <th>Lý do</th>
                <td><?php echo $warrantyDetails['Reason']; ?></td>
            </tr>
            <tr>
                <th>Mô tả</th>
                <td><?php echo $warrantyDetails['Description']; ?></td>
            </tr>
            <tr>
                <th>Trạng thái</th>
                <td style="color: red;"><?php echo $warrantyDetails['Status']; ?></td>
            </tr>
            <tr>
                <th>Tên khách hàng</th>
                <td><?php echo $warrantyDetails['FullName']; ?></td>
            </tr>
            <tr>
                <th>Email khách hàng</th>
                <td><?php echo $warrantyDetails['Email']; ?></td>
            </tr>
            <tr>
                <th>Số điện thoại</th>
                <td><?php echo $warrantyDetails['Phone']; ?></td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 20px;">
            <a href="warranty.php" class="btn">Quay lại</a>
        </div>
    </div>

    </div>
</div>
    <script src="../js/logoutModal.js"></script>
</body>
</html>
