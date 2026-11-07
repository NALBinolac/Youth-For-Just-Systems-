<?php

include 'config.php';

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$interest = $_POST['interest'];
$motivation = $_POST['motivation'];

$sql = "INSERT INTO memberships
(fullname, email, interest, motivation)
VALUES
(?, ?, ?, ?)";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([$fullname, $email, $interest, $motivation]);
    echo "Membership Application Submitted!";
} catch (PDOException $e) {
    echo "Error: could not save your application.";
}

?>