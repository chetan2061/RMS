<?php
require '../includes/session.php';
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: menu.php');
    exit;
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    
    // Image Upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_path = 'uploads/' . $filename;
        }
    }

    if (!empty($name) && !empty($price)) {
        $sql = "INSERT INTO products (name, description, price, image_url) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $desc, $price, $image_path]);
        header('Location: menu.php');
        exit;
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
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
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="main-header">
            <h2>Menu Management</h2>
        </div>

        <div class="container" style="width: 100%; padding: 0;">
            <!-- Add Form -->
            <div class="card" style="margin-bottom: 30px; padding: 25px;">
                <h3 style="margin-bottom: 15px;">Add New Item</h3>
                <form method="POST" enctype="multipart/form-data" style="display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                    <input type="text" name="name" placeholder="Item Name" required>
                    <input type="text" name="price" placeholder="Price (Rs)" required>
                    <input type="text" name="description" placeholder="Description (e.g. delicious burger)">
                    <input type="file" name="image">
                    <button type="submit" class="btn-add">Add Item</button>
                </form>
            </div>

            <!-- List -->
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
                        <a href="menu.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Delete this item?')" style="color: #e74c3c; font-weight: bold;">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    </div> <!-- End Container -->
    </div> <!-- End Main Content -->
</div> <!-- End Dashboard Container -->

</body>
</html>
