<?php
session_start();

require_once("./config.php");

$vnp_SecureHash = $_GET['vnp_SecureHash'];
$inputData = array();
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

unset($inputData['vnp_SecureHash']);
ksort($inputData);
$i = 0;
$hashData = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}

$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VNPAY RESPONSE</title>
    <link href="/vnpay_php/assets/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="container">
        <div class="header clearfix">
            <h3 class="text-muted">VNPAY RESPONSE</h3>
        </div>
        <div class="table-responsive">
            <div class="form-group">
                <label>Mã đơn hàng:</label>
                <label><?php echo $_GET['vnp_TxnRef'] ?></label>
            </div>
            <div class="form-group">
                <label>Số tiền:</label>
                <label><?php echo $_GET['vnp_Amount'] ?></label>
            </div>
            <div class="form-group">
                <label>Nội dung thanh toán:</label>
                <label><?php echo $_GET['vnp_OrderInfo'] ?></label>
            </div>
            <div class="form-group">
                <label>Mã phản hồi (vnp_ResponseCode):</label>
                <label><?php echo $_GET['vnp_ResponseCode'] ?></label>
            </div>
            <div class="form-group">
                <label>Mã GD Tại VNPAY:</label>
                <label><?php echo $_GET['vnp_TransactionNo'] ?></label>
            </div>
            <div class="form-group">
                <label>Kết quả:</label>
                <label>
                    <?php
                    require("../config.php");
                    if ($secureHash == $vnp_SecureHash) {
                        if ($_GET['vnp_ResponseCode'] == '00') {
                            echo "<span style='color:blue'>Thanh toán thành công</span>";

                            if (isset($_SESSION['user_email'])) {
                                $user_email = $_SESSION['user_email'];

                                $stmt = $pdo->prepare("SELECT id FROM users WHERE Email = :user_email");
                                $stmt->execute(['user_email' => $user_email]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($user) {
                                    $customer_id = $user['id']; 

                                    $stmt = $pdo->prepare("SELECT Address FROM users WHERE id = :customer_id");
                                    $stmt->execute(['customer_id' => $customer_id]);
                                    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $shipping_address = $user_data['Address'];

                                    $stmt = $pdo->prepare("INSERT INTO orders (CustomerID, Status, ShippingAddress, OrderDate) 
                                                    VALUES (:customer_id, 'Đang xử lý', :shipping_address, NOW())");
                                    $stmt->execute([
                                        'customer_id' => $customer_id,
                                        'shipping_address' => $shipping_address
                                    ]);
                                    $order_id = $pdo->lastInsertId(); 

                                    // Kiểm tra nếu là "Mua ngay" 
                                    if (isset($_SESSION['direct_buy'])) {
                                        $product = $_SESSION['direct_buy'];

                                        // Thêm chi tiết đơn hàng vào bảng orderdetails
                                        $stmt = $pdo->prepare("INSERT INTO orderdetails (OrderID, ProductID, Quantity, Price, TotalAmount) 
                                                                VALUES (:order_id, :product_id, :quantity, :price, :total_amount)");
                                        $stmt->execute([
                                            'order_id' => $order_id,
                                            'product_id' => $product['product_id'],
                                            'quantity' => $product['quantity'],
                                            'price' => $product['price'],
                                            'total_amount' => $product['total_amount']
                                        ]);
                                        unset($_SESSION['direct_buy']);
                                    } else {
                                        // Lấy thông tin giỏ hàng nếu là thanh toán qua giỏ hàng
                                        $stmt = $pdo->prepare("SELECT c.ProductID, c.Quantity, p.Price 
                                                            FROM carts c 
                                                            JOIN products p ON c.ProductID = p.id
                                                            WHERE c.CustomerID = :customer_id");
                                        $stmt->execute(['customer_id' => $customer_id]);
                                        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        // Thêm chi tiết đơn hàng vào bảng orderdetails
                                        foreach ($cart_items as $item) {
                                            $total_amount = $item['Price'] * $item['Quantity'];
                                            $stmt = $pdo->prepare("INSERT INTO orderdetails (OrderID, ProductID, Quantity, Price, TotalAmount) 
                                                                VALUES (:order_id, :product_id, :quantity, :price, :total_amount)");
                                            $stmt->execute([
                                                'order_id' => $order_id,
                                                'product_id' => $item['ProductID'],
                                                'quantity' => $item['Quantity'],
                                                'price' => $item['Price'],
                                                'total_amount' => $total_amount
                                            ]);
                                        }

                                        // Xóa giỏ hàng của khách hàng sau khi thanh toán thành công
                                        $stmt = $pdo->prepare("DELETE FROM carts WHERE CustomerID = :customer_id");
                                        $stmt->execute(['customer_id' => $customer_id]);
                                    }

                                    // Xử lý mã Seri cho hóa đơn
                                    function generateUniqueSeri($pdo) {
                                        do {
                                            $seri = 'BILL_' . microtime(true) . '_' . random_int(100000, 999999);
                                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM bills WHERE Seri = :seri");
                                            $stmt->execute(['seri' => $seri]);
                                            $count = $stmt->fetchColumn();
                                        } while ($count > 0); 
                                        
                                        return $seri;
                                    }

                                    $seri = generateUniqueSeri($pdo);

                                    // Thêm Orderdetails vào bảng bills
                                    $stmt = $pdo->prepare("SELECT id, TotalAmount FROM orderdetails WHERE OrderID = :order_id");
                                    $stmt->execute(['order_id' => $order_id]);
                                    $orderdetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($orderdetails) > 0) {
                                        $total_amount = 0;
                                        foreach ($orderdetails as $item) {
                                            $total_amount += $item['TotalAmount'];
                                        }

                                        foreach ($orderdetails as $item) {
                                            $stmt = $pdo->prepare("INSERT INTO bills (Seri, OrderdetailsID, TotalAmount, BillDate, Status) 
                                                                VALUES (:seri, :orderdetail_id, :total_amount, NOW(), 'Đã thanh toán')");
                                            $stmt->execute([
                                                'seri' => $seri,
                                                'orderdetail_id' => $item['id'],
                                                'total_amount' => $total_amount
                                            ]);
                                        }

                                        header("Location: http://phonevibe.vn:3000/bill.php?orderdetail_id=" . $orderdetails[0]['id']);
                                        exit();
                                    } else {
                                        echo "<span style='color:red'>Không tìm thấy chi tiết đơn hàng.</span>";
                                    }
                                } else {
                                    echo "<span style='color:red'>Không tìm thấy người dùng với email: $user_email.</span>";
                                }
                            } else {
                                echo "<span style='color:red'>Không tìm thấy email trong session.</span>";
                            }
                        } else {
                            echo "<span style='color:red'>Thanh toán thất bại</span>";
                        }
                    } else {
                        echo "<span style='color:red'>Chữ ký không hợp lệ</span>";
                    }
                    ?>
                </label>
            </div>
        </div>
    </div>
</body>
</html>
