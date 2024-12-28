<?php
session_start();
require "../config.php";
require "sendmail.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gửi mã xác thực
    if (isset($_POST['send_code'])) {
        $email = $_POST['email'];

        $stmt = $pdo->prepare('SELECT * FROM users WHERE Email = :Email');
        $stmt->execute([':Email' => $email]);

        if ($stmt->rowCount() > 0) {
            $code = rand(100000, 999999);
            
            $queryUpdate = $pdo->prepare('UPDATE users SET Code = :Code WHERE Email = :Email');
            $queryUpdate->execute([':Email' => $email, ':Code' => $code]);

            if (sendMail($email, $code)) {
                $_SESSION['message'] = "Mã xác thực đã được gửi đến email của bạn.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Gửi thất bại";
                $_SESSION['message_type'] = "error";
            }
        } else {
            $_SESSION['message'] = "Email không tồn tại trong hệ thống";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: quenmk.php?email=" . urlencode($email));
        exit();
    }

   
    elseif (isset($_POST['confirm_code'])) {
        $email = $_POST['email'];
        $code = $_POST['code'];
    
        $stmt = $pdo->prepare('SELECT * FROM users WHERE Email = :Email AND Code = :Code');
        $stmt->execute([':Email' => $email, ':Code' => $code]);
        
        if ($stmt->rowCount() > 0) {
            header("Location: laylaimk.php?email=" . urlencode($email));
            exit();
        } else {
            $_SESSION['message'] = "Mã xác thực không chính xác";
            $_SESSION['message_type'] = "error";
            header("Location: quenmk.php?email=" . urlencode($email));
            exit();
        }
    }
    
}
?>
