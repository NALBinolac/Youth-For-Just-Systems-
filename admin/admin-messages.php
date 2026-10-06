<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inquiries & Messages | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

 <?php include '../includes/admin-navbar.php'; ?>

<div class="main-content">
    <h1 style="margin-bottom: 10px;">Inquiries & Messages</h1>
    <p style="color: #666; margin-bottom: 30px;">Tingnan ang mga mensahe mula sa contact form ng website.</p>

    <div class="panel-box">
        <table class="messages-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Sender Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject'] ?? 'Walang Subject'); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                            <td>
                                <button class="btn-view" onclick="openMessageModal(
                                    <?= (int)$row['id'] ?>,
                                    '<?= htmlspecialchars(addslashes($row['name'])) ?>',
                                    '<?= htmlspecialchars(addslashes($row['email'])) ?>',
                                    '<?= htmlspecialchars(addslashes($row['subject'] ?? 'Walang Subject')) ?>',
                                    '<?= htmlspecialchars(addslashes(nl2br($row['message']))) ?>'
                                )">View</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">Walang mensaheng natanggap.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Message View Modal -->
<div class="modal" id="messageModal">
    <div class="modal-content">
        <h2 id="modal-subject" style="color:#2e7d32; margin-bottom: 5px;"></h2>
        <p style="color:#666; font-size:0.9rem; margin-bottom: 15px;">
            From: <strong id="modal-name"></strong> (<span id="modal-email"></span>)
        </p>
        <div class="message-body" id="modal-message"></div>

        <div style="margin-top: 20px; display:flex; gap:10px; justify-content:flex-end;">
            <button type="button" onclick="closeMessageModal()" style="background:#6c757d; color:white; border:none; padding:10px 18px; border-radius:4px; cursor:pointer;">Close</button>
            <button type="button" id="modal-mark-read-btn" onclick="markAsRead()" style="background:#2e7d32; color:white; border:none; padding:10px 18px; border-radius:4px; cursor:pointer; font-weight:600;">Mark as Read</button>
        </div>
    </div>
</div>

<script>
let currentMessageId = null;

function openMessageModal(id, name, email, subject, message) {
    currentMessageId = id;
    document.getElementById('modal-subject').innerText = subject;
    document.getElementById('modal-name').innerText = name;
    document.getElementById('modal-email').innerText = email;
    document.getElementById('modal-message').innerHTML = message;
    document.getElementById('messageModal').style.display = 'flex';
}

function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
}

function markAsRead() {
    if (!currentMessageId) return;

    fetch('../api/update-message-status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(currentMessageId) + '&status=read'
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert('Failed to update status: ' + data.message);
        }
    })
    .catch(err => alert('Error: ' + err));
}

// Close modal when clicking outside the box
document.getElementById('messageModal').addEventListener('click', function(e) {
    if (e.target === this) closeMessageModal();
});
</script>

</body>
</html>