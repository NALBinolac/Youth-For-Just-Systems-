<?php
session_start();
require_once '../config.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch aggregate statistics with safe error containment
$user_count = 0;
$volunteer_count = 0;
$event_count = 0;
$module_count = 0;

try {
    $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
    if($res) $user_count = mysqli_fetch_assoc($res)['total'];

    $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM volunteers");
    if($res) $volunteer_count = mysqli_fetch_assoc($res)['total'];

    $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM events");
    if($res) $event_count = mysqli_fetch_assoc($res)['total'];

    $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM modules");
    if($res) $module_count = mysqli_fetch_assoc($res)['total'];
} catch (mysqli_sql_exception $e) {
    // Suppress missing tables until created in phpMyAdmin
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">
        <div class="welcome-box">
            <h2>Welcome Back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>!</h2>
            <p style="color: rgba(255,255,255,0.8); margin-bottom: 0; margin-top: 5px;">Use this control console to update landing modules, track metrics, and manage user sessions.</p>
        </div>

        <h1>Platform Overview</h1>
        <p>Real-time analytics status across the organization workspace.</p>

        <div class="metrics-grid">
            <div class="card">
                <h3>Total Accounts</h3>
                <div class="value"><?= $user_count ?></div>
            </div>
            <div class="card">
                <h3>Volunteers</h3>
                <div class="value"><?= $volunteer_count ?></div>
            </div>
            <div class="card">
                <h3>Scheduled Events</h3>
                <div class="value"><?= $event_count ?></div>
            </div>
            <div class="card">
                <h3>Course Modules</h3>
                <div class="value"><?= $module_count ?></div>
            </div>
        </div>
    </div>

</body>
</html>