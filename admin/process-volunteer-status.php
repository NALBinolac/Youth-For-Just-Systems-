<?php
/**
 * Process Volunteer Status Updates with Email Notifications
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
    $admin_notes = isset($_GET['notes']) ? $_GET['notes'] : '';
    $rejection_reason = isset($_GET['reason']) ? $_GET['reason'] : '';

    // Fetch current volunteer information
    $query = "SELECT fullname, email, status FROM volunteers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $volunteer = $stmt->fetch();

    if (!$volunteer) {
        die("Volunteer not found.");
    }

    $previous_status = $volunteer['status'];
    $volunteer_email = $volunteer['email'];
    $volunteer_name = $volunteer['fullname'];

    if ($action === 'approve') {
        $new_status = 'Approved';
        $query = "UPDATE volunteers SET status = ?, status_updated_at = NOW(), status_notified = 0 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $update_success = $stmt->execute([$new_status, $id]);

        if ($update_success) {
            $email_sent = sendVolunteerStatusEmail($volunteer_email, $volunteer_name, 'Approved', '', $admin_notes);
            
            // Uncomment the line below ONLY IF you have created this function in your email-helper.php
            // logVolunteerStatusChange($conn, $id, $previous_status, $new_status, $email_sent, $admin_notes);

            if ($email_sent) {
                $update_stmt = $conn->prepare("UPDATE volunteers SET status_notified = 1 WHERE id = ?");
                $update_stmt->execute([$id]);
            }
        }

        $success_message = "Volunteer approved successfully!" . ($email_sent ? " Email notification sent." : " Email notification failed.");

    } elseif ($action === 'reject') {
        $new_status = 'Rejected';
        $query = "UPDATE volunteers SET status = ?, rejection_reason = ?, status_updated_at = NOW(), status_notified = 0 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $update_success = $stmt->execute([$new_status, $rejection_reason, $id]);

        if ($update_success) {
            $email_sent = sendVolunteerStatusEmail($volunteer_email, $volunteer_name, 'Rejected', $rejection_reason, $admin_notes);
            
            // Uncomment the line below ONLY IF you have created this function in your email-helper.php
            // logVolunteerStatusChange($conn, $id, $previous_status, $new_status, $email_sent, $admin_notes);

            if ($email_sent) {
                $update_stmt = $conn->prepare("UPDATE volunteers SET status_notified = 1 WHERE id = ?");
                $update_stmt->execute([$id]);
            }
        }

        $success_message = "Volunteer rejected." . ($email_sent ? " Email notification sent." : " Email notification failed.");

    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM volunteers WHERE id = ?");
        $stmt->execute([$id]);

        $success_message = "Volunteer record deleted.";
    }
}

header("Location: admin-volunteers.php?success=" . urlencode($success_message));
exit();
?>