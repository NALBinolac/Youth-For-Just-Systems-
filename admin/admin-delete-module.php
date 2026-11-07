<?php
session_start();
require_once '../config.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['module_number'])) {
    $module_number = intval($_GET['module_number']);

    $find_query = "SELECT file_path FROM modules WHERE module_number = ?";
    $stmt = $conn->prepare($find_query);
    $stmt->execute([$module_number]);
    
    if ($row = $stmt->fetch()) {
        $disk_path = "../" . $row['file_path'];
        if (file_exists($disk_path)) {
            unlink($disk_path);
        }
    }

    $delete_query = "DELETE FROM modules WHERE module_number = ?";
    $del_stmt = $conn->prepare($delete_query);
    
    if ($del_stmt->execute([$module_number])) {
        header("Location: admin-modules.php?status=deleted");
        exit();
    } else {
        header("Location: admin-modules.php?error=Database could not wipe module entry.");
        exit();
    }
} else {
    header("Location: admin-modules.php");
    exit();
}
?>