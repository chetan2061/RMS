<?php

$host = 'localhost';
$database   = 'np03cy4a24005';
$username = 'np03cy4a240058';
$password = 'dnW3yFwNLp';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => true,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$database",
        $username,
        $password,
        $options
    );
} catch (PDOException $e) {
    die('Database connection failed.');
}
?>
