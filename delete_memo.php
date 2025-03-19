<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validate request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['memo_id'])) {
    $memo_id = $_POST['memo_id'];

    // Ensure the logged-in user is the sender
    $sql = "DELETE FROM memos WHERE id = ? AND sender_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $memo_id, $_SESSION['user_id']);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Memo deleted successfully!";
    } else {
        $_SESSION['error_message'] = "You are not allowed to delete this memo.";
    }

    header("Location: activity_list.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: activity_list.php");
    exit();
}
?>
<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validate request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['memo_id'])) {
    $memo_id = $_POST['memo_id'];

    // Ensure the logged-in user is the sender
    $sql = "DELETE FROM memos WHERE id = ? AND sender_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $memo_id, $_SESSION['user_id']);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Memo deleted successfully!";
    } else {
        $_SESSION['error_message'] = "You are not allowed to delete this memo.";
    }

    header("Location: activity_list.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: activity_list.php");
    exit();
}
?>
