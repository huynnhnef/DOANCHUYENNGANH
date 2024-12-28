<?php
session_start();
require("config.php");

if (isset($_GET['orderdetail_id'])) {
    $orderdetail_id = $_GET['orderdetail_id'];

   
    $stmt = $pdo->prepare("SELECT b.Seri, b.TotalAmount, b.BillDate, o.CustomerID, b.Status, od.OrderID 
                           FROM bills b
                           JOIN orderdetails od ON b.OrderdetailsID = od.id
                           JOIN orders o ON od.OrderID = o.id
                           WHERE b.OrderdetailsID = :OrderdetailsID");
    $stmt->execute(['OrderdetailsID' => $orderdetail_id]);
    $bill = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($bill) {
        $order_id = $bill['OrderID']; 
        
        $customer_id = $bill['CustomerID'];
        $stmt = $pdo->prepare("SELECT FullName, Email, Address FROM users WHERE id = :customer_id");
        $stmt->execute(['customer_id' => $customer_id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Hóa đơn thanh toán</title>
            <link href="/vnpay_php/assets/bootstrap.min.css" rel="stylesheet"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <link rel="stylesheet" href="css/style.css">
            <link rel="icon" href="img/android-icon-36x36.png" type="image/png">

            <style>
                .header {
                    text-align: center;
                }
                .header h3 {
                    color: #007bff;
                    font-size: 28px;
                    font-weight: 700;
                }
                .table-responsive {
                    width: 50%;
                    margin: 0 auto;
                    margin-top: 20px;
                    background-color: azure;
                }
                .form-group {
                    margin-bottom: 15px;
                    display: flex;
                    justify-content: space-between;
                    font-size: 16px;
                }
                .form-group label {
                    font-weight: bold;
                }
                .form-group .label-value {
                    font-weight: normal;
                    color: #555;
                }
                .form-group .value {
                    text-align: right;
                    color: #333;
                }
                .form-group .value strong {
                    color: #007bff;
                    font-weight: bold;
                }
                .showdetail{
                    text-align: center;
                    font-size: 2rem;
                    margin-top: 80px;
                }

                .showdetail a:hover{
                    color: red;
                    text-decoration: underline;
                }
            </style>
        </head>
        
        <?php include "include/header.php"; ?>
        <body>
            <div class="container">
                <div class="header clearfix">
                    <h3 class="text-muted">HÓA ĐƠN THANH TOÁN</h3>
                </div>
                <div class="table-responsive">
                    <div class="form-group">
                        <label>Mã Hóa Đơn:</label>
                        <label><?php echo $bill['Seri']; ?></label>
                    </div>
                    <div class="form-group">
                        <label>Khách Hàng:</label>
                        <label><?php echo $customer['FullName']; ?></label>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <label><?php echo $customer['Email']; ?></label>
                    </div>
                    <div class="form-group">
                        <label>Địa Chỉ Giao Hàng:</label>
                        <label><?php echo $customer['Address']; ?></label>
                    </div>
                    <div class="form-group">
                        <label>Tổng Tiền:</label>
                        <label><?php echo number_format($bill['TotalAmount']); ?> VND</label>
                    </div>
                    <div class="form-group">
                        <label>Ngày Lập Hóa Đơn:</label>
                        <label><?php echo $bill['BillDate']; ?></label>
                    </div>
                    <div class="form-group">
                        <label>Trạng Thái:</label>
                        <label style="color: red;"><?php echo $bill['Status']; ?></label>
                    </div>
                    <p class="showdetail"><a href="orderdetail.php?order_id=<?php echo $order_id; ?>">Xem chi tiết đơn hàng</a></p>
                    
                </div>
            </div>
        </body>
        <?php include "include/footer.php"; ?>
        <script src="js/logoutModal.js"></script> 
        </html>
        <?php
    } else {
        echo "Không tìm thấy hóa đơn này.";
    }
} else {
    echo "Không có mã hóa đơn.";
}
?>


