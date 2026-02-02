<?php
// Simple database setup script
require 'includes/db.php';

try {
    // Create reviews table with foreign keys
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        product_id INT,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        comment TEXT,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    // Define extra columns for orders table
    $cols = [
        'customer_name' => 'VARCHAR(255) DEFAULT NULL',
        'customer_phone' => 'VARCHAR(50) DEFAULT NULL',
        'delivery_location' => 'VARCHAR(255) DEFAULT NULL',
        'order_details' => 'TEXT DEFAULT NULL'
    ];

    // Add columns if they don't exist
    foreach ($cols as $col => $def) {
        $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE '$col'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN $col $def");
        }
    }

    // Clean up unnecessary tables
    $pdo->exec("DROP TABLE IF EXISTS order_items");

    echo "Final DB Setup Completed Successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
