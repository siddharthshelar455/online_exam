<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* 🔐 Check if exam already completed */
$checkExam = pg_query_params(
    $conn,
    "SELECT 1 FROM results WHERE user_id = $1 LIMIT 1",
    [$user_id]
);

$exam_completed = pg_num_rows($checkExam) > 0;

/* Set exam time only if not completed */
if (!$exam_completed) {
    $_SESSION['exam_start_time'] = time();
    $_SESSION['exam_duration'] = 60 * 5; // 5 minutes
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Dashboard</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">Online Examination</span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-5">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-body text-center">

                    <h3 class="card-title mb-3">Welcome to Online Exam</h3>
                    <p class="text-muted">Exam Duration: <strong>5 Minutes</strong></p>

                    <?php if ($exam_completed) { ?>
                        <div class="alert alert-success">
                            ✅ You have already completed the exam.
                        </div>
                        <a href="result.php" class="btn btn-success">
                            View Result
                        </a>
                    <?php } else { ?>
                        <div class="alert alert-warning">
                            ⚠️ You can attempt the exam only once.
                        </div>
                        <a href="exam.php" class="btn btn-primary btn-lg">
                            Start Exam
                        </a>
                    <?php } ?>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>