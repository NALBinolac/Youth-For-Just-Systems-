<?php
session_start();
require_once '../config.php';

// Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $section = $_POST['section_name'];
    $content = $_POST['content_text'];
    $image_path = null;

    // 1. Handle the Media Upload (if the admin attached a photo)
    if (isset($_FILES['media_asset']) && $_FILES['media_asset']['error'] == 0) {
        
        $target_dir = "../uploads/";
        
        // Safety check: Create the uploads folder automatically if it doesn't exist yet!
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = time() . "_" . basename($_FILES['media_asset']['name']);
        $destination = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES['media_asset']['tmp_name'], $destination)) {
            // Save the path without the "../" so it works nicely on the public site
            $image_path = "uploads/" . $filename; 
        }
    }

    // 2. Save to Database (insert, or update if the section already exists)
    if ($image_path) {
        // If they uploaded a new image, update both text and image
        $query = "INSERT INTO site_content (section_name, content_text, image_path) VALUES (?, ?, ?) 
                  ON CONFLICT (section_name) DO UPDATE SET content_text = EXCLUDED.content_text, image_path = EXCLUDED.image_path";
        $params = [$section, $content, $image_path];
    } else {
        // If no image was uploaded, ONLY update the text
        $query = "INSERT INTO site_content (section_name, content_text) VALUES (?, ?) 
                  ON CONFLICT (section_name) DO UPDATE SET content_text = EXCLUDED.content_text";
        $params = [$section, $content];
    }

    // 3. Execute and Redirect
    $stmt = $conn->prepare($query);
    if ($stmt->execute($params)) {
        header("Location: admin-programs.php?status=success");
        exit();
    } else {
        header("Location: admin-programs.php?error=Database Error");
        exit();
    }
} else {
    header("Location: admin-programs.php");
    exit();
}
?>