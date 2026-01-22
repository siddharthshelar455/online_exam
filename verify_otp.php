<?php
include "config.php";

define('RESEND_COOLDOWN', 30); // seconds


/* ---------------- HELPER FUNCTIONS ---------------- */

function redirectToLogin($delay = 2)
{
    header("Refresh: $delay; url=login.php");
    exit();
}

function isOtpValidWithinFiveMinutes($otpExpiresAt)
{
    if (empty($otpExpiresAt)) {
        return false;
    }

    return strtotime($otpExpiresAt) >= time();
}

/* ---------------- MAIN LOGIC ---------------- */

$email   = $_GET['email'] ?? $_POST['email'] ?? '';
$error   = '';
$success = '';

if (isset($_POST['verify'])) {

    $otp = str_pad(trim($_POST['otp']), 6, '0', STR_PAD_LEFT);

    if (empty($email)) {
        $error = "❌ Invalid session. Please resend OTP.";
    } else {

        $query = "
            SELECT id, otp_expires_at
            FROM users
            WHERE email = $1
              AND otp_code = $2
              AND otp_expires_at >= NOW()
              AND is_verified = false
        ";

        $result = pg_query_params($conn, $query, [$email, $otp]);

        if ($result && pg_num_rows($result) === 1) {

            $row = pg_fetch_assoc($result);

            if (!isOtpValidWithinFiveMinutes($row['otp_expires_at'])) {
                $error = "❌ OTP expired. Please resend OTP.";
            } else {

                pg_query_params($conn, "
                    UPDATE users
                    SET is_verified = true,
                        otp_code = NULL,
                        otp_expires_at = NULL
                    WHERE email = $1
                ", [$email]);

                $success = "✅ Email verified successfully. Redirecting to login...";
                redirectToLogin(2);
            }

        } else {
            $error = "❌ Invalid or expired OTP";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Verify OTP</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
body {
    min-height: 100vh;
    margin: 0;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
}
.container {
    background: #ffffff;
    width: 100%;
    max-width: 380px;
    padding: 35px 30px;
    border-radius: 14px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}
h2 {
    text-align: center;
    color: #333;
    margin-bottom: 25px;
}
label {
    font-weight: 600;
}
input[type="text"] {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    margin-bottom: 18px;
}
button {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
}
.error {
    background: #ffe3e3;
    color: #c40000;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
}
.success {
    background: #e3ffe3;
    color: #00c400;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
}
.resend-link {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
}
</style>
</head>

<body>
<div class="container">
<h2>Email Verification</h2>

<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
    <label>Enter OTP</label>
    <input type="text" name="otp" placeholder="6-digit OTP" required>
    <button type="submit" name="verify">Verify OTP</button>
</form>

<a href="resend_otp.php?email=<?= urlencode($email) ?>" class="resend-link">
    Resend OTP
</a>
</div>
</body>
</html>
