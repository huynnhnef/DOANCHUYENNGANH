<?php
session_start();
require "../config.php";


$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
if (!$user_email) {
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :user_email");
$stmt->execute(['user_email' => $user_email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy người dùng!"]);
    exit;
}

$customer_id = $user['id'];  

$product_id = $_POST['product_id'];

$stmt = $pdo->prepare("DELETE FROM carts WHERE CustomerID = :customer_id AND ProductID = :product_id");
$stmt->execute(['customer_id' => $customer_id, 'product_id' => $product_id]);

echo json_encode(["success" => true]);
?>
