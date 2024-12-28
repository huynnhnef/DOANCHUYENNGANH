<?php
session_start();
require "../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $errors = [];

    $stmtEmail = $pdo->prepare('SELECT * FROM users WHERE Email = :Email');
    $stmtEmail->execute([':Email' => $email]);


    $stmtUsername = $pdo->prepare('SELECT * FROM users WHERE Username = :Username');
    $stmtUsername->execute([':Username' => $username]);

    
    if ($stmtEmail->rowCount() > 0) {
        $errors[] = "Email đã tồn tại!";
    }


    if ($stmtUsername->rowCount() > 0) {
        $errors[] = "Tên người dùng đã tồn tại!";
    }


    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        $_SESSION['post_data'] = $_POST;
        header("Location: register.php");
        exit();
    } else {
        $roleID = 8; 
        $user_status="Đang hoạt động";
        $sql = $pdo->prepare('INSERT INTO users (Username, Password, FullName, Email, Phone, Address, RoleID, user_status) VALUES (:Username, :Password, :FullName, :Email, :Phone, :Address, :RoleID, :user_status)');
        $sql->execute([
            ':Username' => $username, 
            ':Password' => $password, 
            ':FullName' => $fullname, 
            ':Email' => $email, 
            ':Phone' => $phone, 
            ':Address' => $address, 
            ':RoleID' => $roleID,
            ':user_status'=>$user_status
        ]);

        $_SESSION['register_success'] = "Đăng ký thành công!";
        header("Location: login.php");
        exit();
    }
}
?>
