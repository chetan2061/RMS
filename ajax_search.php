<?php
require 'includes/db.php';

// Get search term
$q = $_GET['q'] ?? '';

if ($q) {
    // Simple query to find matches
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ?");
    $stmt->execute(["%$q%"]);
    
    // Return result as JSON
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
