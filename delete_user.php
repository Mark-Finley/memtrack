<?php
session_start();
include 'config.php';

// Ensure only admins can delete users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Prevent admins from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error_message'] = "You cannot delete your own account.";
        header("Location: admin_panel.php");
        exit();
    }

    // Delete user from database
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "User deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete user.";
    }

    header("Location: admin_panel.php");
    exit();
}
?>
