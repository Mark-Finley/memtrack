<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username']; // Fetch the logged-in user's name

// Fetch received memos using recipient_name instead of recipient_id
$sql = "SELECT memos.id, memos.subject, memos.file_path 
        FROM memos
        WHERE memos.received_by = ? 
        ORDER BY memos.date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username); // Bind username instead of user_id
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'layout.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" type="image/png" href="favicon.png">
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
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <h1>Admin Dashboard</h1>
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>As an admin, you can archive memos, delete any memo, and manage users.</p>
        <?php else: ?>
            <h1>User Dashboard</h1>
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>You can send, read, and manage your own memos.</p>
        <?php endif; ?>
        
        <div class="buttons">
            <a href="receive_memo.php">📨 Receive Memo</a>
            <a href="send_memo.php">📨 Send Memo</a>
            <a href="view_memos.php">📂 View Memos</a>
            <a href="activity_list.php">📝 Activity List</a>
            <?php if($_SESSION['role'] == 'admin'): ?>
                <a href="admin_panel.php">⚙ Manage Users</a>
                <a href="work_stat.php">📊 Work Stats</a>
            <?php endif; ?>
        </div>

        <h2>📋 Your Received Memos</h2>
        <div class="memos">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="memo">
                        <strong>From: <?php echo htmlspecialchars($row['sender']); ?></strong>
                        <p><b>Subject:</b> <?php echo htmlspecialchars($row['subject']); ?></p>
                        <?php if (!empty($row['file_path'])): ?>
                            <p><a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">📂 View Memo</a></p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No memos received yet.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>

<?php include 'includes/footer.php'; ?>
