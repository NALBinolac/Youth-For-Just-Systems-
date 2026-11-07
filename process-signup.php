<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Securely hash the password so it's unreadable in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare statement to insert user safely
    $sql = "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$fullname, $email, $hashed_password]);
        // Successfully registered! Send them straight to the login page
        header("Location: login.php?registration=success");
        exit();
    } catch (PDOException $e) {
        echo "Error: Email might already be registered.";
    }
}
?>