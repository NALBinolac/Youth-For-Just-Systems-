<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$volunteers = [];
$result = mysqli_query($conn, "SELECT * FROM volunteers ORDER BY id DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $volunteers[] = $row;
    }
}

$success_message = isset($_GET['success']) ? $_GET['success'] : '';

function statusBadge($status) {
    switch ($status) {
        case 'Approved':
            return "<span style='background:#d4edda;color:#155724;padding:5px 12px;border-radius:4px;font-weight:600;font-size:0.85rem;'>Approved</span>";
        case 'Rejected':
            return "<span style='background:#f8d7da;color:#721c24;padding:5px 12px;border-radius:4px;font-weight:600;font-size:0.85rem;'>Rejected</span>";
        default:
            return "<span style='background:#fff3cd;color:#856404;padding:5px 12px;border-radius:4px;font-weight:600;font-size:0.85rem;'>Pending</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteer Management | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">
        <h1>Volunteer Management</h1>
        <p>Review applications and update status. Volunteers are automatically notified by email when status changes.</p>

        <?php if ($success_message): ?>
            <div class="alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Activities / Affiliation</th>
                        <th>Status</th>
                        <th>Date Applied</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($volunteers)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #888;">No volunteer applications yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($volunteers as $v): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($v['fullname']) ?></strong><br>
                                <span style="font-size: 0.8rem; color: #666;"><?= htmlspecialchars($v['email']) ?></span>
                            </td>
                            <td>
                                <?= htmlspecialchars($v['activities'] ?? '') ?><br>
                                <span style="font-size: 0.8rem; color: #888;"><?= htmlspecialchars($v['affiliation'] ?? '') ?></span>
                            </td>
                            <td>
                                <?= statusBadge($v['status']) ?>
                                <?php if ($v['status'] === 'Rejected' && !empty($v['rejection_reason'])): ?>
                                    <div class="reason-note">Reason: <?= htmlspecialchars($v['rejection_reason']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars(date('M d, Y', strtotime($v['created_at']))) ?></td>
                            <td>
                                <!-- Action Buttons now trigger Javascript functions -->
                                <div class="action-btns">
                                    <button type="button" class="btn btn-approve" 
                                            onclick="openApproveModal(<?= $v['id'] ?>, '<?= htmlspecialchars(addslashes($v['fullname'])) ?>')">
                                        Approve
                                    </button>

                                    <button type="button" class="btn btn-reject" 
                                            onclick="openRejectModal(<?= $v['id'] ?>, '<?= htmlspecialchars(addslashes($v['fullname'])) ?>')">
                                        Reject
                                    </button>

                                    <button type="button" class="btn btn-delete" 
                                            onclick="openDeleteModal(<?= $v['id'] ?>, '<?= htmlspecialchars(addslashes($v['fullname'])) ?>')">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 1. Approve Modal -->
    <div id="approveModal" class="modal">
        <div class="modal-content" style="text-align: center;">
            <h2 style="color: #2e7d32; margin-bottom: 15px;">Confirm Approval</h2>
            <p style="margin-bottom: 25px; color: #555;">Are you sure you want to approve <strong id="approveName"></strong>? An automated email will be sent to them.</p>
            
            <div style="display: flex; justify-content: center; gap: 15px;">
                <button type="button" class="btn btn-back" onclick="closeModal('approveModal')">Cancel</button>
                <a id="approveLink" href="#" class="btn btn-approve">Yes, Approve</a>
            </div>
        </div>
    </div>

    <!-- 2. Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <h2 style="color: #c62828; margin-bottom: 15px; text-align: center;">Reject Application</h2>
            <p style="margin-bottom: 15px; color: #555; text-align: center;">Provide a reason for rejecting <strong id="rejectName"></strong>. This will be sent to their email.</p>
            
            <!-- Forms perfectly handle passing the variables to your backend -->
            <form action="process-volunteer-status.php" method="GET" style="display: flex; flex-direction: column; gap: 15px;">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="id" id="rejectId" value="">
                
                <textarea name="reason" id="rejectReason" placeholder="Enter rejection reason here..." required style="width: 100%; min-height: 80px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit;"></textarea>
                
                <div style="display: flex; justify-content: center; gap: 15px; margin-top: 10px;">
                    <button type="button" class="btn btn-back" onclick="closeModal('rejectModal')">Cancel</button>
                    <button type="submit" class="btn btn-reject">Submit Rejection</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content" style="text-align: center;">
            <h2 style="color: #6c757d; margin-bottom: 15px;">Confirm Deletion</h2>
            <p style="margin-bottom: 25px; color: #555;">Are you sure you want to permanently delete <strong id="deleteName"></strong>'s record? This cannot be undone.</p>
            
            <div style="display: flex; justify-content: center; gap: 15px;">
                <button type="button" class="btn btn-back" onclick="closeModal('deleteModal')">Cancel</button>
                <a id="deleteLink" href="#" class="btn btn-delete">Yes, Delete</a>
            </div>
        </div>
    </div>

    <!-- Modal Control Logic -->
    <script>
        function openApproveModal(id, name) {
            document.getElementById('approveName').textContent = name;
            // Injects the exact ID into the link's URL before showing the pop-up
            document.getElementById('approveLink').href = "process-volunteer-status.php?action=approve&id=" + id;
            document.getElementById('approveModal').style.display = 'flex';
        }

        function openRejectModal(id, name) {
            document.getElementById('rejectName').textContent = name;
            // Injects the exact ID into the hidden form field
            document.getElementById('rejectId').value = id;
            document.getElementById('rejectReason').value = ''; // Clears old text
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function openDeleteModal(id, name) {
            document.getElementById('deleteName').textContent = name;
            document.getElementById('deleteLink').href = "process-volunteer-status.php?action=delete&id=" + id;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close any modal if the admin clicks the dark background outside the box
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>

</body>
</html>