<?php
session_start();
include 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if email already exists
    $check_query = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $error = "This email is already registered.";
    } else {
        // Hash the password safely
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Fixed column targeting to use 'username'
        $insert_query = "INSERT INTO users (name, fullname, username, email, password) VALUES (?, ?, ?, ?, ?) RETURNING id";
        $stmt2 = $conn->prepare($insert_query);

        if ($stmt2->execute([$username, $username, $username, $email, $hashed_password])) {
            $_SESSION['user_id'] = $stmt2->fetchColumn();
            $_SESSION['user_name'] = $username;
            $_SESSION['role'] = 'student';

            header("Location: learning.php");
            exit();
        } else {
            $error = "Registration failed. Please check your database connection.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Youth for Just Food Systems</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section style="min-height: 85vh; display: flex; align-items: center; background-color: var(--light-gray);">
    <div class="container" style="max-width: 500px;">
        <h2 class="section-title">Create Account</h2>
        <p class="section-subtitle text-center">Register to track your modules progression.</p>

        <?php if (!empty($error)): ?>
            <div style="background-color: #ffcdd2; color: #b71c1c; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; text-align: center;">
                ⚠️ <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="signup.php" method="POST">
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Username / Full Name</label>
            <input type="text" name="username" placeholder="Juan Dela Cruz" required>

            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Email Address</label>
            <input type="email" name="email" placeholder="juan@university.edu.ph" required>

            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Password</label>
            <input type="password" name="password" placeholder="Create password" required>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px; border-radius: 10px;">
                Register & Open Portal
            </button>
        </form>

        <p class="text-center" style="margin-top: 20px;">
            Already have an account? <a href="login.php" class="edit-btn">Log in here</a>
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
