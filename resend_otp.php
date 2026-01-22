<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
include "config.php"; // session_start() is already inside config.php

$email = $_GET['email'] ?? '';

if (!$email) {
    die("❌ Invalid request.");
}

// Generate a 6-digit OTP
$otp    = rand(100000, 999999);
$expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

try {
    // ----------------- Setup PHPMailer -----------------
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'siddharthshelar455@gmail.com';
    $mail->Password   = 'bmoiamjbdkzljxoa';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('noreply@gmail.com', 'Student Registration');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body    = "Your new OTP is <strong>$otp</strong>. It expires in 5 minutes.";

    $mail->send(); // ✅ Email sent

    // ----------------- Update OTP in Database -----------------
    $update = "
        UPDATE users
        SET otp_code = $1,
            otp_expires_at = $2
        WHERE email = $3 AND is_verified = false
    ";

    $result = pg_query_params($conn, $update, [(string)$otp, $expiry, $email]);

    if ($result && pg_affected_rows($result) === 1) {

        // ✅ START RESEND COOLDOWN TIMER (CORRECT PLACE)
        $_SESSION['last_otp_resend'] = time();

        // Redirect back to verification page
        header("Location: verify_otp.php?email=" . urlencode($email));
        exit();

    } else {
        echo "❌ Failed to update OTP. Make sure the email exists and is not already verified.";
    }

} catch (Exception $e) {
    echo "❌ Failed to send OTP email: " . htmlspecialchars($mail->ErrorInfo);
}
?>
