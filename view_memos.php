<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM memos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Memos</title>
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
            max-width: 800px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            box-sizing: border-box;
        }
        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 30px;
        }
        .memo {
            background-color: #ffffff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .memo:hover {
            transform: scale(1.02);
        }
        .subject {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        .message {
            font-size: 16px;
            color: #555;
            margin-top: 5px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
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
    <h1>Your Memos</h1>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="memo">
            <div class="subject"><?= htmlspecialchars($row['subject']); ?></div>
            <div class="message"><?= nl2br(htmlspecialchars($row['message'])); ?></div>
        </div>
    <?php endwhile; ?>

    <div class="back-link">
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </div>
</div>

</body>
</html>
