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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-5">
        <h1 class="text-primary">Admin Panel: User Management</h1>
        <a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Back to Dashboard</a>

        <!-- Button to Open Add User Modal -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">➕ Add New User</button>

        <table class="table table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= htmlspecialchars($row['role']); ?></td>
                        <td>
                            <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                <div class="d-flex gap-2">
                                    <!-- Role Update -->
                                    <form method="POST" action="update_role.php">
                                        <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                                        <select name="role" class="form-select d-inline w-auto">
                                            <option value="user" <?= ($row['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                            <option value="admin" <?= ($row['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Update Role</button>
                                    </form>

                                    <!-- Change Password Button -->
                                    <button class="btn btn-warning btn-sm change-password-btn" data-bs-toggle="modal" 
                                        data-bs-target="#changePasswordModal" data-userid="<?= $row['id']; ?>" 
                                        data-username="<?= htmlspecialchars($row['username']); ?>">🔑 Change Password</button>

                                    <!-- Delete User -->
                                    <form method="POST" action="delete_user.php" onsubmit="return confirm('Are you sure?');">
                                        <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">🗑 Delete</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                (You)
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password for <span id="modalUsername"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <input type="hidden" name="user_id" id="modalUserId">
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            // Handle opening the Change Password modal
            $(".change-password-btn").click(function () {
                var userId = $(this).data("userid");
                var username = $(this).data("username");
                $("#modalUserId").val(userId);
                $("#modalUsername").text(username);
            });

            // Handle Change Password form submission
            $("#changePasswordForm").submit(function (e) {
                e.preventDefault();

                $.ajax({
                    url: "change_password.php",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function (response) {
                        alert(response);
                        $("#changePasswordModal").modal("hide");
                    }
                });
            });
        });
    </script>

</body>
</html>
