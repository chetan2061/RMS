<?php
/**
 * INDEX.PHP - Main Menu Page
 * Purpose: Display all menu items, allow searching, adding to cart, and submitting reviews
 * Flow: User browses products → Can search → Add to cart → Submit reviews (if logged in)
 */

// ============================================
// INITIALIZE SESSION AND DATABASE
// ============================================
require 'includes/session.php';  // Start session
require 'includes/db.php';       // Connect to database

// ============================================
// CHECK USER LOGIN STATUS
// ============================================
$is_logged_in = isset($_SESSION['user_id']);  // Check if user is logged in
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;  // Count items in cart

// ============================================
// HANDLE SEARCH FUNCTIONALITY
// ============================================
$search_query = '';  // Initialize search query
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);  // Get search term from URL
}

// ============================================
// FETCH PRODUCTS FROM DATABASE
// ============================================
if (!empty($search_query)) {
    // Search products by name or description
    $sql = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $search_term = '%' . $search_query . '%';  // Add wildcards for partial matching
    $stmt->execute([$search_term, $search_term]);
    $products = $stmt->fetchAll();
} else {
    // Get all products if no search
    $sql = "SELECT * FROM products ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll();
}

// ============================================
// HANDLE LOGOUT
// ============================================
if (isset($_GET['logout'])) {
    session_destroy();  // Destroy session
    header('Location: login.php');  // Redirect to login
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cups and Mugs - Baneshwor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- ============================================ -->
<!-- NAVIGATION BAR -->
<!-- ============================================ -->
<nav class="navbar">
    <!-- Left Side: Profile Link + Logo -->
    <div style="display: flex; align-items: center;">
        <?php if ($is_logged_in): ?>
            <a href="profile.php" style="margin-right: 20px; color: #6f4e37; font-weight: bold;">
                <i class="fas fa-user-circle"></i> Profile
            </a>
        <?php endif; ?>
        <div class="logo">Cups and Mugs</div>
    </div>
    
    <!-- Right Side: Menu, Cart, Login -->
    <div class="nav-links">
        <a href="home.php" class="active">Menu</a>
        <a href="cart.php">Cart <span class="badge"><?php echo $cart_count; ?></span></a>
        <?php if (!$is_logged_in): ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<!-- ============================================ -->
<!-- MAIN CONTENT CONTAINER -->
<!-- ============================================ -->
<div class="container">
    
    <!-- Hero Section -->
    <header class="hero">
        <h1>Welcome to Cups and Mugs</h1>
        <p>Best Coffee and Food in Baneshwor</p>
    </header>

    <!-- ============================================ -->
    <!-- SEARCH BAR -->
    <!-- ============================================ -->
    <div style="max-width: 600px; margin: 30px auto;">
        <form method="GET" action="home.php" style="display: flex; gap: 10px;">
            <input type="text" 
                   name="search" 
                   placeholder="Search menu items..." 
                   value="<?php echo htmlspecialchars($search_query); ?>"
                   style="flex: 1; padding: 12px 20px; border: 2px solid #ddd; border-radius: 30px; font-size: 1rem;">
            <button type="submit" 
                    style="padding: 12px 30px; background: #4e342e; color: white; border: none; border-radius: 30px; cursor: pointer; font-weight: bold;">
                <i class="fas fa-search"></i> Search
            </button>
            <?php if (!empty($search_query)): ?>
                <a href="index.php" 
                   style="padding: 12px 20px; background: #95a5a6; color: white; border-radius: 30px; text-decoration: none; font-weight: bold;">
                    Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Show search results count -->
    <?php if (!empty($search_query)): ?>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Found <?php echo count($products); ?> result(s) for "<?php echo htmlspecialchars($search_query); ?>"
        </p>
    <?php endif; ?>

    <!-- ============================================ -->
    <!-- MENU GRID - Display All Products -->
    <!-- ============================================ -->
    <div class="menu-grid">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <!-- Single Menu Item Card -->
                <div class="menu-item">
                    <!-- Product Image -->
                    <img src="<?php echo htmlspecialchars($product['image_url'] ?: 'placeholder.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                    
                    <!-- Product Details -->
                    <div class="item-details">
                        <!-- Product Name -->
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        
                        <!-- Product Description -->
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        
                        <!-- Price and Add to Cart Button -->
                        <div class="price-action">
                            <span class="price">Rs. <?php echo number_format($product['price'], 2); ?></span>
                            
                            <!-- Add to Cart Form -->
                            <form action="add_to_cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn-add">Add to Cart</button>
                            </form>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- REVIEWS SECTION -->
                        <!-- ============================================ -->
                        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px dashed #ddd;">
                            <small style="font-weight: bold; color: #6f4e37;">Reviews:</small>
                            
                            <?php
                            // Fetch recent reviews for this product
                            $stmt = $pdo->prepare("SELECT rating, comment FROM reviews WHERE product_id = ? ORDER BY created_at DESC LIMIT 2");
                            $stmt->execute([$product['id']]);
                            $reviews = $stmt->fetchAll();
                            ?>
                            
                            <!-- Display Reviews -->
                            <?php if (count($reviews) > 0): ?>
                                <ul style="list-style: none; padding: 0; margin: 5px 0;">
                                    <?php foreach ($reviews as $review): ?>
                                        <li style="font-size: 0.85em; color: #555; margin-bottom: 3px;">
                                            <!-- Star Rating -->
                                            <span style="color: #f39c12;">
                                                <?php for($i=0; $i<$review['rating']; $i++) echo '★'; ?>
                                            </span>
                                            <!-- Review Comment -->
                                            <?php echo htmlspecialchars($review['comment']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p style="font-size: 0.8em; color: #999;">No reviews yet.</p>
                            <?php endif; ?>

                            <!-- ============================================ -->
                            <!-- WRITE REVIEW (Only for Logged In Users) -->
                            <!-- ============================================ -->
                            <?php if ($is_logged_in): ?>
                                <!-- Button to Show Review Form -->
                                <button onclick="document.getElementById('review-form-<?php echo $product['id']; ?>').style.display = 'block'" 
                                        style="background: none; border: none; color: #2980b9; cursor: pointer; font-size: 0.8em; padding: 0; text-decoration: underline;">
                                    Write a Review
                                </button>
                                
                                <!-- Review Form (Hidden by Default) -->
                                <form id="review-form-<?php echo $product['id']; ?>" 
                                      action="submit_review.php" 
                                      method="POST" 
                                      style="display: none; margin-top: 10px;">
                                    
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    
                                    <!-- Rating Dropdown -->
                                    <select name="rating" required style="padding: 5px; width: 100%; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 4px;">
                                        <option value="">Rate...</option>
                                        <option value="5">5 - Excellent</option>
                                        <option value="4">4 - Very Good</option>
                                        <option value="3">3 - Good</option>
                                        <option value="2">2 - Fair</option>
                                        <option value="1">1 - Poor</option>
                                    </select>
                                    
                                    <!-- Comment Textarea -->
                                    <textarea name="comment" 
                                              placeholder="Your review..." 
                                              rows="2" 
                                              style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                                    
                                    <!-- Submit Button -->
                                    <button type="submit" 
                                            style="font-size: 0.8em; padding: 5px; background-color: #333; margin-top: 5px;">
                                        Submit
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- No Products Found -->
            <p style="text-align: center; color: #999; grid-column: 1/-1;">
                <?php if (!empty($search_query)): ?>
                    No menu items found for "<?php echo htmlspecialchars($search_query); ?>"
                <?php else: ?>
                    No menu items available yet.
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================ -->
<!-- JAVASCRIPT -->
<!-- ============================================ -->
<script src="js/script.js"></script>
</body>
</html>
