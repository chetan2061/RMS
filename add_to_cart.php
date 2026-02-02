<?php
/**
 * ADD_TO_CART.PHP - Add Product to Shopping Cart
 * Purpose: Handle adding products to session-based shopping cart
 * Flow: Receive product_id → Add/increment in session → Redirect to menu
 */


// START SESSION
session_start();

// ADD PRODUCT TO CART
if (isset($_POST['product_id'])) {
    $id = $_POST['product_id'];  // Get product ID from form
    
    // Add to cart or increment quantity if already exists
    // Using null coalescing operator (??) to handle undefined array keys
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
}

// REDIRECT BACK TO MENU
header('Location: index.php');
?>
