<?php
session_start();
include('config.php');

if (isset($_GET['id'])) {
    $warrantyID = $_GET['id'];

    // Lấy thông tin chi tiết yêu cầu bảo hành và thông tin người dùng
    $stmt = $pdo->prepare("SELECT wa.*, u.FullName, u.Email, u.Phone, u.Address
                           FROM warranty_accepts wa
                           LEFT JOIN users u ON wa.CustomerID = u.id
                           WHERE wa.id = :warrantyID");
    $stmt->execute(['warrantyID' => $warrantyID]);
    $warrantyDetail = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$warrantyDetail) {
        header('Location: warranty.php');
        exit();
    }
} else {
    header('Location: warranty.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Yêu Cầu Bảo Hành</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/android-icon-36x36.png" type="image/png">
    <style>

        .container{
            margin-bottom: 24px;
        }
        .container h3{
            margin-top: 10px;
            font-size: 2rem;
            text-align: center;
            
        }
  
        .warranty-detail {
            width: 50%;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .warranty-detail h3 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 20px;
        }

 
        .warranty-detail p {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .warranty-detail p strong {
            color: #333;
            font-weight: 600;
        }

        .warranty-detail h4 {
            font-size: 20px;
            color: #007bff;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .warranty-detail p {
            font-size: 16px;
            margin-bottom: 10px;
        }


        .warranty-detail p {
            font-size: 16px;
            line-height: 1.5;
        }

        .warranty-detail p strong {
            font-weight: bold;
            color: #333;
        }
        .btn-return{
            width: 100%;
            font-size: 1.7rem;
            color: black;
            background-color: #fde800c3;
        }
        .btn-return:hover{
            background-color: #fbff7a;
        }
    
        @media (max-width: 768px) {
            .container {
                width: 90%;
            }

            .warranty-detail {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include("include/header.php"); ?>

    <div class="container">
        <h3>Chi tiết yêu cầu bảo hành</h3>
        <div class="warranty-detail">
            <p><strong>Mã hóa đơn:</strong> <?php echo $warrantyDetail['BillSeri']; ?></p>
            <p><strong>Ngày yêu cầu:</strong> <?php echo $warrantyDetail['WarrantyDate']; ?></p>
            <p><strong>Lý do bảo hành:</strong> <?php echo $warrantyDetail['Reason']; ?></p>
            <p><strong>Mô tả chi tiết:</strong> <?php echo $warrantyDetail['Description']; ?></p>
            <p><strong>Trạng thái:</strong>&nbsp;<span style="color: #e67e22;"><?php echo $warrantyDetail['Status']; ?></span></p>

            <h4>Thông Tin Khách Hàng</h4>
            <p><strong>Tên:</strong> <?php echo $warrantyDetail['FullName']; ?></p>
            <p><strong>Email:</strong> <?php echo $warrantyDetail['Email']; ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo $warrantyDetail['Phone']; ?></p>
            <p><strong>Địa chỉ:</strong> <?php echo $warrantyDetail['Address']; ?></p>
            <a href="warranty.php?tab=history"><button class="btn-return">Quay lại</button></a>
        </div>
        
    </div>

    <?php
        include("include/footer.php");
    ?>
    <script src="js/logoutModal.js"></script> 
</body>
</html>
