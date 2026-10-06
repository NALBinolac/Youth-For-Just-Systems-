<?php
session_start();
require_once '../config.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$events = [];
$table_error = false;

// Changed DESC to ASC so events display from nearest date to farthest date!
try {
    $query = "SELECT * FROM events ORDER BY date ASC";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $events[] = $row;
        }
    }
} catch (mysqli_sql_exception $e) {
    $table_error = true; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events Management | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Events Management</h1>
            <a href="admin-add-event.php" class="btn btn-save">+ Add New Event</a>
        </div>

        <?php if ($table_error): ?>
            <div class="alert-warning">
                <strong>Database Table Missing:</strong> The 'events' table was not found in your database. Please run the SQL setup script in phpMyAdmin.
            </div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>Event Title</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr><td colspan="4" style="text-align: center; color: #666;">No upcoming events found.</td></tr>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['title']) ?></td>
                        <td><?= htmlspecialchars($event['date']) ?></td>
                        <td><?= htmlspecialchars($event['venue']) ?></td>
                        <td>
                            <a href="admin-edit-event.php?id=<?= $event['id'] ?>" class="btn-edit">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>