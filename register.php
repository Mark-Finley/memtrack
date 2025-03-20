<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = isset($_POST['admin']) ? 'admin' : 'user'; // Checkbox for admin role

    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Account created successfully!";
        header("Location: login.php");
    } else {
        $_SESSION['error_message'] = "Registration failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        label {
            font-size: 14px;
            color: #333;
        }
        .checkbox-label {
            font-size: 14px;
            color: #333;
            display: flex;
            align-items: center;
        }
        .checkbox-label input {
            margin-right: 10px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin-top: 20px;
        }
        .message p {
            margin: 0;
            font-size: 16px;
        }
        .message .success {
            color: green;
        }
        .message .error {
            color: red;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Register</h2>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success">
                <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="message error">
                <p><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" required placeholder="Enter username">
            <input type="email" name="email" required placeholder="Enter email">
            <input type="password" name="password" required placeholder="Enter password">

            <div class="checkbox-label">
                <input type="checkbox" name="admin">
                <label for="admin">Register as Admin</label>
            </div>

            <button type="submit">Register</button>
        </form>

        <div class="back-link">
            <a href="login.php">Already have an account? Login here</a>
        </div>
    </div>

</body>
</html>
