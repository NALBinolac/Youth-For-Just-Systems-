<?php
session_start();
require_once '../config.php'; 

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all users using MySQLi
$query = "SELECT id, username, fullname, email, role, created_at FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">

    <h1>User Management</h1>
    <p>Manage all registered accounts and their roles.</p>

    

<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
         <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['fullname'] ?: 'Not Provided') ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td style="text-transform: capitalize;"><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <a href="admin-edit-user.php?id=<?= $user['id'] ?>" class="btn-edit">Edit</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center; color: #888; padding: 30px;">No registered users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>