<?php
include "../config.php";

if (isset($_POST['add'])) {
    pg_query($conn, "INSERT INTO questions
    (question, option1, option2, option3, option4, correct_option)
    VALUES(
    '$_POST[q]', '$_POST[o1]', '$_POST[o2]',
    '$_POST[o3]', '$_POST[o4]', $_POST[co])");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Question</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background: #ffffff;
            padding: 25px;
            width: 420px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            margin-bottom: 4px;
        }

        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="number"] {
            width: 100%;
        }

        button {
            width: 100%;
            margin-top: 15px;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #218838;
        }
    </style>
</head>

<body>

<form method="post">
    <h2>Add New Question</h2>

    <label>Question</label>
    <input type="text" name="q" required>

    <label>Option 1</label>
    <input type="text" name="o1" required>

    <label>Option 2</label>
    <input type="text" name="o2" required>

    <label>Option 3</label>
    <input type="text" name="o3" required>

    <label>Option 4</label>
    <input type="text" name="o4" required>

    <label>Correct Option (1–4)</label>
    <input type="number" name="co" min="1" max="4" required>

    <button name="add">Add Question</button>
</form>

</body>
</html>