<?php
session_start();
require_once '../config.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$members = [];
$result = mysqli_query($conn, "SELECT * FROM memberships ORDER BY id DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $members[] = $row;
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
    <title>Membership Management | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">
        <h1>Membership Management</h1>
        <p>Review official membership applications and update their status.</p>

        <?php if ($success_message): ?>
            <div class="alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Applicant</th>
                        <th>Status</th>
                        <th>Date Applied</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #888;">No membership applications yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['id'] ?? '') ?></td>
                            
                            <td>
                                <strong><?= htmlspecialchars($m['fullname'] ?? '') ?></strong><br>
                                <span style="font-size: 0.8rem; color: #666;"><?= htmlspecialchars($m['email'] ?? '') ?></span>
                            </td>
                            
                            <td>
                                <?php 
                                    $currentStatus = $m['status'] ?? 'Pending';
                                    echo statusBadge($currentStatus); 
                                ?>
                                <?php if ($currentStatus === 'Rejected' && !empty($m['rejection_reason'])): ?>
                                    <div class="reason-note">Reason: <?= htmlspecialchars($m['rejection_reason']) ?></div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= isset($m['created_at']) ? htmlspecialchars(date('M d, Y', strtotime($m['created_at']))) : 'N/A' ?>
                            </td>
                            
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="btn btn-approve"
                                            onclick="openApproveModal(<?= htmlspecialchars($m['id'] ?? 0) ?>, '<?= htmlspecialchars(addslashes($m['fullname'] ?? '')) ?>')">
                                        Approve
                                    </button>

                                    <button type="button" class="btn btn-reject"
                                            onclick="openRejectModal(<?= htmlspecialchars($m['id'] ?? 0) ?>, '<?= htmlspecialchars(addslashes($m['fullname'] ?? '')) ?>')">
                                        Reject
                                    </button>

                                    <button type="button" class="btn btn-delete"
                                            onclick="openDeleteModal(<?= htmlspecialchars($m['id'] ?? 0) ?>, '<?= htmlspecialchars(addslashes($m['fullname'] ?? '')) ?>')">
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
            <p style="margin-bottom: 25px; color: #555;">Are you sure you want to approve <strong id="approveName"></strong> as a member?</p>
           
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
            <p style="margin-bottom: 15px; color: #555; text-align: center;">Provide a reason for rejecting <strong id="rejectName"></strong>.</p>
           
            <form action="process-member-status.php" method="GET" style="display: flex; flex-direction: column; gap: 15px;">
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
            <p style="margin-bottom: 25px; color: #555;">Are you sure you want to permanently delete <strong id="deleteName"></strong>'s record?</p>
           
            <div style="display: flex; justify-content: center; gap: 15px;">
                <button type="button" class="btn btn-back" onclick="closeModal('deleteModal')">Cancel</button>
                <a id="deleteLink" href="#" class="btn btn-delete">Yes, Delete</a>
            </div>
        </div>
    </div>

    <script>
        function openApproveModal(id, name) {
            document.getElementById('approveName').textContent = name;
            document.getElementById('approveLink').href = "process-member-status.php?action=approve&id=" + id;
            document.getElementById('approveModal').style.display = 'flex';
        }

        function openRejectModal(id, name) {
            document.getElementById('rejectName').textContent = name;
            document.getElementById('rejectId').value = id;
            document.getElementById('rejectReason').value = '';
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function openDeleteModal(id, name) {
            document.getElementById('deleteName').textContent = name;
            document.getElementById('deleteLink').href = "process-member-status.php?action=delete&id=" + id;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>