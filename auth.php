<?php

session_start();

if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role']    = $user['role'];

    // Role-based destination
    if ($user['role'] === 'admin') {
        header('Location: /admin/admin-dashboard.php');
        exit;
    } else {
        header('Location: /user/dashboard.php');
        exit;
    }
}

?>