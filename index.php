<?php
// Main landing page for the coffee shop
require 'includes/session.php';
require 'includes/db.php';

// Check login status and search parameters
$is_logged_in = isset($_SESSION['user_id']);
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$search_query = trim($_GET['search'] ?? '');

// Search Logic
$search = $_GET['search'] ?? '';
$products = [];

if ($search) {
    // Search for products matching name or description
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
    $products = $stmt->fetchAll();
} else {
    // Show all products if no search
    $products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
}

// Handle user logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
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

<!-- Site navigation -->
<nav class="navbar">
    <!-- Left Side: Logo -->
    <div style="display: flex; align-items: center;">
        <div class="logo">Cups and Mugs</div>
    </div>
    
    <!-- Right Side: Menu, Cart, Login -->
    <div class="nav-links">
        <a href="index.php" class="active">Menu</a>
        <a href="cart.php">Cart <span class="badge"><?php echo $cart_count; ?></span></a>
        <?php if ($is_logged_in): ?>
            <a href="index.php?logout=1" style="color: #e74c3c;">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<!-- Main shop container -->
<div class="container">
    
    <!-- Hero Section -->
    <header class="hero">
        <h1>Welcome to Cups and Mugs</h1>
        <p>Best Coffee and Food in Baneshwor</p>
    </header>

    <!-- Search interface -->
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

    <!-- Product display grid -->
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
                        
                        <!-- Product reviews -->
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

                            <!-- Submit a new review -->
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
<!-- AJAX Search Script -->
<script>
const searchInput = document.querySelector('input[name="search"]');
const grid = document.querySelector('.menu-grid');

searchInput.addEventListener('keyup', function() {
    let term = this.value;
    
    // Only search if 3 or more characters
    if (term.length > 2) {
        fetch('ajax_search.php?q=' + term)
            .then(response => response.json())
            .then(products => {
                if (products.length > 0) {
                    let html = '';
                    products.forEach(p => {
                        html += `
                        <div class="menu-item">
                            <img src="${p.image_url || 'placeholder.jpg'}" alt="${p.name}">
                            <div class="item-details">
                                <h3>${p.name}</h3>
                                <p>${p.description}</p>
                                <div class="price-action">
                                    <span class="price">Rs. ${p.price}</span>
                                    <form action="add_to_cart.php" method="POST">
                                        <input type="hidden" name="product_id" value="${p.id}">
                                        <button class="btn-add">Add</button>
                                    </form>
                                </div>
                            </div>
                        </div>`;
                    });
                    grid.innerHTML = html;
                }
            });
    }
});
</script>
</body>
</html>
