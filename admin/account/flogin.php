<?php
require "../../config.php";
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $sql = $pdo->prepare('SELECT * FROM users WHERE Email = :Email');
        $sql->execute([':Email' => $email]);
        $iEmail = $sql->fetch(PDO::FETCH_ASSOC);

       
        if ($iEmail) {
            
            if($iEmail['user_status']=="Đang hoạt động"){
                if (password_verify($password, $iEmail['Password'])) {
              
                    if (in_array(intval($iEmail['RoleID']), [5, 6, 7])) {
                        $_SESSION['user_email'] = $iEmail['Email'];
                        $_SESSION['RoleID'] = $iEmail['RoleID'];
    
                        if ($_SESSION['RoleID'] == 5) {
                            header("Location: ../manager/user.php");
                        } elseif ($_SESSION['RoleID'] == 6) {
                            header("Location: ../manager/product.php");  
                        } elseif ($_SESSION['RoleID'] == 7) {
                            header("Location: ../manager/order.php");  
                        }
                        exit();
                    } else {
                        $_SESSION['login_error'] = "Bạn không có quyền truy cập!";
                        header("Location: login.php");
                        exit();
                    }
                } else {
                    $_SESSION['login_error'] = "Tài khoản hoặc mật khẩu không chính xác!";
                    header("Location: login.php");
                    exit();
                }
            }
    
            else{
                $_SESSION['check_status']="<div style='color: red; font-weight: bold;'>Email hoặc mật khẩu không chính xác!</div>";
                header("Location: login.php");
            }
            
        } else {
            $_SESSION['login_error'] = "Tài khoản hoặc mật khẩu không chính xác!";
            header("Location: login.php");
            exit();
        }
    }
} catch (PDOException $e) {
    echo "Đăng nhập thất bại!" . $e->getMessage();
}
