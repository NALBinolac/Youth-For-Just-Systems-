<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header("Location: ../login.php");
    exit(); 
}

// ==========================================
// 1. EDIT MODE LOGIC
// ==========================================
$edit_module_number = '';
$edit_title = '';
$edit_type = '';
$is_editing = false;

if (isset($_GET['edit'])) {
    $is_editing = true;
    $edit_id = intval($_GET['edit']);
    
    $edit_query = "SELECT * FROM modules WHERE module_number = $edit_id LIMIT 1";
    $edit_result = mysqli_query($conn, $edit_query);
    
    if ($edit_result && mysqli_num_rows($edit_result) > 0) {
        $edit_row = mysqli_fetch_assoc($edit_result);
        $edit_module_number = $edit_row['module_number'];
        $edit_title = $edit_row['title'];
        $edit_type = $edit_row['type'];
    }
}
// ==========================================

$submissions_query = "SELECT s.*, u.username AS student_name 
                      FROM submissions s 
                      LEFT JOIN users u ON s.user_id = u.id 
                      ORDER BY s.submitted_at DESC";
$submissions_result = mysqli_query($conn, $submissions_query);

$modules_query = "SELECT * FROM modules ORDER BY module_number ASC";
$modules_result = mysqli_query($conn, $modules_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Platform Management</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom UI Enhancements for the Modal */
        .upload-modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            position: relative;
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .file-upload-box {
            border: 2px dashed #a5d6a7;
            background-color: #f1f8e9;
            padding: 25px 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .file-upload-box:hover {
            border-color: #2e7d32;
            background-color: #e8f5e9;
        }
        .close-btn {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 24px;
            color: #999;
            cursor: pointer;
            line-height: 1;
            transition: 0.2s;
        }
        .close-btn:hover { color: #d32f2f; }
        #modal-scroll-area {
            max-height: 60vh;
            overflow-y: auto;
            padding-right: 5px;
        }
    </style>
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">
        
        <!-- Header & Action Button -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
            <div>
                <h1>Learning Platform Management</h1>
                <p class="subtitle" style="margin-bottom:0;">Deploy core text records, tracking assessments, quizzes, and digital worksheets.</p>
            </div>
            <button onclick="openModal()" class="btn btn-save" style="padding: 12px 25px; font-size: 1.05rem; box-shadow: 0 4px 10px rgba(46,125,50,0.3);">
                <span style="margin-right: 8px; font-weight:bold;">+</span> Upload Module
            </button>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'success'): ?>
                <div class="alert alert-success">✅ Successfully uploaded <?= htmlspecialchars($_GET['count'] ?? '') ?> module(s)!</div>
            <?php elseif ($_GET['status'] === 'deleted'): ?>
                <div class="alert alert-success">✅ Module deleted successfully!</div>
            <?php elseif ($_GET['status'] === 'error'): ?>
                <div class="alert alert-error">❌ Upload failed: <?= htmlspecialchars($_GET['errors'] ?? '') ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- New Layout: Inventory on Left, Feed on Right -->
        <div class="dashboard-layout">
            
            <!-- Active Modules Inventory (Now takes the main left space) -->
            <div class="inventory-section" style="margin-top: 0; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                <h3 style="color: #2e7d32; border-bottom: 2px solid #2e7d32; padding-bottom: 12px; margin-top:0;">Active Modules Inventory</h3>
                
                <?php if ($modules_result && mysqli_num_rows($modules_result) > 0): ?>
                    <div style="max-height: 500px; overflow-y: auto; padding-right: 10px; margin-top: 15px;">
                    <?php while($mod = mysqli_fetch_assoc($modules_result)): ?>
                        <div class="inventory-item" style="padding: 20px 0;">
                            <div class="inventory-meta">
                                <h5 style="font-size: 1.05rem; margin-bottom: 4px;">M<?php echo htmlspecialchars($mod['module_number']); ?>: <?php echo htmlspecialchars($mod['title']); ?></h5>
                                <p style="font-size: 0.85rem;">Type: <?php echo htmlspecialchars($mod['type']); ?></p>
                            </div>
                            <div class="action-btns">
                                <a href="admin-modules.php?edit=<?php echo $mod['module_number']; ?>" class="btn-edit">Edit</a>
                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $mod['module_number']; ?>)" class="btn-delete">Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #888;">Walang idinagdag na module sa imbentaryo.</div>
                <?php endif; ?>
            </div>

            <!-- Student Activity Overview (Stays on right) -->
            <div class="feed-panel" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                <h3 style="margin-top:0;">Student Activity Overview</h3>
                <p style="font-size: 0.85rem; color: #666666; margin-bottom: 20px;">Recent assignments submitted by enrolled users.</p>
                
                <div style="max-height: 500px; overflow-y: auto; padding-right: 5px;">
                    <?php if ($submissions_result && mysqli_num_rows($submissions_result) > 0): ?>
                        <?php while($sub = mysqli_fetch_assoc($submissions_result)): 
                            $file_names = json_decode($sub['file_name'], true) ?: [$sub['file_name']];
                            $file_paths = json_decode($sub['file_path'], true) ?: [$sub['file_path']];
                            $display_name = !empty($file_names) ? $file_names[0] : 'View Submission';
                            $download_link = !empty($file_paths) ? '../' . $file_paths[0] : '#';
                            $final_name = !empty($sub['student_name']) ? $sub['student_name'] : 'Deleted/Unknown User';
                        ?>
                            <div class="submission-card">
                                <div class="student-header">
                                    <div class="avatar"><?php echo strtoupper(substr($final_name, 0, 1)); ?></div>
                                    <div class="student-meta" style="flex-grow: 1;">
                                        <h4><?php echo htmlspecialchars($final_name); ?></h4>
                                        <span><?php echo date('M d, Y • g:i A', strtotime($sub['submitted_at'])); ?></span>
                                    </div>
                                    <span class="mod-tag">M<?php echo htmlspecialchars($sub['module_number']); ?></span>
                                </div>
                                <div class="file-badge">
                                    <span style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">📄 <?php echo htmlspecialchars($display_name); ?></span>
                                    <a href="<?php echo htmlspecialchars($download_link); ?>" class="btn-download" download>Download</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: #999999; padding: 40px; border: 1px dashed #dddddd; border-radius: 4px;">No recent submissions found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- THE NEW UPLOAD POPUP MODAL                 -->
    <!-- ========================================== -->
    <div id="uploadModal" class="modal" style="<?php echo $is_editing ? 'display:flex;' : 'display:none;'; ?> align-items:center; justify-content:center; background-color: rgba(0,0,0,0.6);">
        <div class="upload-modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            
            <div style="text-align: center; margin-bottom: 25px;">
                <h3 style="color: #2e7d32; margin-bottom: 5px; font-size: 1.4rem;">
                    <?php echo $is_editing ? 'Edit Module Record' : 'Upload Educational Files'; ?>
                </h3>
                <p style="font-size: 0.85rem; color: #666;">Provide the necessary material access for workspace students.</p>
            </div>

            <form action="admin-process-module.php" method="POST" enctype="multipart/form-data">
                <div id="modal-scroll-area">
                    <div id="module-rows">
                        <div class="module-row" style="position: relative; margin-bottom: 20px;">
                            
                            <!-- Dashed File Upload Box (Like Image Reference) -->
                            <div class="file-upload-box">
                                <div style="color: #2e7d32; font-size: 24px; margin-bottom: 10px;">☁️</div>
                                <p style="font-weight: 600; margin-bottom: 10px; font-size: 0.95rem;">Select file to upload</p>
                                <input type="file" name="module_pdf[]" accept="application/pdf" required style="border:none; box-shadow:none; padding:0; text-align:center; margin-bottom:0;">
                                <?php if($is_editing): ?>
                                    <p style="color:#d32f2f; font-size:0.75rem; margin-top:10px;">*Re-select PDF to apply changes</p>
                                <?php endif; ?>
                            </div>

                            <div class="form-group" style="padding:0; box-shadow:none; border:none; margin-top:0;">
                                <label style="font-size: 0.85rem;">Module Number</label>
                                <input type="number" name="module_number[]" placeholder="e.g., 1" required style="padding:10px; background:#f9fafb;" value="<?php echo htmlspecialchars($edit_module_number); ?>">
                            </div>
                            
                            <div class="form-group" style="padding:0; box-shadow:none; border:none; margin-top:0;">
                                <label style="font-size: 0.85rem;">Module / Assignment Title</label>
                                <input type="text" name="title[]" placeholder="e.g., Intro to Sustainable Urban Farming" required style="padding:10px; background:#f9fafb;" value="<?php echo htmlspecialchars($edit_title); ?>">
                            </div>
                            
                            <div class="form-group" style="padding:0; box-shadow:none; border:none; margin-top:0;">
                                <label style="font-size: 0.85rem;">Resource Classification</label>
                                <input type="text" name="type[]" placeholder="e.g., Core Reading Link" required style="padding:10px; background:#f9fafb;" value="<?php echo htmlspecialchars($edit_type); ?>">
                            </div>
                            
                            <button type="button" class="remove-row-btn" onclick="removeModuleRow(this)" style="display:none; background:#f8d7da; color:#721c24; border:none; padding:4px 10px; border-radius:4px; font-size:0.75rem; cursor:pointer; position:absolute; top:-10px; right:0;">Remove</button>
                        </div>
                    </div>
                </div>

                <?php if(!$is_editing): ?>
                    <button type="button" onclick="addModuleRow()" style="width:100%; background:#e8f5e9; color:#2e7d32; padding:10px; border:1px dashed #2e7d32; border-radius:6px; font-weight:600; cursor:pointer; margin-bottom:20px; transition: 0.3s;">+ Add Another Document</button>
                <?php endif; ?>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="closeModal()" class="btn btn-back" style="flex: 1; padding: 14px; border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-save" style="flex: 1; padding: 14px; border-radius: 8px; justify-content:center;">
                        <?php echo $is_editing ? 'Update Module' : 'Upload Files'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    function openModal() {
        document.getElementById('uploadModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('uploadModal').style.display = 'none';
        // If they were editing, reset the URL so it doesn't stay stuck in edit mode
        <?php if($is_editing): ?>
            window.location.href = 'admin-modules.php';
        <?php endif; ?>
    }

    // Close modal if user clicks outside the white box
    window.onclick = function(event) {
        var modal = document.getElementById('uploadModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    function addModuleRow() {
        const rows = document.getElementById('module-rows');
        const newRow = rows.children[0].cloneNode(true);
        
        newRow.querySelectorAll('input[type="number"], input[type="text"]').forEach(el => el.value = '');
        newRow.querySelectorAll('input[type="file"]').forEach(el => el.value = '');
        newRow.querySelector('select').selectedIndex = 0;
        
        const warningSpan = newRow.querySelector('p[style*="color:#d32f2f"]');
        if (warningSpan) warningSpan.remove();

        newRow.querySelector('.remove-row-btn').style.display = 'inline-block';
        rows.appendChild(newRow);
        
        // Add a line separator for visual clarity
        newRow.style.borderTop = "1px solid #eee";
        newRow.style.paddingTop = "20px";

        updateRemoveButtons();
    }

    function removeModuleRow(btn) {
        btn.closest('.module-row').remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.module-row');
        rows.forEach((row) => {
            const removeBtn = row.querySelector('.remove-row-btn');
            removeBtn.style.display = rows.length > 1 ? 'inline-block' : 'none';
        });
    }

    function confirmDelete(moduleNumber) {
        Swal.fire({
            title: 'Delete Module?',
            text: "Are you sure you want to delete this module?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d32f2f', 
            cancelButtonColor: '#6c757d',   
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'admin-delete-module.php?module_number=' + moduleNumber;
            }
        });
    }
    </script>

</body>
</html>