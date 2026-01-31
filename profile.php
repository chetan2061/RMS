<?php
require 'includes/session.php';
require 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Fetch user info
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT email, role, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch Order History
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile - Cups and Mugs</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <div style="display: flex; align-items: center;">
    <div class="logo">Cups and Mugs</div>
    <div class="nav-links">
        <a href="index.php">Menu</a>
        <a href="cart.php">Cart</a>
        <a href="profile.php" class="active">Profile</a>
    </div>
</nav>

<div class="container" style="padding: 40px 20px;">
    <h2 style="margin-bottom: 30px; color: var(--primary);"><i class="fas fa-user-circle"></i> My Profile</h2>

    <div class="card" style="max-width: 800px; margin: 0 auto; padding: 30px;">
        <h3 style="margin-bottom: 20px; color: var(--primary);">Account Information</h3>
        
        <div style="margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <strong><i class="fas fa-envelope"></i> Email:</strong><br>
            <span style="color: #555;"><?php echo htmlspecialchars($user['email']); ?></span>
        </div>
        
        <div style="margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <strong><i class="fas fa-user-tag"></i> Role:</strong><br>
            <span style="color: #555;"><?php echo ucfirst($user['role']); ?></span>
        </div>
        
        <div style="margin-bottom: 25px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            <strong><i class="fas fa-calendar"></i> Member Since:</strong><br>
            <span style="color: #555;"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
        </div>

        <hr style="margin: 25px 0; border: none; border-top: 1px solid #e0e0e0;">

        <h3 style="margin-bottom: 20px; color: var(--primary);">Order History</h3>
        
        <?php if (empty($orders)): ?>
            <p style="color: #777; text-align: center; padding: 20px;">No orders yet. <a href="index.php" style="color: var(--primary);">Start shopping!</a></p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td style="max-width: 300px;"><?php echo htmlspecialchars($order['order_details']); ?></td>
                            <td>Rs. <?php echo number_format($order['total_price'], 2); ?></td>
                            <td>
                                <span style="padding: 5px 10px; border-radius: 4px; background: <?php 
                                    echo match($order['status']) {
                                        'completed' => '#d4edda',
                                        'cancelled' => '#f8d7da',
                                        default => '#fff3cd'
                                    };
                                ?>; color: <?php 
                                    echo match($order['status']) {
                                        'completed' => '#155724',
                                        'cancelled' => '#721c24',
                                        default => '#856404'
                                    };
                                ?>; font-weight: bold;">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <hr style="margin: 25px 0; border: none; border-top: 1px solid #e0e0e0;">

        <a href="profile.php?logout=1" 
           onclick="return confirm('Are you sure you want to logout?')"
           style="display: inline-block; background: #e74c3c; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

</body>
</html>
