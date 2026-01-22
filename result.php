<?php
include "config.php";

$uid = $_SESSION['user_id'];
$res = pg_fetch_assoc(
    pg_query($conn, "SELECT score FROM results WHERE user_id=$uid ORDER BY id DESC LIMIT 1")
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Exam Result</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .result-box {
            background: #ffffff;
            padding: 30px;
            width: 350px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        }

        .result-box h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        .score {
            font-size: 40px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 25px;
        }

        .result-box a {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            background: #4CAF50;
            color: #fff;
            border-radius: 5px;
            font-size: 16px;
        }

        .result-box a:hover {
            background: #43a047;
        }
    </style>
</head>

<body>

<div class="result-box">
    <h2>Your Score</h2>
    <div class="score"><?= $res['score'] ?></div>

    <a href="certificate.php" style="margin-bottom:10px; display:block;">
        Download Certificate
    </a>

    <a href="dashboard.php">Back to Dashboard</a>
</div>



</body>
</html>