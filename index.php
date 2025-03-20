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
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }

        .logo-container {
            margin-top: 50px;
            margin-bottom: 30px;
        }

        .logo-container img {
            max-width: 200px; /* Adjust the logo size */
            height: auto;
            transition: transform 0.3s ease-in-out;
        }

        .logo-container img:hover {
            transform: scale(1.1); /* Hover effect */
        }

        .container {
            width: 60%;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 32px;
        }

        p {
            color: #666;
            margin-bottom: 20px;
            font-size: 18px;
        }

        a {
            display: inline-block;
            text-decoration: none;
            margin: 15px;
            padding: 15px 30px;
            background: #007bff;
            color: white;
            font-size: 18px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <!-- Logo Section -->
    <div class="logo-container">
        <img src="Kath-logo.png" alt="Memo Tracker Logo"> 
    </div>

    <!-- Content Section -->
    <div class="container">
        <h1>Welcome to Memo Tracker</h1>
        <p>Track, manage, and organize your memos efficiently.</p>
        <a href="login.php">Login</a>
        <a href="#">Register</a>
    </div>

</body>
</html>
