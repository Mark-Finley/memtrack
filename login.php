<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Store role in session

        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        .login-container a {
            color: #007BFF;
            text-decoration: none;
        }

        .login-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    
    <!-- Display error message if session has error_message -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); ?> <!-- Clear the error message after displaying it -->
    <?php endif; ?>
    
    <form method="post">
        <input type="text" name="email" placeholder="E-mail" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    
    <!-- <p>Don't have an account? <a href="register.php">Register here</a></p> -->
</div>

</body>
</html>
