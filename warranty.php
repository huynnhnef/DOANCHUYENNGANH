<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_email'])) {
    header('Location: account/login.php');
    exit();
}

include('config.php');
$error_message = ''; 
$success_message = '';

// Lấy CustomerID từ bảng users qua user_email
$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : "";
$stmt = $pdo->prepare("SELECT id FROM users WHERE Email = :email");
$stmt->execute(['email' => $email]);
$users = $stmt->fetch(PDO::FETCH_ASSOC);
$customerID = $users['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitRequest'])) {
    $billSeri = $_POST['billSeri'];
    $reason = $_POST['reason'];
    $description = $_POST['description'];

    // Kiểm tra mã hóa đơn có tồn tại trong bảng bills
    $stmt = $pdo->prepare("SELECT id FROM bills WHERE Seri = :billSeri");
    $stmt->execute(['billSeri' => $billSeri]);
    $bill = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($bill) {
        $billID = $bill['id'];

        $stmt = $pdo->prepare("INSERT INTO warranty_accepts (BillSeri, WarrantyDate, Description, Reason, Status, BillID, CustomerID) 
                               VALUES (:billSeri, NOW(), :description, :reason, 'Đang xử lý', :billID, :customerID)");
        $stmt->execute([
            'billSeri' => $billSeri,
            'description' => $description,
            'reason' => $reason,
            'billID' => $billID,
            'customerID' => $customerID
        ]);

        $success_message = "Yêu cầu bảo hành đã được gửi thành công!";
        header('Location: warranty.php?tab=history');
        exit();
    } else {
        $error_message = "Mã hóa đơn không tồn tại!";
    }
}

// Lấy lịch sử yêu cầu bảo hành của khách hàng
$stm = $pdo->prepare("SELECT wa.*, u.FullName, u.Email, u.Phone 
                      FROM warranty_accepts wa
                      LEFT JOIN users u ON wa.CustomerID = u.id
                      WHERE wa.CustomerID = :customerID");
$stm->execute(['customerID' => $customerID]);
$history = $stm->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu Cầu Bảo Hành</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/android-icon-36x36.png" type="image/png">
    <style>
        .container{
            margin-bottom: 24px;
        }
        .error{
            text-align: center;
            font-size: 1.6rem;
            color: red;
            margin-top: 20px;
        }
        .success{
            text-align: center;
            font-size: 1.6rem;
            color: #007bff;
            margin-top: 20px;
        }
        .history-section {
            display: none;
            margin-top: 20px;
            width: 100%;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .history-section.show {
            display: block;
        }

        .history-items {
            display: flex;
            flex-direction: column;
            font-size: 1.7rem;
        }

        .history-item {
            padding: 20px;
            margin-bottom: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .history-item:hover {
            background-color: #f1f1f1;
        }

        .history-item h4 {
            margin: 0;
            font-size: 18px;
        }

        .history-item p {
            margin: 5px 0;
        }

        .buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .buttons button {
            margin: 0 15px;
            padding: 14px 30px;
            width: 100%;
            font-size: 18px;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #333;
            background-color: white;
        }

        .buttons button:hover {
            background-color: #fbff7a;
            color: black;
        }

        .buttons button.active {
            background-color: #fde800c3;
            color: black;
        }

        .form-section {
            display: none;
            margin-top: 20px;
            width: 100%;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-section.show {
            display: block;
        }
        .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-size: 16px;
        color: #333;
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .form-group input[type="text"], .form-group textarea {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .form-group button {
        background-color: #fde800c3;
        color: black;
        width: 100%;
        padding: 12px 25px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .form-group button:hover {
        background-color: #fbff7a;
    }
    </style>
</head>
<body>
    <?php include("include/header.php"); ?>

    <div class="container">
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="buttons">
            <button type="button" onclick="showWarrantyForm()">Yêu cầu bảo hành</button>
            <button type="button" onclick="showWarrantyHistory()">Lịch sử bảo hành</button>
        </div>

        <div class="form-section" id="warrantyForm">
            <form method="POST" action="warranty.php">
                <div class="form-group">
                    <label for="billSeri">Mã hóa đơn (Seri):</label>
                    <input type="text" id="billSeri" name="billSeri" required>
                </div>
                <div class="form-group">
                    <label for="reason">Lý do bảo hành:</label>
                    <input type="text" id="reason" name="reason" required>
                </div>
                <div class="form-group">
                    <label for="description">Mô tả chi tiết:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" name="submitRequest">Gửi yêu cầu</button>
                </div>
            </form>
        </div>

        <div class="history-section" id="historyTable">
            <div class="history-items">
                <?php if (isset($history) && !empty($history)): ?>
                    <?php foreach ($history as $item): ?>
                        <div class="history-item" onclick="viewWarrantyDetails(<?php echo $item['id']; ?>)">
                            <h4>Mã hóa đơn: <?php echo $item['BillSeri']; ?></h4>
                            <p><strong>Ngày bảo hành:</strong> <?php echo $item['WarrantyDate']; ?></p>
                            <p><strong>Lý do:</strong> <?php echo $item['Reason']; ?></p>
                            <p ><strong>Trạng thái:</strong> <?php ?> <span style="color: #e67e22; font-weight: bold;"><?php echo $item['Status']; ?></span></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có yêu cầu bảo hành nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Kiểm tra URL xem có tham số 'tab=history' không
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'history') {
                showWarrantyHistory(); 
            } else {
                showWarrantyForm();  
            }
        };

        function showWarrantyForm() {
            document.getElementById('warrantyForm').classList.add('show');
            document.getElementById('historyTable').classList.remove('show');
            setActiveButton(0);
        }

        function showWarrantyHistory() {
            document.getElementById('historyTable').classList.add('show');
            document.getElementById('warrantyForm').classList.remove('show');
            setActiveButton(1);
        }

        function setActiveButton(index) {
            const buttons = document.querySelectorAll('.buttons button');
            buttons.forEach((btn, i) => {
                if (i === index) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }
        function viewWarrantyDetails(warrantyID) {
            window.location.href = "warranty_detail.php?id=" + warrantyID;
        }
    </script>

    <?php
        include("include/footer.php");
    ?>
    <script src="js/logoutModal.js"></script> 
</body>
</html>
