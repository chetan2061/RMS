<?php
/**
 * CART.PHP - Shopping Cart and Checkout Page
 * Purpose: Display cart items, allow removal, and handle order placement
 * Flow: View cart → Remove items (optional) → Fill checkout form → Place order
 */

// ============================================
// INITIALIZE SESSION AND DATABASE
// ============================================
session_start();
require 'includes/db.php';

// ============================================
// HANDLE REMOVE ITEM FROM CART
// ============================================
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);  // Remove item from cart
    header('Location: cart.php');  // Refresh page
    exit;
}

// ============================================
// HANDLE ORDER PLACEMENT
// ============================================
if (isset($_POST['order'])) {
    $items = '';  // String to store order details
    $total = 0;   // Total price
    
    // Get cart items from database
    if (!empty($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);  // Get all product IDs in cart
        
        // Create SQL with placeholders for each ID
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")");
        $stmt->execute($ids);
        
        // Build order details and calculate total
        foreach ($stmt->fetchAll() as $p) {
            $qty = $_SESSION['cart'][$p['id']];  // Get quantity from cart
            $items .= $p['name'] . ' x' . $qty . ', ';  // Add to order details string
            $total += $p['price'] * $qty;  // Add to total
        }
    }
    
    // Insert order into database
    $user_id = $_SESSION['user_id'] ?? null;  // Get user ID if logged in
    $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, delivery_location, total_price, order_details, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')")
        ->execute([$user_id, $_POST['name'], $_POST['phone'], $_POST['address'], $total, $items]);
    
    // Clear cart and show success
    unset($_SESSION['cart']);
    $done = 1;  // Flag to show success message
}

// ============================================
// GET CART ITEMS FOR DISPLAY
// ============================================
$cart = [];  // Array to store cart items
$total = 0;  // Total price

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);  // Get all product IDs
    
    // Fetch products from database
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")");
    $stmt->execute($ids);
    
    // Build cart array with product details
    foreach ($stmt->fetchAll() as $p) {
        $qty = $_SESSION['cart'][$p['id']];  // Get quantity
        $cart[] = [
            'id' => $p['id'],
            'name' => $p['name'],
            'price' => $p['price'],
            'qty' => $qty
        ];
        $total += $p['price'] * $qty;  // Calculate total
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Cart</title>
<link rel="stylesheet" href="css/style.css">
<!-- Inline CSS for cart page -->
<style>
.cart{max-width:800px;margin:40px auto;padding:20px}
.box{background:#fff;padding:25px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:15px}
.item{display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid #eee}
.total{background:#4e342e;color:#fff;padding:15px;border-radius:8px;text-align:center;font-size:1.3rem;margin-top:15px}
input,textarea{width:100%;padding:10px;margin:8px 0;border:1px solid #ddd;border-radius:5px;box-sizing:border-box}
button{width:100%;padding:12px;background:#4e342e;color:#fff;border:none;border-radius:5px;cursor:pointer;font-size:1rem}
.empty{text-align:center;padding:50px}
.btn{display:inline-block;padding:10px 20px;background:#4e342e;color:#fff;text-decoration:none;border-radius:5px}
</style>
</head>
<body>

<!-- ============================================ -->
<!-- NAVIGATION BAR -->
<!-- ============================================ -->
<nav class="navbar">
    <div class="logo">RMS</div>
    <div class="nav-links">
        <a href="index.php">Menu</a>
        <a href="cart.php">Cart (<?=count($_SESSION['cart']??[])?>)</a>
    </div>
</nav>

<!-- ============================================ -->
<!-- MAIN CART CONTAINER -->
<!-- ============================================ -->
<div class="cart">

<!-- ============================================ -->
<!-- ORDER SUCCESS STATE -->
<!-- ============================================ -->
<?php if(isset($done)):?>
<div class="box empty">
    <h2>✓ Order Placed!</h2>
    <p>Call: +977 9741706333</p>
    <a href="index.php" class="btn">Menu</a>
</div>

<!-- ============================================ -->
<!-- EMPTY CART STATE -->
<!-- ============================================ -->
<?php elseif(empty($cart)):?>
<div class="box empty">
    <h2>Cart Empty</h2>
    <a href="index.php" class="btn">Browse Menu</a>
</div>

<!-- ============================================ -->
<!-- CART WITH ITEMS -->
<!-- ============================================ -->
<?php else:?>

<!-- Cart Items Box -->
<div class="box">
<h2>Cart</h2>
<?php foreach($cart as $i):?>
<!-- Single Cart Item -->
<div class="item">
    <div>
        <b><?=$i['name']?></b><br>
        <small>Qty: <?=$i['qty']?> × Rs.<?=$i['price']?></small>
    </div>
    <div>
        <b>Rs.<?=$i['price']*$i['qty']?></b>
        <!-- Remove Button -->
        <a href="?remove=<?=$i['id']?>" style="color:red;margin-left:10px">×</a>
    </div>
</div>
<?php endforeach;?>

<!-- Total Display -->
<div class="total">Total: Rs.<?=$total?></div>
</div>

<!-- ============================================ -->
<!-- CHECKOUT FORM -->
<!-- ============================================ -->
<div class="box">
<h2>Checkout</h2>
<form method="POST">
    <!-- Customer Name -->
    <input type="text" name="name" placeholder="Name" required>
    
    <!-- Phone Number -->
    <input type="tel" name="phone" placeholder="Phone" required>
    
    <!-- Delivery Address -->
    <textarea name="address" placeholder="Address" required></textarea>
    
    <!-- Submit Button -->
    <button name="order">Place Order</button>
</form>
</div>

<?php endif;?>
</div>

</body>
</html>
