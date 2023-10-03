<?php
    if ($_SERVER['PHP_SELF'] == '/send.php'){
        header('location:/index.php');
    }
    require('conn.php');
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    date_default_timezone_set('Asia/Taipei');

    $mail = new PHPMailer(true);

    function send_mail($mail_address, $name, $subject, $body)
    {
        global $mail;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'team07.NPO@gmail.com';
        $mail->Password   = 'arfizvefoocdvvln';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;
        $mail->CharSet = "utf-8";

        $mail->setFrom('team07.NPO@gmail.com', '機咪與恩波團隊');
        $mail->AddAddress($mail_address, $name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = nl2br($body);
        $mail->Send();
    }
?>