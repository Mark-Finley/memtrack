<?php
session_start();
include 'config.php';

// Ensure only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Fetch all users
$sql = "SELECT id, username, email, role FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 90%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        h1 {
            color: #007bff;
            font-size: 2em;
            margin-bottom: 20px;
        }
        a {
            text-decoration: none;
            color: #007bff;
            font-size: 16px;
        }
        a:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
            background-color: #fff;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .actions form {
            display: inline-block;
        }
        select, button {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ccc;
            cursor: pointer;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        button.delete-btn {
            background-color: red;
        }
        button.delete-btn:hover {
            background-color: darkred;
        }
        .role-select {
            margin-right: 10px;
        }
        .container a {
            margin-top: 20px;
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
        }
        .container a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Admin Panel: User Management</h1>
        <a href="dashboard.php">⬅ Back to Dashboard</a>

        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= htmlspecialchars($row['role']); ?></td>
                    <td>
                        <?php if ($row['id'] != $_SESSION['user_id']): ?>
                            <div class="actions">
                                <!-- Role Change Form -->
                                <form method="POST" action="update_role.php">
                                    <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                                    <select name="role" class="role-select">
                                        <option value="user" <?= ($row['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?= ($row['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <button type="submit">Update Role</button>
                                </form>

                                <!-- Delete User Button -->
                                <form method="POST" action="delete_user.php" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                                    <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                                    <button type="submit" class="delete-btn">🗑 Delete</button>
                                </form>
                            </div>
                        <?php else: ?>
                            (You)
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>
