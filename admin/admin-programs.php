<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Content Management | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">
        <h1 style="font-size: 2.2rem; color: #111; margin-bottom: 5px;">Website Content Management</h1>
        <p style="color: #666; margin-bottom: 30px;">Modify text components, leadership rosters, initiative profiles, and landing modules.</p>

        <!-- Dynamic Alerts -->
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert-success">Section updated successfully!</div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <!-- Reduced overall padding from 35px to 30px -->
        <div class="form-group" style="max-width: 100%; padding: 30px;">
            <form action="admin-process-content.php" method="POST" enctype="multipart/form-data">
                
                <label style="margin-top: 0;">Select Target Workspace Section</label>
                <select name="section_name" required>
                    <option value="homepage_hero">Landing Page Hero Text</option>
                    <option value="about_who_we_are">About Us - Who We Are</option>
                    <option value="about_impact">About Us - Our Impact Narrative</option>
                </select>

                <!-- Removed extra margin-top; letting the CSS handle the spacing naturally -->
                <label>Updated Description / Narrative Text</label>
                <!-- Added a smooth 20px margin below the editor -->
                <div id="editor-content" style="min-height: 250px; background: white; font-family: 'Montserrat', sans-serif; margin-bottom: 20px;"></div>
                <textarea name="content_text" id="content_text" style="display:none;"></textarea>

                <label>Attach Supporting Media Assets (Photos / Video Features)</label>
                <!-- Slimmed down the padding inside the dashed box -->
                <div style="border: 2px dashed #ced4da; padding: 12px 15px; border-radius: 8px; background-color: #f8f9fa; transition: background-color 0.2s;">
                    <input type="file" name="media_asset" accept="image/*" style="margin-bottom: 0; border: none; padding: 0; background: transparent; width: 100%;">
                </div>

                <!-- Pulled the button container closer -->
                <div style="margin-top: 20px; display: flex; justify-content: flex-end; border-top: 1px solid #eee; padding-top: 20px;">
                    <button type="submit" class="btn btn-save" style="font-size: 1.05rem; padding: 10px 24px;">
                        Commit Section Updates
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        const quillContent = new Quill('#editor-content', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        // Sync Quill content into the hidden textarea right before submit
        document.querySelector('form').addEventListener('submit', function (e) {
            const html = quillContent.root.innerHTML;
            document.querySelector('#content_text').value = html;
            
            if (quillContent.getText().trim().length === 0) {
                e.preventDefault();
                alert('Please enter some content before submitting.');
            }
        });
    </script>

</body>
</html>