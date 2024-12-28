<?php
session_start();

require("config.php");

// Kiểm tra xem người dùng có đăng nhập hay không
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
if (!$user_email) {
    header("Location: account/login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, FullName FROM users WHERE email = :user_email");
$stmt->execute(['user_email' => $user_email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Không tìm thấy người dùng!";
    exit;
}

$customer_id = $user['id']; 

// Truy vấn tất cả đơn hàng của người dùng
$stmt = $pdo->prepare("SELECT o.id, o.Status, o.ShippingAddress, o.OrderDate
                       FROM orders o
                       WHERE o.CustomerID = :customer_id
                       ORDER BY o.OrderDate DESC");
$stmt->execute(['customer_id' => $customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đơn hàng - Phonevibe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="img/android-icon-36x36.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    
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

        .order-item-info {
            flex: 1;
        }

        .order-item-info h3 {
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

        .order-item-info p strong {
            color: #333;
        }

        .order-item-info .status {
            font-weight: bold;
            color: #e67e22;
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
            <h3>Đơn hàng của bạn</h3>

            <?php if (empty($orders)): ?>
                <p style="text-align: center; font-size: 1.4rem;">Chưa có đơn hàng nào.</p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <a href="orderdetail.php?order_id=<?php echo $order['id']; ?>">
                        <div class="order-item">
                            <div class="order-item-info">
                                <p><strong>Ngày đặt: </strong><?php echo date('d/m/Y H:i:s', strtotime($order['OrderDate'])); ?></p>
                                <p><strong>Địa chỉ giao hàng: </strong><?php echo $order['ShippingAddress']; ?></p>
                                <p><strong>Trạng thái: </strong><span class="status"><?php echo $order['Status']; ?></span></p>
                            </div>
                            <i class="fas fa-arrow-right" style="color: #3498db;"></i>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>

            <a href="index.php"><button class="back-btn">Quay lại</button></a>
        </div>
    </div>
    <?php
        include("include/footer.php");
    ?>
    <script src="js/logoutModal.js"></script> 
</body>
</html>

