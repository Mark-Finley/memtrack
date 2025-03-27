<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to delete a memo.";
    header("Location: login.php");
    exit();
}

// Check if the memo_id is provided
if (!isset($_POST['memo_id'])) {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: activity_list.php");
    exit();
}

$memo_id = $_POST['memo_id'];
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; // Assuming 'admin' or 'user'

// If the user is not an admin, check if they are the sender of the memo
if ($user_role !== 'admin') {
    $stmt = $conn->prepare("SELECT sender_id FROM memos WHERE id = ?");
    $stmt->bind_param("i", $memo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $_SESSION['error_message'] = "Memo not found.";
        header("Location: activity_list.php");
        exit();
    }

    $row = $result->fetch_assoc();
    
    if ($row['sender_id'] != $user_id) {
        $_SESSION['error_message'] = "You can only delete memos you sent.";
        header("Location: activity_list.php");
        exit();
    }
}

// Proceed with deletion
$stmt = $conn->prepare("DELETE FROM memos WHERE id = ?");
$stmt->bind_param("i", $memo_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Memo deleted successfully.";
} else {
    $_SESSION['error_message'] = "Error deleting memo.";
}

$stmt->close();
$conn->close();
header("Location: activity_list.php");
exit();
?>
