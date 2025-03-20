<?php
$host = "localhost";
$dbname = "memo_tracker";
$username = "root"; // Change if using a different MySQL user
$password = ""; // Change if you have set a password

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
