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
        echo "Memo sent successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<form method="post">
    Recipient ID: <input type="number" name="recipient_id" required><br>
    Subject: <input type="text" name="subject" required><br>
    Message: <textarea name="message" required></textarea><br>
    <button type="submit">Send Memo</button>
</form>
