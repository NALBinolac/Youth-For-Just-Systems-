<?php
session_start();
require_once '../config.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = "";
$success = "";

// Handle the Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $date = $_POST['date'];
    
    $start_time_raw = $_POST['start_time'];
    $end_time_raw = $_POST['end_time'];
    $start_formatted = date("g:i A", strtotime($start_time_raw));
    $end_formatted = date("g:i A", strtotime($end_time_raw));
    $time_range = $start_formatted . ' - ' . $end_formatted;

    $venue = $_POST['venue'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    // Insert the new event with ALL columns
    $insert_query = "INSERT INTO events (title, date, time_range, venue, category, description) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "ssssss", $title, $date, $time_range, $venue, $category, $description);

    if (mysqli_stmt_execute($stmt)) {
        $success = "New event successfully added to the schedule!";
    } else {
        $error = "Failed to add event. Make sure your database structure is updated.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Event | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">
        <h1 style="font-size: 2.2rem; color: #111; margin-bottom: 5px;">Schedule New Event</h1>
        <p style="color: #666; margin-bottom: 30px;">Add upcoming festivals, campaigns, or training sessions to the platform.</p>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="page-layout">
            <div class="form-group">
                <form action="admin-add-event.php" method="POST">
                    
                    <label>Event Title</label>
                    <input type="text" name="title" placeholder="e.g., Annual Plant-Based Festival" required>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label>Event Date</label>
                            <input type="date" name="date" required>
                        </div>
                        <div>
                            <label>Time Range</label>
                            <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 24px;">
                                <input type="time" name="start_time" required style="margin-bottom: 0;">
                                <span style="color: #666; font-weight: 600;">to</span>
                                <input type="time" name="end_time" required style="margin-bottom: 0;">
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                        <div>
                            <label>Venue / Location</label>
                            <input type="text" name="venue" placeholder="e.g., University Main Amphitheater" required>
                        </div>
                        <div>
                            <label>Category Tag</label>
                            <select name="category" required>
                                <option value="Festival">Festival</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Discussion">Discussion</option>
                                <option value="Campaign">Campaign</option>
                            </select>
                        </div>
                    </div>

                    <label>Event Description</label>
                    <div id="editor-description"></div>
                    <textarea name="description" id="description" style="display:none;"></textarea>

                    <div class="btn-container">
                        <a href="admin-events.php" class="btn btn-back">← Back to Events</a>
                        <button type="submit" class="btn btn-save">Create Event</button>
                    </div>
                </form>
            </div>

            <div class="info-card">
                <h3>💡 Publishing Guidelines</h3>
                <p style="margin-bottom: 10px; font-size: 0.95rem;">Before scheduling a new event, please ensure:</p>
                <ul>
                    <li>The venue has been officially booked and confirmed.</li>
                    <li>Dates do not conflict with university exam weeks or major holidays.</li>
                    <li>For online events, paste the meeting link in the venue field.</li>
                </ul>
            </div>
        </div>
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        const quillDescription = new Quill('#editor-description', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        document.querySelector('form[action="admin-add-event.php"]').addEventListener('submit', function (e) {
            document.querySelector('#description').value = quillDescription.root.innerHTML;
            if (quillDescription.getText().trim().length === 0) {
                e.preventDefault();
                alert('Please describe the event before submitting.');
            }
        });
    </script>
</body>
</html>