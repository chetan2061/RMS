<?php
require '../includes/session.php';
require '../includes/db.php';

// Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Fetch Admin Info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Profile</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="main-header">
            <h2>Admin Profile</h2>
        </div>

        <div class="card" style="max-width: 600px; padding: 30px;">
            <h3 style="margin-bottom: 20px;">Account Information</h3>
            
            <div style="margin-bottom: 15px;">
                <strong>Email:</strong><br>
                <span><?php echo htmlspecialchars($admin['email']); ?></span>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Role:</strong><br>
                <span style="color: #e74c3c; font-weight: bold;">Administrator</span>
            </div>
            
            <div style="margin-bottom: 25px;">
                <strong>Member Since:</strong><br>
                <span><?php echo date('F j, Y', strtotime($admin['created_at'])); ?></span>
            </div>

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">

            <a href="profile.php?logout=1" 
               onclick="return confirm('Are you sure you want to logout?')"
               style="display: inline-block; background: #e74c3c; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

    </div>
</div>

</body>
</html>
