<?php
// Function for Volunteer Emails
function sendVolunteerStatusEmail($to_email, $name, $status, $rejection_reason = '', $admin_notes = '') {
    $subject = "Update on Your Volunteer Application";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // Change this to your actual NGO email
    $headers .= "From: Your NGO Name <noreply@yourngo.org>" . "\r\n"; 

    $message = "<html><body style='font-family: Arial, sans-serif; color: #333;'>";
    $message .= "<h2 style='color: #2e7d32;'>Volunteer Application $status</h2>";
    $message .= "<p>Hi <strong>" . htmlspecialchars($name) . "</strong>,</p>";
    
    if ($status === 'Approved') {
        $message .= "<p>Great news! Your application to volunteer with us has been <strong>approved</strong>.</p>";
        $message .= "<p>We will be in touch soon with details about upcoming activities and how you can get started.</p>";
    } elseif ($status === 'Rejected') {
        $message .= "<p>Thank you for offering your time to volunteer. Unfortunately, we are unable to accept your application at this time.</p>";
        if (!empty($rejection_reason)) {
            $message .= "<div style='background-color: #f8d7da; padding: 10px; border-left: 4px solid #c62828; margin: 15px 0;'>";
            $message .= "<strong>Reason:</strong> " . nl2br(htmlspecialchars($rejection_reason));
            $message .= "</div>";
        }
    }

    $message .= "<br><p>Best regards,<br><strong>The Admin Team</strong></p>";
    $message .= "</body></html>";

    return mail($to_email, $subject, $message, $headers);
}

// Function for Member Emails
function sendMemberStatusEmail($to_email, $name, $status, $rejection_reason = '') {
    $subject = "Update on Your Membership Application";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // Change this to your actual NGO email
    $headers .= "From: Your NGO Name <noreply@yourngo.org>" . "\r\n"; 

    $message = "<html><body style='font-family: Arial, sans-serif; color: #333;'>";
    $message .= "<h2 style='color: #2e7d32;'>Membership Application $status</h2>";
    $message .= "<p>Hi <strong>" . htmlspecialchars($name) . "</strong>,</p>";
    
    if ($status === 'Approved') {
        $message .= "<p>Congratulations! Your application to become an official member has been <strong>approved</strong>.</p>";
        $message .= "<p>We are thrilled to have you join our structural organizational framework. Our team will reach out shortly with your next steps and onboarding details.</p>";
    } elseif ($status === 'Rejected') {
        $message .= "<p>Thank you for applying to become a member. After careful review, we regret to inform you that we cannot approve your membership at this time.</p>";
        if (!empty($rejection_reason)) {
            $message .= "<div style='background-color: #f8d7da; padding: 10px; border-left: 4px solid #c62828; margin: 15px 0;'>";
            $message .= "<strong>Reason:</strong> " . nl2br(htmlspecialchars($rejection_reason));
            $message .= "</div>";
        }
        $message .= "<p>We highly encourage you to stay involved by joining our volunteer initiatives or participating in our public events.</p>";
    }

    $message .= "<br><p>Best regards,<br><strong>The Admin Team</strong></p>";
    $message .= "</body></html>";

    return mail($to_email, $subject, $message, $headers);
}
?>