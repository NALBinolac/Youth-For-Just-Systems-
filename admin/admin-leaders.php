<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ==========================================
// 1. EDIT MODE LOGIC: Fetch leader data if 'edit' is clicked
// ==========================================
$edit_id = '';
$edit_name = '';
$edit_role = '';
$edit_bio = '';
$is_editing = false;

if (isset($_GET['edit'])) {
    $is_editing = true;
    $edit_id = intval($_GET['edit']);
    
    $edit_query = "SELECT * FROM leaders WHERE id = $edit_id LIMIT 1";
    $edit_result = mysqli_query($conn, $edit_query);
    
    if ($edit_result && mysqli_num_rows($edit_result) > 0) {
        $edit_row = mysqli_fetch_assoc($edit_result);
        $edit_name = $edit_row['name'];
        $edit_role = $edit_row['role'];
        $edit_bio = $edit_row['bio'];
    }
}
// ==========================================

$leaders = [];
$query = "SELECT * FROM leaders ORDER BY id ASC";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $leaders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leadership Roster | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">
        <h1 style="margin-bottom: 10px;">Leadership Roster</h1>
        <p style="color: #666; margin-bottom: 30px;">Manage the executive team and board members displayed on the About page.</p>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert-success">Team member saved successfully!</div>
        <?php endif; ?>
        <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
            <div class="alert-success">Team member removed.</div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="grid-split">
            <!-- Left Side: Form -->
            <div id="leader-form-section">
                <div class="panel-box">
                    
                    <!-- CLICKABLE TOGGLE HEADER -->
                    <h3 onclick="toggleLeaderForm()" style="margin-bottom: 25px; color:#2e7d32; cursor: pointer; display: flex; justify-content: space-between; align-items: center; user-select: none; padding: 12px 15px; background: #e8f5e9; border-radius: 6px; border: 1px dashed #2e7d32; font-size: 1.1rem;">
                        <span><?= $is_editing ? '✎ Edit Team Member' : '+ Add Team Member' ?></span>
                        <span id="form-toggle-icon" style="font-size: 1rem; color: #2e7d32;"><?= $is_editing ? '▲' : '▼' ?></span>
                    </h3>
                    
                    <!-- HIDDEN FORM CONTAINER -->
                    <div id="leader-form-container" style="display: <?= $is_editing ? 'block' : 'none' ?>;">
                        <form action="admin-process-leaders.php" method="POST" enctype="multipart/form-data">
                            
                            <!-- Hidden ID field for updates -->
                            <?php if($is_editing): ?>
                                <input type="hidden" name="leader_id" value="<?= $edit_id ?>">
                            <?php endif; ?>

                            <label>Full Name</label>
                            <input type="text" name="name" required placeholder="e.g., Castle Reynera" value="<?= htmlspecialchars($edit_name) ?>">
                            
                            <label>Official Title / Role</label>
                            <input type="text" name="role" required placeholder="e.g., National Convener" value="<?= htmlspecialchars($edit_role) ?>">
                            
                            <label>Biography</label>
                            <div id="editor-bio"><?= $edit_bio ?></div>
                            <textarea name="bio" id="bio" style="display:none;"></textarea>
                            
                            <label style="margin-top: 15px;">Headshot Photo 
                                <?php if($is_editing): ?>
                                    <span style="color:#d32f2f; font-size:0.8rem; font-weight:normal;">*Leave blank to keep current photo</span>
                                <?php endif; ?>
                            </label>
                            <input type="file" name="image" accept="image/*" <?= $is_editing ? '' : 'required' ?> style="padding: 10px; background: #f8f9fa;">
                            
                            <?php if($is_editing): ?>
                                <div style="display: flex; gap: 10px; margin-top: 10px;">
                                    <button type="submit" name="action" value="edit" class="btn btn-save" style="flex: 1;">Update Roster</button>
                                    <a href="admin-leaders.php" class="btn btn-back" style="flex: 1; justify-content: center;">Cancel Edit</a>
                                </div>
                            <?php else: ?>
                                <button type="submit" name="action" value="add" class="btn btn-save" style="width: 100%;">Add to Roster</button>
                            <?php endif; ?>

                        </form>
                    </div> <!-- END HIDDEN FORM CONTAINER -->
                </div>
            </div>

            <!-- Right Side: Live Roster -->
            <div class="panel-box" style="border: 1px solid #e0e0e0; background: #fafafa; height: fit-content;">
                <h3 style="margin-bottom: 15px; color:#1b5e20;">Current Team</h3>
                
                <div class="feed-container">
                    <?php if (empty($leaders)): ?>
                        <p style="color: #888; font-style: italic; text-align: center; margin-top: 20px;">No leaders added yet.</p>
                    <?php else: ?>
                        <?php foreach ($leaders as $leader): ?>
                            <div class="leader-card">
                                <img src="../<?= htmlspecialchars($leader['image_path']) ?>" alt="Headshot" class="leader-img">
                                <div class="leader-info">
                                    <div class="leader-name"><?= htmlspecialchars($leader['name']) ?></div>
                                    <div class="leader-role"><?= htmlspecialchars($leader['role']) ?></div>
                                    
                                    <!-- PROPERLY ALIGNED EDIT AND REMOVE BUTTONS -->
                                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                                        <a href="admin-leaders.php?edit=<?= $leader['id'] ?>" class="btn-edit" style="display: flex; align-items: center; justify-content: center; padding: 0 14px; height: 32px; box-sizing: border-box; font-size: 0.85rem;">Edit</a>
                                        
                                        <form action="admin-process-leaders.php" method="POST" onsubmit="return confirm('Remove this member?');" style="margin: 0; display: flex;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="leader_id" value="<?= $leader['id'] ?>">
                                            <input type="hidden" name="image_path" value="<?= htmlspecialchars($leader['image_path']) ?>">
                                            <button type="submit" class="btn-delete" style="display: flex; align-items: center; justify-content: center; padding: 0 14px; height: 32px; box-sizing: border-box; font-size: 0.85rem; border: none; cursor: pointer; color: white;">Remove</button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        // Form Toggle Function
        function toggleLeaderForm() {
            const formContainer = document.getElementById('leader-form-container');
            const icon = document.getElementById('form-toggle-icon');
            
            if (formContainer.style.display === 'none') {
                formContainer.style.display = 'block';
                icon.textContent = '▲';
            } else {
                formContainer.style.display = 'none';
                icon.textContent = '▼';
            }
        }

        const quillBio = new Quill('#editor-bio', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['clean']
                ]
            }
        });

        document.querySelector('form[action="admin-process-leaders.php"]').addEventListener('submit', function (e) {
            document.querySelector('#bio').value = quillBio.root.innerHTML;
            if (quillBio.getText().trim().length === 0) {
                e.preventDefault();
                alert('Please write a biography before submitting.');
            }
        });

        // Auto-scroll to form if editing
        <?php if($is_editing): ?>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("leader-form-section").scrollIntoView({ behavior: "smooth" });
        });
        <?php endif; ?>
    </script>

</body>
</html>