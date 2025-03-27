<?php
include "config.php";

// Handle adding work entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_work"])) {
    $worker_name = $_POST["worker_name"];
    $issue = $_POST["issue"];
    $department = $_POST["department"];

    $query = "INSERT INTO work_status (worker_name, issue_reported, department) VALUES ('$worker_name', '$issue', '$department')";
    $conn->query($query);
}

// Handle updating status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_status"])) {
    $id = $_POST["work_id"];
    $status = $_POST["status"];
    $remarks = $_POST["remarks"];
    
    // Correct NULL handling
    $date_resolved = ($status == "Completed") ? "'" . date("Y-m-d H:i:s") . "'" : "NULL";

    // Properly formatted SQL query
    $query = "UPDATE work_status SET status='$status', remarks='$remarks', date_resolved=$date_resolved WHERE id=$id";
    $conn->query($query);
}


// Fetch work data
$result = $conn->query("SELECT * FROM work_status ORDER BY date_reported DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Work Status Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

    <h2 class="mb-3">Work Status Tracker</h2>

    <!-- Add Work Form -->
    <form method="post" class="mb-3 d-flex gap-2">
        <input type="text" name="worker_name" placeholder="Worker Name" class="form-control" required>
        <input type="text" name="department" placeholder="Department" class="form-control" required>
        <input type="text" name="issue" placeholder="Issue Reported" class="form-control" required>
        <button type="submit" name="add_work" class="btn btn-primary">Add Work</button>
    </form>

    <!-- Work Status Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Worker</th>
                <th>Issue</th>
                <th>Department</th>
                <th>Status</th>
                <th>Date Reported</th>
                <th>Date Resolved</th>
                <th>Remarks</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['worker_name'] ?></td>
                    <td><?= $row['issue_reported'] ?></td>
                    <td><?= $row['department'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['date_reported'] ?></td>
                    <td><?= $row['date_resolved'] ?? 'N/A' ?></td>
                    <td><?= $row['remarks'] ?></td>
                    <td>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="work_id" value="<?= $row['id'] ?>">
                            <select name="status" class="form-select">
                                <option value="Pending" <?= ($row['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="In Progress" <?= ($row['status'] == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                <option value="Completed" <?= ($row['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                            </select>
                            <input type="text" name="remarks" placeholder="Remarks (Optional)" class="form-control">
                            <button type="submit" name="update_status" class="btn btn-warning">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
