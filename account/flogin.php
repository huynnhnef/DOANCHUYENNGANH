<?php
require "../config.php";
session_start(); 

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $sql = $pdo->prepare('SELECT * FROM users WHERE Email = :Email AND RoleID = 8');
        $sql->execute([':Email' => $email]);
        $iEmail = $sql->fetch(PDO::FETCH_ASSOC);

        if ($iEmail) {
            if($iEmail['user_status']=="Đang hoạt động"){
                if (password_verify($password, $iEmail['Password'])) {
                
                    $_SESSION['user_email'] = $iEmail['Email'];
                    header("Location: ../index.php");
                    exit();
                } else {
                    $_SESSION['login_error'] = "Email hoặc mật khẩu không chính xác!";
                    header("Location: login.php");
                    exit();
                }
            }
            else{
                $_SESSION['check_status']="<div style='color: red; font-weight: bold;'>Tài khoản của bạn đã bị khóa <br>
                Hãy liên hệ Hotline: <a href='tel:012345678' style='color: blue;'>012345678</a> để biết thêm chi tiết</div>";
                header("Location: login.php");
            }
            
        } else {
            $_SESSION['login_error'] = "Email hoặc mật khẩu không chính xác!";
            header("Location: login.php");
            exit();
        }
    }
} catch (PDOException $e) {
    echo "Đăng nhập thất bại!" . $e->getMessage();
}
?>
