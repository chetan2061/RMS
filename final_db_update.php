<?php
require 'includes/db.php';

try {
    // Add full_name
    $sql = "SHOW COLUMNS FROM users LIKE 'full_name'";
    $stmt = $pdo->query($sql);
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN full_name VARCHAR(255) DEFAULT NULL AFTER id");
        echo "Added full_name column to users.<br>";
    }
    
    echo "Final DB Update Completed Successfully.";

} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>
