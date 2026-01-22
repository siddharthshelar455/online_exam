<?php
include "config.php";

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Invalid reset link");
}

$query = pg_query($conn, "SELECT * FROM users WHERE reset_token='$token'");
if (pg_num_rows($query) != 1) {
    die("Invalid or expired token");
}

if (isset($_POST['reset'])) {

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    pg_query($conn, "
        UPDATE users 
        SET password='$password', reset_token=NULL 
        WHERE reset_token='$token'
    ");

    echo "✅ Password updated successfully. <a href='login.php'>Login</a>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reset Password</title>

<style>
/* ===== GLOBAL RESET ===== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", sans-serif;
}

/* ===== PAGE BACKGROUND ===== */
body {
    min-height: 100vh;
    background: linear-gradient(135deg, #4f46e5, #9333ea);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

/* ===== CARD ===== */
.reset-card {
    background: #ffffff;
    width: 100%;
    max-width: 420px;
    padding: 35px;
    border-radius: 15px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.25);
    animation: slideUp 0.6s ease;
}

/* ===== ANIMATION ===== */
@keyframes slideUp {
    from {
        transform: translateY(40px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* ===== HEADER ===== */
.reset-card h2 {
    text-align: center;
    color: #1f2937;
    margin-bottom: 10px;
}

.reset-card p {
    text-align: center;
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 25px;
}

/* ===== INPUT ===== */
.input-group {
    margin-bottom: 20px;
    position: relative;
}

.input-group input {
    width: 100%;
    padding: 14px 15px;
    border-radius: 10px;
    border: 1.5px solid #d1d5db;
    font-size: 15px;
    transition: 0.3s;
}

.input-group input:focus {
    border-color: #6366f1;
    outline: none;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
}

/* ===== PASSWORD STRENGTH HINT ===== */
.hint {
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
}

/* ===== BUTTON ===== */
button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(to right, #6366f1, #8b5cf6);
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(99,102,241,0.4);
}

button:active {
    transform: translateY(0);
}

/* ===== FOOTER LINK ===== */
.back-link {
    margin-top: 20px;
    text-align: center;
}

.back-link a {
    color: #6366f1;
    text-decoration: none;
    font-size: 14px;
}

.back-link a:hover {
    text-decoration: underline;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 480px) {
    .reset-card {
        padding: 25px;
    }

    button {
        font-size: 15px;
    }
}
</style>
</head>

<body>

<div class="reset-card">
    <h2>Reset Password</h2>
    <p>Create a strong new password</p>

    <form method="post">
        <div class="input-group">
            <input type="password" name="password" placeholder="New Password" required>
            <div class="hint">Minimum 8 characters recommended</div>
        </div>

        <button name="reset">Reset Password</button>
    </form>

    <div class="back-link">
        <a href="login.php">← Back to Login</a>
    </div>
</div>

</body>
</html>
