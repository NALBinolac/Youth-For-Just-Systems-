<?php
/**
 * Secure Password Hashing and Verification Example in PHP
 */

// 1. Simulate a plaintext password received from a user input/form
$plainTextPassword = 'admin';

// 2. Transform the plaintext password into a cryptographic hash
// PASSWORD_DEFAULT uses the strongest current algorithm (bcrypt or Argon2) supported by PHP.
$hashedPassword = password_hash($plainTextPassword, PASSWORD_DEFAULT);

echo "<h3>Password Transformation Results</h3>";
echo "<strong>Plaintext Password:</strong> " . htmlspecialchars($plainTextPassword) . "<br><br>";
echo "<strong>Cryptographic Hash (Store this in database):</strong><br>";
echo "<code>" . htmlspecialchars($hashedPassword) . "</code><br><br>";

// ---------------------------------------------------------
// 3. Verifying the password (e.g., during a login attempt)
// ---------------------------------------------------------

// Simulate a login attempt with the correct password
$loginAttemptSuccess = 'MySecurePassword123!';
// Simulate a login attempt with an incorrect password
$loginAttemptFailed = 'WrongPassword!';

echo "<h3>Verification Tests</h3>";

// Test 1: Verifying with the correct password
if (password_verify($loginAttemptSuccess, $hashedPassword)) {
    echo "✅ <strong>Login Success:</strong> The password matches the cryptographic hash!<br>";
} else {
    echo "❌ <strong>Login Failed:</strong> Password mismatch.<br>";
}

// Test 2: Verifying with an incorrect password
if (password_verify($loginAttemptFailed, $hashedPassword)) {
    echo "✅ <strong>Login Success:</strong> The password matches the cryptographic hash!<br>";
} else {
    echo "❌ <strong>Login Failed:</strong> Password mismatch (As expected for wrong password).<br>";
}
?>