<?php

include 'config.php';

$form_type = $_POST['form_type'];

if($form_type == "member"){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $interest = $_POST['core_interest'];
    $motivation = $_POST['motivation'];

    $sql = "INSERT INTO memberships
    (fullname,email,interest,motivation)
    VALUES
    (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$fullname, $email, $interest, $motivation]);

    header("Location: get-involved.php?success=member");
    exit();
}

if($form_type == "volunteer"){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    $activities =
        isset($_POST['activities']) && is_array($_POST['activities'])
        ? implode(", ", $_POST['activities'])
        : '';

    // Server-side guard: reject the submission if no activity was selected,
    // in case the browser-side check above is bypassed (e.g. JS disabled).
    if (empty($activities)) {
        header("Location: get-involved.php?error=no_activity#volunteer");
        exit();
    }

    $affiliation = $_POST['affiliation'];

   $sql = "INSERT INTO volunteers
(fullname,email,activities,affiliation)
VALUES
(?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$fullname, $email, $activities, $affiliation]);

    header("Location: get-involved.php?success=volunteer");
    exit();
}

?>