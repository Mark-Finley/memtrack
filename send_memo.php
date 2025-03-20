<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_SESSION['user_id'];
    $recipient_id = $_POST['recipient_id'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $sql = "INSERT INTO memos (sender_id, recipient_id, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $sender_id, $recipient_id, $subject, $message);
    
    if ($stmt->execute()) {
        echo "<div class='message success'>Memo sent successfully.</div>";
    } else {
        echo "<div class='message error'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Memo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            width: 80%;
            max-width: 600px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            box-sizing: border-box;
        }
        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 30px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input, textarea {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            width: 100%;
        }
        input[type="number"] {
            width: 48%;
            display: inline-block;
        }
        button {
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
        .message.success {
            background-color: #28a745;
            color: white;
        }
        .message.error {
            background-color: #dc3545;
            color: white;
        }
        .back-button {
            display: block;
            margin-top: 20px;
            text-align: center;
            font-size: 16px;
            color: #007bff;
            text-decoration: none;
        }
        .back-button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Receive Memo</h1>
    
    <form method="post">
        <label for="recipient_id">Recipient ID:</label>
        <input type="number" name="recipient_id" id="recipient_id" required>
        
        <label for="subject">Subject:</label>
        <input type="text" name="subject" id="subject" required>
        
        <label for="message">Message:</label>
        <textarea name="message" id="message" rows="5" required></textarea>
        
        <button type="submit">Submit</button>
    </form>

    <!-- Back to Dashboard Link -->
    <a href="dashboard.php" class="back-button">⬅ Back to Dashboard</a>
</div>

</body>
</html>
