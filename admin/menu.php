<?php
// Admin tool for managing product menu items
require '../includes/session.php';
require '../includes/db.php';

// Authorization check for admin users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Process item deletion request
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: menu.php');
    exit;
}

// Initialize variables for edit mode
$edit_mode = false;
$edit_id = null;
$name = '';
$desc = '';
$price = '';
$existing_image = '';

// Check if we are editing
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$edit_id]);
    $product = $stmt->fetch();
    if ($product) {
        $name = $product['name'];
        $desc = $product['description'];
        $price = $product['price'];
        $existing_image = $product['image_url'];
    }
}

// Process form submission (Add or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $edit_id = $_POST['edit_id'] ?? null;
    
    // Handle product image file upload
    $image_path = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_path = 'uploads/' . $filename;
        }
    }

    if (!empty($name) && !empty($price)) {
        if ($edit_id) {
            // Update existing product
            $sql = "UPDATE products SET name = ?, description = ?, price = ?, image_url = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $desc, $price, $image_path, $edit_id]);
        } else {
            // Insert new product
            $sql = "INSERT INTO products (name, description, price, image_url) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $desc, $price, $image_path]);
        }
        header('Location: menu.php');
        exit;
    }
}

// Fetch all products for management table
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Menu</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="dashboard-container">
    <!-- Shared admin navigation -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="main-header">
            <h2>Menu Management</h2>
        </div>

        <div class="container" style="width: 100%; padding: 0;">
            <!-- Form to add/edit products -->
            <div class="card" style="margin-bottom: 30px; padding: 25px;">
                <h3 style="margin-bottom: 15px;"><?php echo $edit_mode ? 'Edit Item' : 'Add New Item'; ?></h3>
                <form method="POST" enctype="multipart/form-data" style="display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                    <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo $existing_image; ?>">
                    
                    <input type="text" name="name" placeholder="Item Name" value="<?php echo htmlspecialchars($name); ?>" required>
                    <input type="text" name="price" placeholder="Price (Rs)" value="<?php echo htmlspecialchars($price); ?>" required>
                    <input type="text" name="description" placeholder="Description" value="<?php echo htmlspecialchars($desc); ?>">
                    <input type="file" name="image">
                    
                    <button type="submit" class="btn-add">
                        <?php echo $edit_mode ? 'Update Item' : 'Add Item'; ?>
                    </button>
                    
                    <?php if ($edit_mode): ?>
                        <a href="menu.php" style="display:flex; align-items:center; justify-content:center; text-decoration:none; color:#777;">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table listing existing products -->
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;">Image</th>
                            <th>Details</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <?php if($p['image_url']): ?>
                            <img src="../<?php echo htmlspecialchars($p['image_url']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                        <?php else: ?>
                            <span style="color:#999; font-size:0.8em;">No Img</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($p['name']); ?></strong><br>
                        <small style="color: #777;"><?php echo htmlspecialchars($p['description']); ?></small>
                    </td>
                    <td>Rs. <?php echo number_format($p['price']); ?></td>
                    <td>
                        <div style="display: flex; gap: 10px;">
                            <a href="menu.php?edit=<?php echo $p['id']; ?>" style="color: #3498db; font-weight: bold;">
                                Edit
                            </a>
                            <a href="menu.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Delete this item?')" style="color: #e74c3c; font-weight: bold;">
                                Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    </div>
    </div>
</div>

</body>
</html>
