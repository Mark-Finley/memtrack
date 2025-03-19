<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validate the request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['memo_id']) && isset($_POST['status'])) {
    $memo_id = $_POST['memo_id'];
    $status = $_POST['status'];

    // Update memo status in database
    $sql = "UPDATE memos SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $memo_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Memo status updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update memo status.";
    }

    header("Location: activity_list.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: activity_list.php");
    exit();
}
?>
