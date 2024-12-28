<?php
session_start();
header('Content-type: text/html; charset=utf-8');
include("momo_helper.php");
// Đọc cấu hình từ file config.json
$config = json_decode(file_get_contents('config.json'), true);

// Kiểm tra xem có đọc được dữ liệu từ file JSON không
if ($config === null) {
    die("Lỗi khi đọc file config.json");
}

// Lấy thông tin từ cấu hình
$partnerCode = $config['partnerCode'];
$accessKey = $config['accessKey'];
$secretKey = $config['secretKey'];

// Kiểm tra session có tồn tại hay không
if (!isset($_SESSION['user_email'])) {
    echo "Vui lòng đăng nhập để thanh toán!";
    exit;
}

// Nhận tổng tiền từ giỏ hàng
$amount = (int)$_GET['total-price']; // Tổng tiền từ cart.php
$orderInfo = "Thanh toán qua mã ATM MoMo";
$orderId = time() . "";
$redirectUrl = "http://phonevibe.vn:3000/bill.php"; // URL chuyển hướng sau khi thanh toán thành công
$ipnUrl = "http://phonevibe.vn:3000/momo_php/return.php"; // URL IPN (thông báo trạng thái thanh toán)
$extraData = "";

// Thông tin MoMo
$requestId = time() . "";
$requestType = "payWithATM";

// Tạo chữ ký HMAC SHA256
$rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;

// Tạo chữ ký
$signature = hash_hmac("sha256", $rawHash, $secretKey);

// Dữ liệu gửi tới API MoMo
$data = array(
    'partnerCode' => $partnerCode,
    'partnerName' => "Test",
    "storeId" => "MomoTestStore",
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
);

// Gửi yêu cầu POST tới MoMo API
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$result = execPostRequest($endpoint, json_encode($data));

// Kiểm tra kết quả trả về từ MoMo
if ($result) {
    // Giải mã JSON
    $jsonResult = json_decode($result, true);

    // Kiểm tra xem có trả về 'payUrl'
    if (json_last_error() == JSON_ERROR_NONE && isset($jsonResult['payUrl'])) {
        // Chuyển hướng người dùng tới URL thanh toán MoMo
        header('Location: ' . $jsonResult['payUrl']);
        exit;
    } else {
        // Nếu không có 'payUrl' hoặc lỗi giải mã JSON
        echo "Lỗi khi giải mã dữ liệu JSON hoặc không có payUrl!<br>";
        echo "MoMo API Response: " . $result;
    }
} else {
    echo "Lỗi khi kết nối đến API MoMo. Vui lòng thử lại!";
}
?>
