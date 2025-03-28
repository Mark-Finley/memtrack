<?php if (isset($_SESSION['success_message'])): ?>
    <p style="color: green;"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <p style="color: red;"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
<?php endif; ?>

<?php
session_start();
include 'config.php';
include 'layout.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all memos with sender & recipient info
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build SQL query with dynamic filters
$sql = "SELECT memos.id, memos.subject, memos.file_path, memos.status, memos.created_at, 
        memos.sent_by, memos.received_by, memos.directorate_from, memos.directorate_to, memos.date
        FROM memos 
        WHERE 1=1";  // Base condition to simplify adding dynamic filters

// Filter by status
if ($status_filter) {
    $sql .= " AND memos.status = ?";
}

// Filter by date range
if ($start_date && $end_date) {
    $sql .= " AND DATE(memos.created_at) BETWEEN ? AND ?";
} elseif ($start_date) {
    $sql .= " AND DATE(memos.created_at) >= ?";
} elseif ($end_date) {
    $sql .= " AND DATE(memos.created_at) <= ?";
}

$stmt = $conn->prepare($sql);

// Bind parameters dynamically based on filters
if ($status_filter && $start_date && $end_date) {
    $stmt->bind_param("ssss", $status_filter, $start_date, $end_date);
} elseif ($status_filter && $start_date) {
    $stmt->bind_param("sss", $status_filter, $start_date);
} elseif ($status_filter && $end_date) {
    $stmt->bind_param("sss", $status_filter, $end_date);
} elseif ($start_date && $end_date) {
    $stmt->bind_param("ss", $start_date, $end_date);
} elseif ($start_date) {
    $stmt->bind_param("s", $start_date);
} elseif ($end_date) {
    $stmt->bind_param("s", $end_date);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Include CSS -->
    <style>
        /* Add your existing styles here */
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
        .filter select, .filter button, .filter input[type="date"] {
            padding: 8px 12px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            color: #333;
            background-color: #fff;
            transition: background-color 0.3s ease;
        }
        .filter select:focus, .filter button:focus, .filter input[type="date"]:focus {
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

        <!-- Filter by Status and Date -->
        <div class="filter">
            <form method="GET">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status">
                    <option value="">All</option>
                    <option value="sent" <?= ($status_filter == 'sent') ? 'selected' : ''; ?>>Sent</option>
                    <option value="read" <?= ($status_filter == 'read') ? 'selected' : ''; ?>>Read</option>
                    <option value="archived" <?= ($status_filter == 'archived') ? 'selected' : ''; ?>>Archived</option>
                </select>

                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($start_date); ?>">

                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($end_date); ?>">

                <button type="submit">Filter</button>
            </form>
        </div>

        <!-- Memo List Table -->
        <table>
            <tr>
                <th>Sent By</th>
                <th>Received By</th>
                <th>Subject</th>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>View Memo</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['sent_by']); ?></td>
                        <td><?= htmlspecialchars($row['received_by']); ?></td>
                        <td><?= htmlspecialchars($row['subject']); ?></td>
                        <td><?= date("F j, Y", strtotime($row['date'])); ?></td>
                        <td><?= htmlspecialchars($row['directorate_from']); ?></td>
                        <td><?= htmlspecialchars($row['directorate_to']); ?></td>

                        <td>
                            <?php if (!empty($row['file_path'])):?>
                                <a href="<?= htmlspecialchars($row['file_path']);?>" target="_blank" >📄</a>
                            <?php else: ?>
                                No file
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status <?= $row['status']; ?>">
                                <?= ucfirst($row['status']); ?>
                            </span>
                        </td>
                        
                        <td>
                            <!-- Action Buttons -->
                            <div class="action-btns">
                                <!-- Change Status Form #comment later
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
                                </form> -->

                                <!-- Delete Memo (Only for Sender) -->
                                <?php if ($_SESSION['role'] =='admin' || $_SESSION['user_id'] == $row['sender_id']): ?>
                                    <form method="POST" action="del_memo.php" onsubmit="return confirm('Are you sure you want to delete this memo?');">
                                        <input type="hidden" name="memo_id" value="<?= $row['id']; ?>">
                                        <button type="submit" class="delete-btn">Delete</button>
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
        <a href="dashboard.php" class="btn btn-secondary mb3">⬅ Back to Dashboard</a>
    </div>

</body>
</html>
