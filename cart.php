<?php
// Cart and checkout management
session_start();
require 'includes/db.php';

// Handle item removal
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header('Location: cart.php');
    exit;
}

// Initialize variables
$error = '';
$name = '';
$phone = '';
$address = '';
$done = false;

// Fetch Cart Data (Unified Logic)
$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    // Ensure ids are integers to be safe
    $ids = array_filter($ids, 'is_numeric');
    
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $p) {
            $qty = $_SESSION['cart'][$p['id']] ?? 1;
            $line_total = $p['price'] * $qty;
            
            $cart_items[] = [
                'id' => $p['id'],
                'name' => $p['name'],
                'price' => $p['price'],
                'qty' => $qty,
                'line_total' => $line_total
            ];
            $total_price += $line_total;
        }
    }
}

// Process Order Submission
if (isset($_POST['order'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Input Validation
    if (empty($cart_items)) {
        $error = "Your cart is empty.";
    } elseif (empty($name)) {
        $error = "Name is required.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error = "Name must contain only letters and spaces.";
    } elseif (!is_numeric($phone) || strlen($phone) < 10) {
        $error = "Please enter a valid phone number (10+ digits).";
    } elseif (empty($address)) {
        $error = "Delivery address is required.";
    } else {
        // Prepare Order Details String
        $details = "";
        foreach ($cart_items as $item) {
            $details .= "{$item['name']} x{$item['qty']}, ";
        }
        $details = rtrim($details, ', ');

        // Save Order
        $user_id = $_SESSION['user_id'] ?? null;
        $sql = "INSERT INTO orders (user_id, customer_name, customer_phone, delivery_location, total_price, order_details, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$user_id, $name, $phone, $address, $total_price, $details])) {
            unset($_SESSION['cart']);
            $cart_items = []; // Clear for display
            $done = true;
        } else {
            $error = "Failed to place order. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">RMS</div>
    <div class="nav-links">
        <a href="index.php">Menu</a>
        <a href="cart.php" class="active">Cart (<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>)</a>
        <?php if(isset($_SESSION['user_id'])):?>
            <a href="index.php?logout=1" style="color: #e74c3c;">Logout</a>
        <?php else:?>
            <a href="login.php">Login</a>
        <?php endif;?>
    </div>
</nav>

<div class="cart-container">

<?php if($done): ?>
    <!-- Success Message -->
    <div class="cart-box cart-empty">
        <h2 style="color: #27ae60;">✓ Order Placed!</h2>
        <p>Your details have been received.</p>
        <p><strong>Call for confirmation: +977 9741706333</strong></p>
        <a href="index.php" class="btn">Return to Menu</a>
    </div>

<?php elseif(empty($cart_items)): ?>
    <!-- Empty Cart -->
    <div class="cart-box cart-empty">
        <h2>Your Cart is Empty</h2>
        <p>Looks like you haven't added anything yet.</p>
        <a href="index.php" class="btn">Browse Menu</a>
    </div>

<?php else: ?>
    <!-- Active Cart Display -->
    <div class="cart-box">
        <h2>Shopping Cart</h2>
        
        <?php foreach($cart_items as $item): ?>
        <div class="cart-item">
            <div><strong><?=htmlspecialchars($item['name'])?></strong></div>
            <div style="color: #666;"><?=htmlspecialchars($item['qty'])?> x Rs.<?=$item['price']?></div>
            <div><strong>Rs.<?=$item['line_total']?></strong></div>
            <div style="text-align: right;">
                <a href="?remove=<?=$item['id']?>" class="remove-link">X</a>
            </div>
        </div>
        <?php endforeach; ?>
        
        <div class="cart-total">
            Total: Rs. <?=$total_price?>
        </div>
    </div>

    <!-- Checkout Form -->
    <div class="cart-box">
        <h2>Checkout Details</h2>
        
        <?php if($error): ?>
            <div style="background: #fdf2f2; color: #dc3545; padding: 15px; border-left: 4px solid #dc3545; margin-bottom: 20px; border-radius: 4px;">
                <strong>Error:</strong> <?=$error?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" value="<?=htmlspecialchars($name)?>" required>
            <input type="tel" name="phone" placeholder="Phone Number" value="<?=htmlspecialchars($phone)?>" required>
            <textarea name="address" placeholder="Delivery Address" required><?=htmlspecialchars($address)?></textarea>
            
            <button type="submit" name="order">Confirm Order</button>
        </form>
    </div>
<?php endif; ?>

</div>
</body>
</html>
