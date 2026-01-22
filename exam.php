<?php
include "config.php";

if (!isset($_SESSION['exam_start_time'])) {
    header("Location: dashboard.php");
}

$elapsed = time() - $_SESSION['exam_start_time'];
$remaining = $_SESSION['exam_duration'] - $elapsed;

if ($remaining <= 0) {
    header("Location: submit_exam.php");
    exit;
}

$result = pg_query($conn, "SELECT * FROM questions");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Online Exam</title>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #e3f2fd, #f8f9fa);
    margin: 0;
    padding: 0;
    user-select: none; /* Disable text selection */
}

/* ===== Main Container ===== */
.container {
    max-width: 900px;
    margin: 40px auto;
    background: #ffffff;
    padding: 35px 45px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}

h2 {
    text-align: center;
    color: #0d47a1;
    font-size: 32px;
}

h3 {
    text-align: center;
    color: #fff;
    background: #1976d2;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 35px;
}

/* Question */
.question-block {
    background: #f9fbfd;
    padding: 20px 25px;
    margin-bottom: 25px;
    border-radius: 12px;
    border-left: 6px solid #1976d2;
}

.question-block p {
    font-size: 18px;
    font-weight: bold;
}

.question-block label {
    display: block;
    padding: 8px 12px;
    margin-bottom: 8px;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #ddd;
    cursor: pointer;
}

input[type="radio"] {
    margin-right: 10px;
}

button {
    display: block;
    width: 260px;
    margin: 40px auto;
    padding: 14px;
    background: linear-gradient(135deg, #28a745, #218838);
    color: #fff;
    font-size: 18px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
}
</style>

<script>
/* ================= TIMER ================= */
let timeLeft = <?= $remaining ?>;

function startTimer() {
    let timer = setInterval(function () {
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;

        document.getElementById("timer").innerHTML =
            minutes + " : " + (seconds < 10 ? "0" : "") + seconds;

        timeLeft--;

        if (timeLeft < 0) {
            clearInterval(timer);
            document.getElementById("examForm").submit();
        }
    }, 1000);
}

/* ================= SECURITY FUNCTIONS ================= */

// Disable right click
document.addEventListener('contextmenu', e => e.preventDefault());

// Disable copy, paste, cut
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('paste', e => e.preventDefault());
document.addEventListener('cut', e => e.preventDefault());

// Disable keyboard shortcuts
document.addEventListener('keydown', function (e) {
    if (
        e.ctrlKey || e.altKey ||
        e.key === "F12" ||
        e.key === "Tab" ||
        e.key === "Escape"
    ) {
        e.preventDefault();
    }
});

// Auto-submit if user switches tab or minimizes window
document.addEventListener("visibilitychange", function () {
    if (document.hidden) {
        alert("You switched the tab. Exam will be submitted!");
        document.getElementById("examForm").submit();
    }
});

// Block window blur
window.onblur = function () {
    alert("You left the exam window. Exam will be submitted!");
    document.getElementById("examForm").submit();
};
</script>

</head>

<body onload="startTimer()">

<div class="container">
    <h2>Online Examination</h2>
    <h3>⏱ Time Left: <span id="timer"></span></h3>

    <form method="post" action="submit_exam.php" id="examForm">
        <?php while ($row = pg_fetch_assoc($result)) { ?>
            <div class="question-block">
                <p><?= $row['question'] ?></p>

                <label>
                    <input type="radio" name="ans[<?= $row['id'] ?>]" value="1">
                    <?= $row['option1'] ?>
                </label>

                <label>
                    <input type="radio" name="ans[<?= $row['id'] ?>]" value="2">
                    <?= $row['option2'] ?>
                </label>

                <label>
                    <input type="radio" name="ans[<?= $row['id'] ?>]" value="3">
                    <?= $row['option3'] ?>
                </label>

                <label>
                    <input type="radio" name="ans[<?= $row['id'] ?>]" value="4">
                    <?= $row['option4'] ?>
                </label>
            </div>
        <?php } ?>

        <button type="submit">Submit Exam</button>
    </form>
</div>

</body>
</html>
