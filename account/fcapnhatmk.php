<?php
session_start();
require "../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $new_password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';


    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('UPDATE users SET Password = :Password WHERE Email = :Email');
        if ($stmt->execute([':Password' => $hashed_password, ':Email' => $email])) {
            $_SESSION['message'] = "Cập nhật mật khẩu thành công.";
            $_SESSION['message_type'] = "success";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['message'] = "Cập nhật mật khẩu thất bại.";
            $_SESSION['message_type'] = "error";
            
        }
    } else {
        $_SESSION['message'] = "Mật khẩu khẩu không trùng khớp.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: laylaimk.php?email=" . urlencode($email));
    exit();
}
?>
