<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = "";
$success = "";

if (!isset($_GET['id'])) {
    header("Location: admin-users.php");
    exit();
}

$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Handle Deletion
    if (isset($_POST['delete_user'])) {
        $delete_query = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin-users.php?msg=deleted");
            exit();
        } else {
            $error = "Failed to delete user.";
        }
    } 
    // 2. Handle Updates
    elseif (isset($_POST['update_user'])) {
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);

        $update_query = "UPDATE users SET fullname = ?, email = ?, role = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "sssi", $fullname, $email, $role, $id);

        if (mysqli_stmt_execute($stmt)) {
            $success = "User account updated successfully!";
        } else {
            $error = "Failed to update user.";
        }
    }
}

$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("User not found in the database.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">
        <h1 style="font-size: 2.2rem; color: #111; margin-bottom: 5px;">Edit User Record</h1>
        <p style="color: #666; margin-bottom: 30px;">Update user details or change workspace access roles.</p>

        <?php if (!empty($success)): ?>
            <div class="alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-group">
            <!-- Update Form -->
            <form action="admin-edit-user.php?id=<?= $id ?>" method="POST">
                
                <label>Username (Cannot be changed)</label>
                <input type="text" value="<?= htmlspecialchars($user['username'] ?? 'No Username') ?>" disabled style="background-color: #e9ecef; cursor: not-allowed;">

                <label>Full Name</label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>

                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label>Account Role</label>
                <select name="role" required>
                    <option value="user" <?= ($user['role'] === 'user') ? 'selected' : '' ?>>Standard User</option>
                    <option value="student" <?= ($user['role'] === 'student') ? 'selected' : '' ?>>Student</option>
                    <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Administrator</option>
                </select>

                <div class="btn-container" style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-top: 20px;">
                    <div style="display: flex; gap: 12px;">
                        <a href="admin-users.php" class="btn btn-back">← Back to Users</a>
                        <button type="submit" name="update_user" class="btn btn-save">Save Changes</button>
                    </div>
            </form>

            <!-- Trigger Button opens the modal instead of submitting -->
            <button type="button" class="btn" style="background-color: #d32f2f; color: white; border: none;" onclick="openDeleteModal()">Delete Account</button>
                </div>
        </div>
    </div>

    <!-- Custom Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content" style="text-align: center;">
            <h2 style="color: #d32f2f; margin-bottom: 15px;">Confirm Deletion</h2>
            <p style="margin-bottom: 25px; color: #555;">Are you sure you want to permanently delete this user? This action cannot be undone.</p>
            
            <!-- The actual deletion form is now safely inside the pop-up -->
            <form action="admin-edit-user.php?id=<?= $id ?>" method="POST" style="display: flex; justify-content: center; gap: 15px;">
                <button type="button" class="btn btn-back" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" name="delete_user" class="btn" style="background-color: #d32f2f; color: white; border: none;">Yes, Delete User</button>
            </form>
        </div>
    </div>

    <!-- Modal Control Logic -->
    <script>
        function openDeleteModal() {
            // Setting this to flex activates your CSS align-items to perfectly center the box
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Automatically close the modal if the admin clicks the dark background outside the box
        window.onclick = function(event) {
            var modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeDeleteModal();
            }
        }
    </script>

</body>
</html>