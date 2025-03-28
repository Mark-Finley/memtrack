<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sent_by = trim($_POST['sent_by']);
    $recieved_by = trim($_POST['received_by']);
    $subject = trim($_POST['subject']);
    $directorate_from = trim($_POST['directorate_from']);
    $directorate_to = trim($_POST['directorate_to']);
    $date = trim($_POST['date']);

    // Validate input
    if (empty($recieved_by) || empty($subject) || empty($directorate_from) || empty($directorate_to) || empty($date)) {
        echo "<div class='message error'>Error: All fields are required.</div>";
        exit();
    }

    // File Upload Handling
    $upload_dir = "uploads/";
    $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
    $file_name = $_FILES['memo_file']['name'];
    $file_tmp = $_FILES['memo_file']['tmp_name'];
    $file_size = $_FILES['memo_file']['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_path = $upload_dir . uniqid() . "_" . basename($file_name);

    if (!in_array($file_ext, $allowed_types)) {
        echo "<div class='message error'>Error: Only JPG, PNG, and PDF files are allowed.</div>";
        exit();
    } elseif ($file_size > 5 * 1024 * 1024) { // 5MB max
        echo "<div class='message error'>Error: File size must be below 5MB.</div>";
        exit();
    } elseif (move_uploaded_file($file_tmp, $file_path)) {
        // Insert memo with recipient name instead of ID
        $sql = "INSERT INTO memos (sent_by, received_by, subject, directorate_from, directorate_to, date, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $sent_by, $recieved_by, $subject, $directorate_from, $directorate_to, $date, $file_path);

        if ($stmt->execute()) {
            echo "<div class='message success'>Memo uploaded and sent successfully.</div>";
        } else {
            echo "<div class='message error'>Error: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='message error'>Error: Failed to upload the file.</div>";
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
        input, button {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            width: 100%;
        }
        button {
            background-color: #007bff;
            color: white;
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
    </style>
</head>
<body>

<div class="container">
    <h1>Send Memo</h1>
    
    <form method="post" enctype="multipart/form-data">
        <label for="sent_by">Sent By :</label>
        <input type="text" name="sent_by" id="sent_by" required>
        
        <label for="received_by">Received By:</label>
        <input type="text" name="received_by" id="received_by" required>

        <label for="subject">Subject:</label>
        <input type="text" name="subject" id="subject" required>

        <label for="date">Date:</label>
        <input type="date" name="date" id="date" required>

        <label for="directorate_from">From:</label>
        <input type="text" name="directorate_from" id="directorate_from" required>

        <label for="directorate_to">To:</label>
        <input type="text" name="directorate_to" id="directorate_to" required>
        
        

        
        <label for="memo_file">Upload Memo (JPG, PNG, PDF):</label>
        <input type="file" name="memo_file" id="memo_file" required>
        
        <button type="submit">Upload & Send Memo</button>
    </form>

    <!-- Back to Dashboard Link -->
    <a href="dashboard.php" class="back-button">⬅ Back to Dashboard</a>
</div>

</body>
</html>
