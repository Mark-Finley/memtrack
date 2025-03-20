<?php
session_start();
include 'config.php';

// Ensure only admins can change roles
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // Prevent non-admins from upgrading themselves
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error_message'] = "You cannot change your own role.";
        header("Location: admin_panel.php");
        exit();
    }

    // Update user role in database
    $sql = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_role, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User role updated successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update role.";
    }

    header("Location: admin_panel.php");
    exit();
}
?>
