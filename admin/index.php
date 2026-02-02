<?php
// Admin dashboard and order management
require '../includes/session.php';
require '../includes/db.php';

// Verify admin permissions
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Handle order status updates from the table
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    
    header('Location: index.php');
    exit;
}

// Calculate business statistics for the dashboard
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="dashboard-container">
    <!-- Load shared admin sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="main-header">
            <h2>Dashboard</h2>
            <span>Welcome, Admin</span>
        </div>

        <!-- Sales and order statistics grid -->
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

        <!-- Latest orders table with status controls -->
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
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Retrieve 10 most recent orders
                    $orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 10")->fetchAll();
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
                            <!-- Status update form triggers on dropdown change -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" onchange="this.form.submit()" class="status-select status-<?php echo $order['status']; ?>">
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>
