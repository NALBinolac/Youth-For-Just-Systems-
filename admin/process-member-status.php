<?php
/**
 * Process Membership Status Updates with Email Notifications
 */

session_start();
require_once '../config.php';
require_once '../includes/email-helper.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$success_message = "Action completed.";
$email_sent = false;

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    // Note: $admin_notes is collected here if you plan to use it later, 
    // but the member email function doesn't require it.
    $admin_notes = isset($_GET['notes']) ? $_GET['notes'] : ''; 
    $rejection_reason = isset($_GET['reason']) ? $_GET['reason'] : '';

    // Fetch current member information (targeting memberships table)
    $query = "SELECT fullname, email, status FROM memberships WHERE id = ?";    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $member = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$member) {
        die("Membership application not found.");
    }

    $previous_status = $member['status'];
    $member_email = $member['email'];
    $member_name = $member['fullname'];

    if ($action === 'approve') {
        $new_status = 'Approved';
        
        $query = "UPDATE memberships SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $id);
        $update_success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($update_success) {
            // Calls the new member-specific email function
            $email_sent = sendMemberStatusEmail($member_email, $member_name, 'Approved');
        }

        $success_message = "Member approved successfully!" . ($email_sent ? " Email notification sent." : " Email notification failed.");

    } elseif ($action === 'reject') {
        $new_status = 'Rejected';
        
        $query = "UPDATE memberships SET status = ?, rejection_reason = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $new_status, $rejection_reason, $id);
        $update_success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($update_success) {
            // Calls the new member-specific email function with the reason
            $email_sent = sendMemberStatusEmail($member_email, $member_name, 'Rejected', $rejection_reason);
        }

        $success_message = "Membership rejected." . ($email_sent ? " Email notification sent." : " Email notification failed.");

    } elseif ($action === 'delete') {
        $stmt = mysqli_prepare($conn, "DELETE FROM memberships WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $success_message = "Membership record deleted.";
    }
}

// Redirect back to the admin members page
header("Location: admin-members.php?success=" . urlencode($success_message));
exit();
?>