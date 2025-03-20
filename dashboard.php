<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch received memos
$sql = "SELECT memos.id, memos.subject, memos.message, users.username AS sender 
        FROM memos 
        JOIN users ON memos.sender_id = users.id 
        WHERE memos.recipient_id = ? 
        ORDER BY memos.created_at DESC";

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
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            width: 60%;
            margin: auto;
            background: white;
            padding: 20px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1 { color: #333; }
        .memos {
            margin-top: 20px;
        }
        .memo {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .memo:last-child { border-bottom: none; }
        .memo strong { color: #007bff; }
        .buttons a {
            display: inline-block;
            margin: 5px;
            padding: 10px;
            text-decoration: none;
            color: white;
            background: #007bff;
            border-radius: 5px;
        }
        .buttons a:hover { background: #0056b3; }
    </style>
</head>
<body>

    <div class="container">
        <!-- <p>Manage your memos easily from this dashboard.</p> -->
        

        <?php if ($_SESSION['role'] == 'admin'): ?>
            <h1>Admin Panel</h1>
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>As an admin, you can archive memos, delete any memo, and manage users.</p>
        <?php else: ?>
            <h1>User Dashboard</h1>
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>You can send, read, and manage your own memos.</p>
        <?php endif; ?>
        

        <div class="buttons">
            <a href="send_memo.php">📨 Receive Memo</a>
            <a href="view_memos.php">📂 View Memos</a>
            <a href="activity_list.php">📝 Activity List</a>
            <?php if($_SESSION['role'] == 'admin'): ?>
                <a href="admin_panel.php">⚙ Manage Users</a>
            <?php endif; ?>
            <a href="logout.php" style="background: red;">🚪 Logout</a>
        </div>

        <h2>📋 Your Received Memos</h2>
        <div class="memos">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="memo">
                        <strong>From: <?php echo htmlspecialchars($row['sender']); ?></strong>
                        <p><b>Subject:</b> <?php echo htmlspecialchars($row['subject']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No memos received yet.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
