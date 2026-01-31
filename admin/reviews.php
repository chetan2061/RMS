<?php
require '../includes/session.php';
require '../includes/db.php';

// Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Handle Delete Review
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: reviews.php');
    exit;
}

// Fetch Reviews
$reviews = $pdo->query("
    SELECT r.*, u.email as user_email, p.name as product_name 
    FROM reviews r 
    LEFT JOIN users u ON r.user_id = u.id 
    JOIN products p ON r.product_id = p.id 
    ORDER BY r.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Reviews</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="main-header">
            <h2>Customer Reviews</h2>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td>#<?php echo $review['id']; ?></td>
                        <td><?php echo htmlspecialchars($review['user_email'] ?: 'Guest'); ?></td>
                        <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                        <td>
                            <span style="color: gold;">
                                <?php echo str_repeat('★', $review['rating']); ?>
                            </span>
                        </td>
                        <td style="max-width: 300px;"><?php echo htmlspecialchars($review['comment']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($review['created_at'])); ?></td>
                        <td>
                            <a href="reviews.php?delete=<?php echo $review['id']; ?>" 
                               onclick="return confirm('Delete this review?')" 
                               style="color: #e74c3c; font-weight: bold;">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">No reviews yet.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>
