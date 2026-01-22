<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'siddharthshelar455@gmail.com';
    $mail->Password = 'bmoiamjbdkzljxoa';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('your_email@gmail.com', 'Server Test');
    $mail->addAddress('your_email@gmail.com');

    $mail->Subject = 'SMTP Test';
    $mail->Body = 'Your server can send emails successfully.';

    $mail->send();
    echo "✅ Email sent successfully";
} catch (Exception $e) {
    echo "❌ Mail error: " . $mail->ErrorInfo;
}