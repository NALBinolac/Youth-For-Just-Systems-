<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fallback empty tracker data array if specific tables haven't been compiled yet
$donations = [];
try {
    $result = mysqli_query($conn, "SELECT * FROM donations ORDER BY id DESC");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $donations[] = $row;
        }
    }
} catch (mysqli_sql_exception $e) { }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donation Management | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">
        <h1>Donation &amp; Support Management</h1>
        <p>Review organization partnerships, advisory group applications, shop transaction logs, and gift pledges.</p>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Contributor Reference</th>
                        <th>Channel / Form Type</th>
                        <th>Pledge Details / Value</th>
                        <th>Proof of Payment</th> <th>Submission Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($donations)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #888;">No donor entries or support pipeline transmissions found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($donations as $donation): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($donation['donor_name'] ?? 'Anonymous') ?></strong><br>
                                <span style="font-size: 0.8rem; color: #666;"><?= htmlspecialchars($donation['email'] ?? '') ?></span>
                            </td>
                            
                            <td>
                                <?= htmlspecialchars($donation['payment_channel'] ?? 'General Fund Pledge') ?><br>
                                <span style="font-size: 0.8rem; color: #888;"><?= htmlspecialchars($donation['purpose'] ?? '') ?></span>
                            </td>
                            
                            <td><strong>PHP <?= htmlspecialchars(number_format($donation['amount'] ?? 0, 2)) ?></strong></td>
                            
                            <td>
                                <?php if (!empty($donation['proof_path'])): ?>
                                    <a href="../<?= htmlspecialchars($donation['proof_path']) ?>" target="_blank" class="btn-receipt">View Receipt</a>
                                <?php else: ?>
                                    <span class="no-image">No Image</span>
                                <?php endif; ?>
                            </td>
                            
                            <td><?= htmlspecialchars($donation['created_at'] ?? 'N/A') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>