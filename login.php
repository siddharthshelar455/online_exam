<?php
include "config.php";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = pg_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = pg_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {

        if (!$user['is_verified']) {
            die("❌ Please verify your email first.");
        }

        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;

    } else {
        $error = "Invalid Email or Password. Please Register Your Email First";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: #ffffff;
            padding: 30px;
            width: 340px;
            border-radius: 12px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.25);
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .login-box input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            background: #667eea;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-btn:hover {
            background: #5563d8;
        }

        /* ✅ Forgot password button */
        .forgot-btn {
            margin-top: 12px;
            width: 100%;
            background: none;
            border: none;
            color: #667eea;
            font-size: 14px;
            cursor: pointer;
            text-decoration: underline;
        }

        .forgot-btn:hover {
            color: #5563d8;
        }

        .error {
            color: #e74c3c;
            background: #fdecea;
            padding: 8px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="login-box">
    <h2>Student Login</h2>

    <?php if (isset($error)) { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="post">
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>

        <button class="login-btn" name="login">Login</button>
    </form>

    <!-- ✅ Forgot Password Button -->
    <a href="forgot_pass.php">
        <button type="button" class="forgot-btn">Forgot Password?</button>
    </a>
</div>

</body>
</html>
