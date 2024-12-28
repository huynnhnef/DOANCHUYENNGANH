<?php
session_start();

require("config.php");

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    $stmt = $pdo->prepare("SELECT o.id, o.CustomerID, o.Status, o.ShippingAddress, o.OrderDate, u.Email, u.FullName
                           FROM orders o 
                           JOIN users u ON o.CustomerID = u.id
                           WHERE o.id = :order_id");
    $stmt->execute(['order_id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "Không tìm thấy đơn hàng.";
        exit;
    }


    $stmt = $pdo->prepare("SELECT p.ProductName, od.id, od.Quantity, od.Price, od.TotalAmount, p.Image
                           FROM orderdetails od 
                           JOIN products p ON od.ProductID = p.id
                           WHERE od.OrderID = :order_id");
    $stmt->execute(['order_id' => $order_id]);
    $order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    echo "Không có mã đơn hàng.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết đơn hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/android-icon-36x36.png" type="image/png">

    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding-top: 50px;
            margin-bottom: 24px;
        }

        .order-container {
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .order-container h3 {
            text-align: center;
            font-size: 24px;
            color: #333;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .order-summary {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .order-summary p {
            margin: 5px 0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            background-color: #fafafa;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .order-item:hover {
            background-color: #f1f1f1;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .order-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
            border-radius: 8px;
        }

        .order-item-info {
            flex: 1;
        }

        .order-item-info h3, h4 {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .order-item-info p {
            font-size: 16px;
            color: #7f8c8d;
            margin: 5px 0;
        }

        .order-item-info .price {
            font-size: 18px;
            color: #f39c12;
            font-weight: bold;
        }

        .back-btn {
            padding: 12px 25px;
            background-color: #fde800c3;
            color: black;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            width: 100%;
            margin-top: 30px;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #fbff7a;
        }

        .showbill{
            text-align: center;
            font-size: 1.6rem;
        }
        .showbill a:hover{
            color: red;
            text-decoration: underline;
        }

        
        @media (max-width: 768px) {
            .container {
                width: 90%;
            }

            .order-container {
                padding: 20px;
            }

            .order-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-item-info {
                margin-bottom: 10px;
            }

            .back-btn {
                padding: 10px 20px;
                font-size: 14px;
            }
        }

    </style>
</head>

    <?php
        include("include/header.php");
    ?>
<body>
    
    <div class="container">
        <div class="order-container">
            <h3>Chi tiết đơn hàng</h3>

            <div class="order-summary">
                <p><strong>Khách hàng: </strong><?php echo $order['FullName']; ?></p>
                <p><strong>Email: </strong><?php echo $order['Email']; ?></p>
                <p><strong>Địa chỉ giao hàng: </strong><?php echo $order['ShippingAddress']; ?></p>
                <p><strong>Ngày đặt: </strong><?php echo date('d/m/Y H:i:s', strtotime($order['OrderDate'])); ?></p>
                <p><strong>Trạng thái: </strong><span style="color: #e67e22;"><?php echo $order['Status']; ?></span></p>
            </div>

            <h4>Chi tiết sản phẩm:</h4>
            <?php if (count($order_details) > 0): ?>
                <?php foreach ($order_details as $item): ?>
                    <div class="order-item">
                        <img src="./img/<?php echo $item['Image']; ?>" alt="<?php echo $item['ProductName']; ?>">
                        <div class="order-item-info">
                            <h3><?php echo $item['ProductName']; ?></h3>
                            <p><strong>Số lượng: </strong><?php echo $item['Quantity']; ?></p>
                            <p><strong>Giá: </strong><span class="price"><?php echo number_format($item['Price'], 0, ',', '.'); ?> VND</span></p>
                            <p><strong>Tổng: </strong><span class="price"><?php echo number_format($item['TotalAmount'], 0, ',', '.'); ?> VND</span></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-order">Không có sản phẩm trong đơn hàng.</p>
            <?php endif; ?>
            <p class="showbill"><a href="bill.php?orderdetail_id=<?php echo $item['id']; ?>">Xem hóa đơn</a></p>
            <a href="order.php"><button class="back-btn">Quay lại</button></a>
        </div>
    </div>
    
    <?php
        include("include/footer.php");
    ?>
    <script src="js/logoutModal.js"></script>
</body>
</html>
