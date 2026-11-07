<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['module_pdf'])) {

    $module_numbers = $_POST['module_number'] ?? [];
    $titles = $_POST['title'] ?? [];
    $types = $_POST['type'] ?? [];
    $files = $_FILES['module_pdf'];

    $total = count($module_numbers);
    $success_count = 0;
    $errors = [];

    for ($i = 0; $i < $total; $i++) {
        // Skip rows with no file actually selected
        if (!isset($files['error'][$i]) || $files['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = "Module #" . htmlspecialchars($module_numbers[$i]) . ": upload error.";
            continue;
        }

        $module_number = intval($module_numbers[$i]);
        $title = $titles[$i];
        $type = $types[$i];

        // If a module with this number already exists, delete its old file
        $check_query = "SELECT file_path FROM modules WHERE module_number = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->execute([$module_number]);

        if ($old_row = $check_stmt->fetch()) {
            $old_disk_path = "../" . $old_row['file_path'];
            if (file_exists($old_disk_path)) {
                unlink($old_disk_path);
            }
        }

        $file_tmp = $files['tmp_name'][$i];
        $target_filename = "module_" . $module_number . "_" . time() . "_" . $i . ".pdf";
        $destination = "../modules/" . $target_filename;
        $db_filepath = "modules/" . $target_filename;

        if (move_uploaded_file($file_tmp, $destination)) {
            $query = "INSERT INTO modules (module_number, title, type, file_path) VALUES (?, ?, ?, ?)
                      ON CONFLICT (module_number) DO UPDATE SET title = EXCLUDED.title, type = EXCLUDED.type,
                      file_path = EXCLUDED.file_path, updated_at = CURRENT_TIMESTAMP";

            $stmt = $conn->prepare($query);

            if ($stmt->execute([$module_number, $title, $type, $db_filepath])) {
                $success_count++;
            } else {
                $errors[] = "Module #" . $module_number . ": database error.";
            }
        } else {
            $errors[] = "Module #" . $module_number . ": failed to save file.";
        }
    }

    if ($success_count > 0 && empty($errors)) {
        header("Location: admin-modules.php?status=success&count=" . $success_count);
        exit();
    } elseif ($success_count > 0 && !empty($errors)) {
        header("Location: admin-modules.php?status=partial&count=" . $success_count . "&errors=" . urlencode(implode(' ', $errors)));
        exit();
    } else {
        header("Location: admin-modules.php?status=error&errors=" . urlencode(implode(' ', $errors)));
        exit();
    }

} else {
    die("No file detected. Please try again.");
}
?>