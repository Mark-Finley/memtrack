<?php if (isset($_SESSION['success_message'])): ?>
    <p style="color: green;"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <p style="color: red;"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
<?php endif; ?>

<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all memos with sender & recipient info
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "SELECT memos.id, memos.subject, memos.message, memos.status, memos.created_at, 
        memos.sender_id, sender.username AS sender_name, recipient.username AS recipient_name
        FROM memos 
        JOIN users AS sender ON memos.sender_id = sender.id
        JOIN users AS recipient ON memos.recipient_id = recipient.id";

if ($status_filter) {
    $sql .= " WHERE memos.status = ?";
}

$stmt = $conn->prepare($sql);

if ($status_filter) {
    $stmt->bind_param("s", $status_filter);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity List</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            background: white;
            padding: 20px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1 { color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
        }
        .filter {
            margin-bottom: 20px;
        }
        .filter select, .filter button {
            padding: 8px 12px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            color: #333;
            background-color: #fff;
            transition: background-color 0.3s ease;
        }
        .filter select:focus, .filter button:focus {
            outline: none;
            border-color: #007bff;
        }
        .filter button:hover {
            background-color: #007bff;
            color: white;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
        }
        .sent { background: blue; }
        .read { background: green; }
        .archived { background: gray; }
        .action-btns {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .action-btns button,
        .action-btns select {
            padding: 8px 12px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .action-btns button {
            background-color: #007bff;
            color: white;
        }
        .action-btns button:hover {
            background-color: #0056b3;
        }
        .action-btns button.delete-btn {
            background-color: red;
            color: white;
        }
        .action-btns button.delete-btn:hover {
            background-color: darkred;
        }
        .action-btns select {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .action-btns select:focus {
            border-color: #007bff;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>📊 Memo Activity List</h1>

        <!-- Filter by Status -->
        <div class="filter">
            <form method="GET">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status">
                    <option value="">All</option>
                    <option value="sent" <?= ($status_filter == 'sent') ? 'selected' : ''; ?>>Sent</option>
                    <option value="read" <?= ($status_filter == 'read') ? 'selected' : ''; ?>>Read</option>
                    <option value="archived" <?= ($status_filter == 'archived') ? 'selected' : ''; ?>>Archived</option>
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>

        <!-- Memo List Table -->
        <table>
            <tr>
                <th>Sender</th>
                <th>Recipient</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['sender_name']); ?></td>
                        <td><?= htmlspecialchars($row['recipient_name']); ?></td>
                        <td><?= htmlspecialchars($row['subject']); ?></td>
                        <td><?= nl2br(htmlspecialchars($row['message'])); ?></td>
                        <td>
                            <span class="status <?= $row['status']; ?>">
                                <?= ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?= date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                        <td>
                            <!-- Action Buttons -->
                            <div class="action-btns">
                                <!-- Change Status Form -->
                                <form method="POST" action="update_status.php">
                                    <input type="hidden" name="memo_id" value="<?= $row['id']; ?>">
                                    <select name="status">
                                        <option value="sent" <?= ($row['status'] == 'sent') ? 'selected' : ''; ?>>Sent</option>
                                        <option value="read" <?= ($row['status'] == 'read') ? 'selected' : ''; ?>>Read</option>
                                        <?php if ($_SESSION['role'] == 'admin'): ?>
                                            <option value="archived" <?= ($row['status'] == 'archived') ? 'selected' : ''; ?>>Archived</option>
                                        <?php endif; ?>
                                    </select>
                                    <button type="submit">Update</button>
                                </form>

                                <!-- Delete Memo (Only for Sender) -->
                                <?php if ($_SESSION['role'] =='admin' || $_SESSION['user_id'] == $row['sender_id']): ?>
                                    <form method="POST" action="del_memo.php" onsubmit="return confirm('Are you sure you want to delete this memo?');">
                                        <input type="hidden" name="memo_id" value="<?= $row['id']; ?>">
                                        <button type="submit" class="delete-btn">🗑 Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No memos found.</td></tr>
            <?php endif; ?>
        </table>

        <br>
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </div>

</body>
</html>
