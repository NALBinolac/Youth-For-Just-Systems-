<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">

    <a href="index.php" class="logo-container">
        <img
            src="images/YJFS Organizational Logo.png"
            alt="Youth for Just Food Systems Logo"
            class="logo-img">
    </a>

    <ul class="nav-links">

        <li>
            <a href="index.php"
            <?php if($currentPage=="index.php") echo 'style="color:#2E7D32;"'; ?>>
                Home
            </a>
        </li>

        <li>
            <a href="about.php"
            <?php if($currentPage=="about.php") echo 'style="color:#2E7D32;"'; ?>>
                About Us
            </a>
        </li>

        <li>
            <a href="work.php"
            <?php if($currentPage=="work.php") echo 'style="color:#2E7D32;"'; ?>>
                Our Work
            </a>
        </li>

        <li>
            <a href="learning.php"
            <?php if($currentPage=="learning.php" || $currentPage=="resources.php") echo 'style="color:#2E7D32;"'; ?>>
                Learn
            </a>
        </li>

        <li>
            <a href="get-involved.php"
            <?php if($currentPage=="get-involved.php") echo 'style="color:#2E7D32;"'; ?>>
                Get Involved
            </a>
        </li>

        <li>
            <a href="support.php"
            <?php if($currentPage=="support.php") echo 'style="color:#2E7D32;"'; ?>>
                Support Us
            </a>
        </li>

        <li>
            <a href="events.php"
            <?php if($currentPage=="events.php") echo 'style="color:#2E7D32;"'; ?>>
                Events
            </a>
        </li>

        <li>
            <a href="about.php#contact">
                Contact
            </a>
        </li>

    </ul>

    <div class="nav-buttons" style="display: flex; align-items: center; gap: 20px;">
        <?php if (!empty($_SESSION['role'])): ?>
            
            <a href="<?php echo ($_SESSION['role'] === 'admin') ? 'admin/admin-dashboard.php' : 'learning.php'; ?>" 
               style="text-decoration: none; color: #2e7d32; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Dashboard
            </a>
            
            <a href="logout.php" style="text-decoration: none; color: #d32f2f; font-weight: 600; font-size: 0.9rem;">Logout</a>
            
        <?php else: ?>
            
            <a href="login.php" style="text-decoration: none; color: #2e7d32; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                Sign In
            </a>
            
        <?php endif; ?>
    </div>

</nav>