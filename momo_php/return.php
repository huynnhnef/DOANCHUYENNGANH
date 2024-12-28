<?php
session_start();
require_once('../config.php');
include('momo_helper.php');

// Nhận dữ liệu POST từ MoMo
$data = file_get_contents("php://input");
$decodedData = json_decode($data, true);

// Kiểm tra nếu MoMo gửi đủ thông tin (orderId, signature, responseCode, ...)
if (isset($decodedData['orderId']) && isset($decodedData['signature'])) {
    $orderId = $decodedData['orderId'];
    $signature = $decodedData['signature'];
    $responseCode = $decodedData['responseCode'];  // 0 nếu thanh toán thành công, các mã khác nếu thất bại

    // Tạo chữ ký để kiểm tra tính hợp lệ của dữ liệu từ MoMo
    $rawHash = "accessKey=" . $accessKey . "&orderId=" . $orderId . "&responseCode=" . $responseCode . "&partnerCode=" . $partnerCode;
    $checkSignature = hash_hmac("sha256", $rawHash, $secretKey);

    // Kiểm tra chữ ký có hợp lệ không
    if ($signature == $checkSignature) {
        // Nếu chữ ký hợp lệ, tiếp tục xử lý kết quả thanh toán
        if ($responseCode == '0') {  // Thanh toán thành công
            // Lấy thông tin khách hàng từ session
            $stmt = $pdo->prepare("SELECT id, Address FROM users WHERE Email = :email");
            $stmt->execute(['email' => $_SESSION['user_email']]);
            $iEmail = $stmt->fetch(PDO::FETCH_ASSOC);
            $customer_id = $iEmail['id']; // Lấy CustomerID từ bảng users
            $shippingAddress = $iEmail['Address']; // Lấy địa chỉ giao hàng từ trường Address trong bảng users

            // Lưu thông tin đơn hàng vào bảng orders
            $stmt = $pdo->prepare("INSERT INTO orders (CustomerID, Status, ShippingAddress, OrderDate) 
                                   VALUES (:customer_id, 'Đang chờ', :shipping_address, NOW())");
            $stmt->execute([
                'customer_id' => $customer_id,
                'shipping_address' => $shippingAddress
            ]);
            $orderId = $pdo->lastInsertId(); // Lấy ID đơn hàng vừa tạo

            // Lấy thông tin chi tiết đơn hàng từ giỏ hàng
            $stmt = $pdo->prepare("SELECT ProductID, Quantity, Price FROM carts WHERE CustomerID = :customer_id");
            $stmt->execute(['customer_id' => $customer_id]);
            $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Thêm chi tiết đơn hàng vào bảng orderdetails
            foreach ($cart_items as $item) {
                $totalAmount = $item['Price'] * $item['Quantity'];
                $stmt = $pdo->prepare("INSERT INTO orderdetails (OrderID, ProductID, Quantity, Price, TotalAmount) 
                                       VALUES (:order_id, :product_id, :quantity, :price, :total_amount)");
                $stmt->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['ProductID'],
                    'quantity' => $item['Quantity'],
                    'price' => $item['Price'],
                    'total_amount' => $totalAmount
                ]);
            }

            // Tạo mã Seri duy nhất cho hóa đơn
            function generateUniqueSeri($pdo) {
                do {
                    $seri = 'BILL_' . microtime(true) . '_' . random_int(100000, 999999);
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bills WHERE Seri = :seri");
                    $stmt->execute(['seri' => $seri]);
                    $count = $stmt->fetchColumn();
                } while ($count > 0); // Nếu mã Seri đã tồn tại, tạo lại

                return $seri;
            }

            // Tạo mã Seri duy nhất
            $seri = generateUniqueSeri($pdo);

            // Lấy thông tin chi tiết đơn hàng từ bảng orderdetails và lưu vào bảng bills
            $stmt = $pdo->prepare("SELECT id, TotalAmount FROM orderdetails WHERE OrderID = :order_id");
            $stmt->execute(['order_id' => $orderId]);
            $orderdetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($orderdetails as $item) {
                $stmt = $pdo->prepare("INSERT INTO bills (Seri, OrderdetailsID, TotalAmount, BillDate, Status) 
                                       VALUES (:seri, :orderdetails_id, :total_amount, NOW(), 'Đã thanh toán')");
                $stmt->execute([
                    'seri' => $seri,
                    'orderdetails_id' => $item['id'],  // Lưu đúng orderdetails_id
                    'total_amount' => $item['TotalAmount']
                ]);
            }

            // Xóa giỏ hàng sau khi thanh toán thành công
            $stmt = $pdo->prepare("DELETE FROM carts WHERE CustomerID = :customer_id");
            $stmt->execute(['customer_id' => $customer_id]);

            // Lấy orderdetail_id và thêm vào URL
            $stmt = $pdo->prepare("SELECT id FROM orderdetails WHERE OrderID = :order_id LIMIT 1");
            $stmt->execute(['order_id' => $orderId]);
            $orderdetail = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($orderdetail) {
                $orderdetail_id = $orderdetail['id'];  // Lấy orderdetail_id

                // Nhận tất cả các tham số từ GET (dữ liệu MoMo trả về)
                $queryParams = $_GET;

                // Thêm orderdetail_id vào các tham số
                $queryParams['orderdetail_id'] = $orderdetail_id;

                // Tạo URL với các tham số đầy đủ
                $url = 'http://phonevibe.vn:3000/bill.php?' . http_build_query($queryParams);

                // Debug URL
                echo "Redirecting to: " . $url;

                // Chuyển hướng đến trang bill.php với orderdetail_id
                header("Location: $url");
                exit();
            } else {
                echo "Không tìm thấy thông tin chi tiết đơn hàng!";
            }
        } else {
            // Nếu thanh toán không thành công
            echo "Thanh toán không thành công!";
        }
    } else {
        // Nếu chữ ký không hợp lệ
        echo "Chữ ký không hợp lệ!";
    }
} else {
    // Nếu thiếu thông tin trong dữ liệu POST từ MoMo
    echo "Dữ liệu không hợp lệ!";
}
?>
