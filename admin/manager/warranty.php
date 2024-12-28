<?php

// Kết nối với cơ sở dữ liệu
include "../../config.php";

try {
    // Truy vấn tất cả thông tin bảo hành từ bảng warranty_accepts
    $sql = "
        SELECT wa.id, wa.BillSeri, wa.WarrantyDate, wa.Description, wa.Reason, wa.Status, wa.BillID, b.OrderdetailsID, od.OrderID, u.FullName 
        FROM warranty_accepts wa
        JOIN bills b ON wa.BillID = b.id
        JOIN orderdetails od ON b.OrderdetailsID = od.id
        JOIN orders o ON od.OrderID = o.id
        JOIN users u ON u.id = wa.CustomerID
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $warrantyAccepts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi khi truy vấn dữ liệu bảo hành: " . $e->getMessage());
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
    <title>Quản lý bảo hành</title>
    <style>
        .right-content h1{
            text-align: center;
            font-size: 35px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }

        th, td {
            padding: 20px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 18px;
        }

        th {
            background-color: #34495e;
            color: white;
        }

        a {
            color: #1abc9c;
        }

        a:hover {
            text-decoration: none;
            color: red;
        }

        .search-bar {
            margin-top: 40px;
        }

        .search-bar input[type="text"] {
            padding: 5px;
            width: 200px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-bar button {
            padding: 9px 15px;
            background: rgb(247, 246, 193);
            color: black;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #fbff7a;
        }
        form select {
            padding: 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
        }
        form select option{
            cursor: pointer;
        }
        form select:focus {
            border-color: #fbff7a;
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

    <h1>Danh sách bảo hành</h1>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Seri hóa đơn</th>
                <th>Ngày bảo hành</th>
                <th>Lý do</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Khách hàng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($warrantyAccepts as $warranty): ?>
                <tr>
                    <td><?php echo $warranty['BillSeri']; ?></td>
                    <td><?php echo $warranty['WarrantyDate'];?></td>
                    <td><?php echo $warranty['Reason'];?></td>
                    <td><?php echo $warranty['Description']; ?></td>
                    <td>
                        <!-- Form để cập nhật trạng thái -->
                        <form method="POST" action="function-warranty/update_warranty_status.php">
                            <select name="status" onchange="this.form.submit()">
                                <option value="Đang xử lý" <?php echo ($warranty['Status'] == 'Đang xử lý') ? 'selected' : ''; ?>>Đang xử lý</option>
                                <option value="Đã xử lý" <?php echo ($warranty['Status'] == 'Đã xử lý') ? 'selected' : ''; ?>>Đã xử lý</option>
                            </select>
                            <input type="hidden" name="warrantyID" value="<?php echo $warranty['id']; ?>" />
                        </form>
                    </td>
                    <td><?php echo $warranty['FullName'];?></td>
                    <td>
                        <a href="function-warranty/delete_warranty.php?warrantyID=<?php echo $warranty['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bảo hành này không?')"><i class="fa-solid fa-trash"></i></a> &nbsp;&nbsp;&nbsp;
                        <a href="warrantydetails.php?warrantyID=<?php echo $warranty['id']; ?>">Chi tiết</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Phân trang (nếu cần thiết) -->
    <div class="page">
        <div class="page-links">
            <!-- Logic phân trang có thể được áp dụng ở đây -->
        </div>
    </div>

    </div>
</div>
            
    <script src="../js/logoutModal.js"></script>
    
</body>
</html>
