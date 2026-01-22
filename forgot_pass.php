<?php
include "config.php";

if (isset($_POST['forgot'])) {

    $email = $_POST['email'];

    $query = pg_query($conn, "SELECT * FROM users WHERE email='$email'");
    if ($query && pg_num_rows($query) == 1) {

        $user = pg_fetch_assoc($query);

        if (!$user['is_verified']) {
            $error = "Please verify your email first.";
        } else {

            // ✅ generate token
            $token = md5(uniqid());

            // ✅ save token
            pg_query($conn, "UPDATE users SET reset_token='$token' WHERE email='$email'");

            // ✅ reset link (LOCALHOST)
            $reset_link = "http://localhost/online_exam/reset.php?token=$token";

            $success = "Reset link generated:<br><a href='$reset_link'>$reset_link</a>";
        }

    } else {
        $error = "Email not found.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>

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

        .forgot-box {
            background: #ffffff;
            padding: 30px;
            width: 340px;
            border-radius: 12px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.25);
        }

        .forgot-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .forgot-box input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .forgot-btn {
            width: 100%;
            padding: 10px;
            background: #667eea;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .forgot-btn:hover {
            background: #5563d8;
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

        .success {
            color: #155724;
            background: #d4edda;
            padding: 8px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="forgot-box">
    <h2>Forgot Password</h2>

    <?php if (isset($error)) { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <?php if (isset($success)) { ?>
        <div class="success"><?php echo $success; ?></div>
    <?php } ?>

    <form method="post">
        <input type="email" name="email" placeholder="Enter Registered Email" required>
        <button class="forgot-btn" name="forgot">Submit</button>
    </form>

    <div class="back-link">
        <a href="login.php">← Back to Login</a>
    </div>
</div>

</body>
</html>
