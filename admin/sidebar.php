<div class="sidebar">
    <div class="sidebar-brand">
        <h2>Admin Panel</h2>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>">
                <i class="fas fa-utensils"></i> Menu Management
            </a>
        </li>
        <li>
            <a href="reviews.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : ''; ?>">
                <i class="fas fa-star"></i> Reviews
            </a>
        </li>
        <li>
            <a href="index.php?logout=1" onclick="return confirm('Logout?')" style="color: #e74c3c;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
        <li>
            <a href="../index.php" target="_blank">
                <i class="fas fa-external-link-alt"></i> Visit Site
            </a>
        </li>
    </ul>
</div>
