<?php
include 'config.php';

$fullname     = $_POST['fullname'];
$email        = $_POST['email'];
$skills       = $_POST['skills'];
$interests    = $_POST['interests'];
$availability = $_POST['availability'];

// 1. Use placeholders (?) instead of raw variables
$sql = "INSERT INTO volunteers (fullname, email, skills, interests, availability) VALUES (?, ?, ?, ?, ?)";

// 2. Prepare and run it safely
$stmt = $conn->prepare($sql);
$stmt->execute([$fullname, $email, $skills, $interests, $availability]);

header("Location: volunteer.php?success=applied");
exit();
?>