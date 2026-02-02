<?php
// Signup logic for new users
session_start();
require 'includes/db.php';

$message = '';

try {
    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate basic email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
        }
        // Ensure password meets minimum length
        elseif (empty($password) || strlen($password) < 6) {
            $message = "Password must be at least 6 characters.";
        }
        // Check if passwords match
        elseif ($password !== $confirm_password) {
            $message = "Passwords do not match.";
        }
        else {
            // Check if account already exists
            $checkSql = "SELECT id FROM users WHERE email = ?";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([$email]);
            
            if ($checkStmt->rowCount() > 0) {
                $message = "Email is already registered.";
            } else {
                // Securely hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert new customer record
                $sql = "INSERT INTO users (email, password, role) VALUES (?, ?, 'customer')";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$email, $hashed_password])) {
                    $message = "User signed up successfully";
                    header('refresh: 2; url=login.php');
                } else {
                    $message = "Failed to create account.";
                }
            }
        }
    }

} catch (Exception $e) {
    $message = "Something went wrong: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup - Cups and Mugs</title>
    <link rel="stylesheet" href="css/signup_css.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="user-auth">

<div class="auth-container">
    <!-- Signup form container -->
    <form method="POST" class="auth-form">
        <h2>Create Account</h2>
        <?php if ($message): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <label>Email</label>
        <input type="email" name="email" required placeholder="email@example.com">

        <label>Password</label>
        <input type="password" name="password" required placeholder="Create a password">
        
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required placeholder="Confirm password">

        <button type="submit" name="signup">Sign Up</button>
        
        <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
</div>

</body>
</html>
