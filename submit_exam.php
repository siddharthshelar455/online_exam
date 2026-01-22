<?php
include "config.php";

$score = 0;

foreach ($_POST['ans'] as $qid => $ans) {
    $q = pg_fetch_assoc(
        pg_query($conn, "SELECT correct_option FROM questions WHERE id=$qid")
    );
    if ($q['correct_option'] == $ans) {
        $score++;
    }
}

$user_id = $_SESSION['user_id'];
pg_query($conn, "INSERT INTO results(user_id, score)
                 VALUES($user_id, $score)");

header("Location: result.php");
exit;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submitting Exam</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .box {
            background: #fff;
            padding: 30px;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
            text-align: center;
        }

        .loader {
            border: 5px solid #eee;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
        }

        p {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="box">
    <h2>Submitting Your Exam</h2>
    <div class="loader"></div>
    <p>Please wait while we calculate your score...</p>
</div>

</body>
</html>