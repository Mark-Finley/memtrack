<?php
session_start();

// If user is logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo Tracker</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .container {
            width: 50%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 { color: #333; }
        a {
            display: block;
            text-decoration: none;
            margin: 10px;
            padding: 10px;
            background: #007bff;
            color: white;
            border-radius: 5px;
        }
        a:hover { background: #0056b3; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Welcome to Memo Tracker</h1>
        <p>Track, manage, and organize your memos efficiently.</p>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div>

</body>
</html>
