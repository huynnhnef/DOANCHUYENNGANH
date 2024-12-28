<?php
session_start();
require "../config.php";

$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
if (!$user_email) {
    header("Location: ../account/login.php");
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
$quantity = (int)$_POST['quantity'];  


$stmt = $pdo->prepare("SELECT id, Quantity FROM carts WHERE CustomerID = :customer_id AND ProductID = :product_id");
$stmt->execute(['customer_id' => $customer_id, 'product_id' => $product_id]);
$cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cart_item) {
    
    if ($quantity > 0) {
        $stmt = $pdo->prepare("UPDATE carts SET Quantity = :quantity WHERE id = :cart_id");
        $stmt->execute(['quantity' => $quantity, 'cart_id' => $cart_item['id']]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM carts WHERE id = :cart_id");
        $stmt->execute(['cart_id' => $cart_item['id']]);
    }
} else {
    
    if ($quantity > 0) {
        $stmt = $pdo->prepare("INSERT INTO carts (CustomerID, ProductID, Quantity) VALUES (:customer_id, :product_id, :quantity)");
        $stmt->execute(['customer_id' => $customer_id, 'product_id' => $product_id, 'quantity' => $quantity]);
    }
}

echo json_encode(["success" => true]);
?>
