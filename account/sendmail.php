<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '..\PHPMailer-master\src\Exception.php';
    require '..\PHPMailer-master\src\PHPMailer.php';
    require '..\PHPMailer-master\src\SMTP.php';

    
    //Hàm gửi mail
    function sendMail($toemail, $code){
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail-> Host = 'smtp.gmail.com';
            $mail->SMTPAuth=true;
            $mail->Username='nguyenloi1442@gmail.com';
            $mail->Password = 'upqjcacxbspuoojh';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom('nguyenloi1442@gmail.com');
            $mail->addAddress($toemail);

            $mail->isHTML(true);
            $mail->FromName='PhoneVibe';
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'PhoneVibe - Cập nhật mật khẩu mới';
            $mail->Body = 'Mã xác thực quên mật khẩu của bạn là:'." ".$code;

            $mail->send();
            return true;
        } catch (Exception  $e) {
            error_log('Lỗi gửi email: ' . $mail->ErrorInfo);
            return false;
        }
    }
    
?>