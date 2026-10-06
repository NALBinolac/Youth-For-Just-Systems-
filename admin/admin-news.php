<?php
session_start();
require_once '../config.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch existing articles
$articles = [];
$query = "SELECT * FROM news_articles ORDER BY article_date DESC, created_at DESC";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $articles[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>News & Media | Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
</head>
<body>

    <?php include '../includes/admin-navbar.php'; ?>

    <div class="main-content">
        <h1 style="margin-bottom: 10px;">News & Media Feed</h1>
        <p style="color: #666; margin-bottom: 30px;">Publish updates, event highlights, and media features to the public news feed.</p>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert-success">Article successfully published!</div>
        <?php endif; ?>
        <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
            <div class="alert-success">Article has been removed.</div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="grid-split">
            <!-- Left Side: Upload Form -->
            <div>
                <div class="panel-box">
                    <h3 style="margin-bottom: 25px; color:#2e7d32;">+ Publish New Article</h3>
                    
                    <form action="admin-process-news.php" method="POST" enctype="multipart/form-data">
                        <label>Article Headline</label>
                        <input type="text" name="title" required placeholder="e.g., Youth Summit 2026 a Massive Success">
                        
                        <label>Date of Event / Publication</label>
                        <input type="date" name="article_date" required>
                        
                        <label>Short Summary</label>
                        <div id="editor-summary"></div>
                        <textarea name="summary" id="summary" style="display:none;"></textarea>
                        
                        <label>Cover Photo</label>
                        <input type="file" name="cover_image" accept="image/*" required style="padding: 10px; background: #f8f9fa;">
                        
                        <button type="submit" name="action" value="add" class="btn">Publish to Feed</button>
                    </form>
                </div>
            </div>

            <!-- Right Side: Live Feed -->
            <div class="panel-box" style="border: 1px solid #e0e0e0; background: #fafafa; height: fit-content;">
                <h3 style="margin-bottom: 15px; color:#1b5e20;">Live Articles</h3>
                
                <div class="feed-container">
                    <?php if (empty($articles)): ?>
                        <p style="color: #888; font-style: italic; text-align: center; margin-top: 20px;">No articles published yet.</p>
                    <?php else: ?>
                        <?php foreach ($articles as $article): 
                            $date_obj = new DateTime($article['article_date']);
                            $formatted_date = $date_obj->format('F j, Y');
                        ?>
                            <div class="article-card">
                                <img src="../<?= htmlspecialchars($article['image_path']) ?>" alt="Thumbnail" class="article-img">
                                <div class="article-info">
                                    <div class="article-title"><?= htmlspecialchars($article['title']) ?></div>
                                    <span class="article-date">📅 <?= $formatted_date ?></span>
                                    
                                    <!-- Delete Button triggers a form submission to the processor -->
                                    <form action="admin-process-news.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                        <input type="hidden" name="image_path" value="<?= htmlspecialchars($article['image_path']) ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
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
        const quillSummary = new Quill('#editor-summary', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        document.querySelector('form[action="admin-process-news.php"]').addEventListener('submit', function (e) {
            document.querySelector('#summary').value = quillSummary.root.innerHTML;
            if (quillSummary.getText().trim().length === 0) {
                e.preventDefault();
                alert('Please write a summary before publishing.');
            }
        });
    </script>

</body>
</html>