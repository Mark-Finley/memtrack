<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM memos WHERE recipient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<b>" . $row['subject'] . "</b>: " . $row['message'] . "<br>";
}
?>
