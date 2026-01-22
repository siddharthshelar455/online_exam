<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
include "config.php";

/* ✅ PASSWORD VALIDATION FUNCTION */
function isValidPassword($password) {
    return preg_match(
        '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
        $password
    );
}

$error = '';
$error_is_html = false;

if (isset($_POST['register'])) {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $rawPassword = $_POST['password'];

    /* ✅ EMAIL VALIDATION (ADDED FEATURE) */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Please enter a valid email address.";
    }

    /* ✅ PASSWORD VALIDATION */
    elseif (!isValidPassword($rawPassword)) {
        $error_is_html = true;
        $error = "
        ❌ Password must meet the following requirements:
        <ul style='text-align:left; margin-top:10px;'>
            <li>Minimum 8 characters</li>
            <li>At least 1 uppercase letter (A–Z)</li>
            <li>At least 1 lowercase letter (a–z)</li>
            <li>At least 1 number (0–9)</li>
            <li>At least 1 special character (!@#$%^&*)</li>
        </ul>";
    }

    else {

        $password = password_hash($rawPassword, PASSWORD_DEFAULT);

        $checkQuery = "SELECT 1 FROM users WHERE email = $1";
        $checkResult = pg_query_params($conn, $checkQuery, [$email]);

        if (pg_num_rows($checkResult) > 0) {
            $error = "❌ Email already registered. Please use another email.";
        } else {

            /* OTP + EXPIRY */
            $otp     = rand(100000, 999999);
            $expiry  = date('Y-m-d H:i:s', strtotime('+5 minutes'));

            $insertQuery = "
                INSERT INTO users (name, email, password, otp_code, otp_expires_at, is_verified)
                VALUES ($1, $2, $3, $4, $5, false)
            ";
            pg_query_params($conn, $insertQuery, [$name, $email, $password, $otp, $expiry]);

            /* MAIL CODE — UNCHANGED */
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'siddharthshelar455@gmail.com';
                $mail->Password   = 'bmoiamjbdkzljxoa';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('noreply@gmail.com', 'Student Registration');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = 'Email Verification OTP';
                $mail->Body    = "
                    <p>Hello <strong>$name</strong>,</p>
                    <p>Your OTP is: <strong>$otp</strong></p>
                    <p>This OTP is valid for <b>5 minutes</b>.</p>
                ";

                $mail->send();
                header("Location: verify_otp.php?email=" . urlencode($email));
                exit();

            } catch (Exception $e) {
                $error = "❌ OTP email could not be sent.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #667eea, #764ba2);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 16px;
    }

    .container {
        background-color: #fff;
        width: 100%;
        max-width: 420px;
        padding: 40px 30px;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }

    h2 {
        text-align: center;
        margin-bottom: 28px;
        font-size: 28px;
        color: #333;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #555;
        font-size: 14px;
    }

    input {
        width: 100%;
        padding: 12px;
        margin-bottom: 18px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 15px;
        transition: 0.3s;
    }

    input:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 6px rgba(102, 126, 234, 0.5);
    }

    button {
        width: 100%;
        padding: 14px;
        background-color: #667eea;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background-color: #5a67d8;
    }

    .error {
        background-color: #ffe0e0;
        color: #b30000;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 600;
        font-size: 14px;
    }

    /* ---------- RESPONSIVE BREAKPOINTS ---------- */

    /* Small phones */
    @media (max-width: 360px) {
        .container {
            padding: 25px 18px;
        }

        h2 {
            font-size: 22px;
        }

        input, button {
            font-size: 14px;
            padding: 11px;
        }
    }

    /* Phones */
    @media (max-width: 480px) {
        .container {
            padding: 30px 22px;
        }

        h2 {
            font-size: 24px;
        }
    }

    /* Tablets */
    @media (min-width: 481px) and (max-width: 768px) {
        .container {
            max-width: 450px;
        }
    }

    /* Large screens */
    @media (min-width: 1200px) {
        .container {
            max-width: 480px;
        }
    }
</style>

</head>
<body>
<div class="container">
    <h2>Student Registration</h2>

    <?php if (!empty($error)) { ?>
    <div class="error">
        <?= $error_is_html ? $error : htmlspecialchars($error) ?>
    </div>
<?php } ?>


    <form method="post">
        <label>Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="register">Register</button>
    </form>
</div>
</body>
</html>
