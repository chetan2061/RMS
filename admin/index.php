<?php
require '../includes/session.php';
require '../includes/db.php';

// Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Handle Status Update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    
    header('Location: index.php');
    exit;
}

// Calculate Statistics
$total_sales_stmt = $pdo->query("SELECT SUM(total_price) FROM orders WHERE status = 'completed'");
$total_sales = $total_sales_stmt->fetchColumn() ?: 0;

$total_orders_stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_orders = $total_orders_stmt->fetchColumn() ?: 0;

$pending_orders_stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
$pending_orders = $pending_orders_stmt->fetchColumn() ?: 0;

$total_products_stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_products = $total_products_stmt->fetchColumn() ?: 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="main-header">
            <h2>Dashboard</h2>
            <span>Welcome, Admin</span>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-box" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <p>Total Sales</p>
                <h2>Rs. <?php echo number_format($total_sales, 2); ?></h2>
            </div>
            <div class="stat-box" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                <p>Total Orders</p>
                <h2><?php echo $total_orders; ?></h2>
            </div>
            <div class="stat-box" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                <p>Pending Orders</p>
                <h2><?php echo $pending_orders; ?></h2>
            </div>
            <div class="stat-box" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                <p>Total Products</p>
                <h2><?php echo $total_products; ?></h2>
            </div>
        </div>

        <!-- Recent Orders -->
        <h3 style="margin-bottom: 15px;">Recent Orders</h3>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Location</th>
                        <th class="hide-mobile">Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 10")->fetchAll();
                    foreach ($orders as $order):
                    ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($order['customer_name'] ?: 'Guest'); ?><br>
                            <small style="color: #777;"><?php echo htmlspecialchars($order['customer_phone']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($order['delivery_location']); ?></td>
                        <td class="hide-mobile" style="max-width: 300px;">
                            <?php echo htmlspecialchars($order['order_details']); ?>
                        </td>
                        <td>Rs. <?php echo number_format($order['total_price']); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" onchange="this.form.submit()" style="padding: 5px 10px; border-radius: 4px; border: 1px solid #ddd; background: <?php 
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
                                ?>; font-weight: bold; cursor: pointer;">
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>
