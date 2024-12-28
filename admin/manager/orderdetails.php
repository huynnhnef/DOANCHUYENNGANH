<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/menuBrands.css">
    <link rel="stylesheet" href="../css/page.css">
    <link rel="stylesheet" href="../css/role.css">
    <title>Order Details</title>
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
                <a href="order.php" style="background-color: #fbff7a;">Đơn hàng</a>
                <a href="product.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Sản phẩm</a>
                <a href="category.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Danh mục</a>
                <a href="brand.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Thương hiệu</a>
                <a href="warranty.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Đơn bảo hành</a>
            </div>
        </div>
    </div>
    <div class="right-content">

    <?php
    // Kiểm tra nếu có 'orderID' trong URL
    if (isset($_GET['orderID'])) {
        $orderID = $_GET['orderID'];

        try {
            // Truy vấn thông tin đơn hàng từ bảng orders
            $orderSql = "
                SELECT o.id as OrderID, o.OrderDate, o.Status, u.FullName as Customer, o.ShippingAddress
                FROM orders o
                JOIN users u ON o.CustomerId = u.id
                WHERE o.id = :orderID
            ";
            $orderStmt = $pdo->prepare($orderSql);
            $orderStmt->execute([':orderID' => $orderID]);
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                echo "Đơn hàng không tồn tại.";
                exit;
            }

            // Truy vấn chi tiết sản phẩm từ bảng orderdetails
            $detailsSql = "
                SELECT od.ProductID, p.Image, p.ProductName, od.Quantity, od.Price, od.TotalAmount
                FROM orderdetails od
                JOIN products p ON od.ProductID = p.id
                WHERE od.OrderID = :orderID
            ";
            $detailsStmt = $pdo->prepare($detailsSql);
            $detailsStmt->execute([':orderID' => $orderID]);
            $orderDetails = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Lỗi truy vấn: " . $e->getMessage();
        }
    } else {
        echo "Không có thông tin đơn hàng.";
        exit;
    }
    ?>

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
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 18px;
        }

        h3{
            text-align: center;
            font-size: 1.8rem;
            margin-top: 50px;
        }
        th {
            background-color: #34495e;
            color: white;
        }

        .btn-back {
            background-color: #fbff7a;
            margin-top: 20px;
            padding: 15px 90px;
            color: black;
            border: none;
            font-size: 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-back:hover {
            background-color: #f7f6c1;
        }
        .back-btn{
            text-align: center;
            
        }
    </style>

    <h1>Chi tiết đơn hàng</h1>

    <div style="width: 50%; margin: 0 auto;">
        <h3>Đơn hàng</h3>
        <table>

            <tr>
                <th>Khách hàng</th>
                <td><?php echo $order['Customer']; ?></td>
            </tr>
            <tr>
                <th>Ngày đặt hàng</th>
                <td><?php echo $order['OrderDate']; ?></td>
            </tr>
            <tr>
                <th>Trạng thái</th>
                <td style="color: red;"><?php echo $order['Status']; ?></td>
            </tr>
            <tr>
                <th>Địa chỉ</th>
                <td><?php echo $order['ShippingAddress']; ?></td>
            </tr>
        </table>
    </div>

    <div>
        <h3>Sản phẩm</h3>
        <table>
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Tổng tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total=0;
                if (count($orderDetails) > 0): ?>
                    <?php foreach ($orderDetails as $detail): ?>
                        <tr>
                            <td style="width: 10%;"><img style="width: 100%;" src="<?php echo $detail['Image']; ?>" alt=""></td>
                            <td><?php echo $detail['ProductName']; ?></td>
                            <td><?php echo $detail['Quantity']; ?></td>
                            <td><?php echo number_format($detail['Price'], 2); ?> VND</td>
                            <td><?php echo number_format($detail['TotalAmount'], 2); ?> VND</td>
                        </tr>
                    <?php 
                    $total+=$detail['TotalAmount'];
                endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Không có sản phẩm trong đơn hàng này.</td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td colspan="4" style="text-align: right;">Thành tiền:</td>
                    <td><?php echo number_format($total, 2); ?> VND</td>
                </tr>
            </tbody>
        </table>

        
    </div>

    <div class="back-btn">
        <a href="order.php"><button class="btn-back">Quay lại</button></a>
    </div>
    
 
            
    </div>
    </div>

    <footer>
        <!-- Footer content -->
    </footer>

</body>
    <script src="../js/logoutModal.js"></script>
</html>

