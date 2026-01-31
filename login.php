<?php

require 'includes/session.php';
require 'includes/db.php';

$error = '';

// Generating CSRF token if not exists to protect against Cross-Site Request Forgery
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $error = "Invalid request.";
        }
        else {
            // Sanitize and retrieve input
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Validate input is not empty
            if (empty($email) || empty($password)) {
                $error = "Invalid email or password";
            }
            else {
                // Use prepared statement to prevent SQL injection
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                // Verify password hash
                if ($user && password_verify($password, $user['password'])) {
                    // Regenerate session ID to prevent session fixation attacks
                    session_regenerate_id(true);
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role']; // Store role in session

                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        header('Location: admin/index.php'); // Fixed path to admin index
                    } else {
                        header('Location: index.php');
                    }
                    exit;
                } else {
                    // Generic error message to prevent account enumeration
                    $error = "Invalid email or password";
                }
            }
        }
    }

} catch (Exception $e) {
    // Handle database or other errors
    $error = "Something went wrong.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Cups and Mugs</title>
    <link rel="stylesheet" href="css/signup_css.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="user-auth">

<div class="auth-container">
    <form method="POST" class="auth-form">
        <h2>Welcome Back</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <label>Email</label>
        <input type="email" name="email" required placeholder="Enter your email">

        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter your password">

        <button type="submit" name="login">Login</button>
        
        <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        <!-- <p><a href="admin/login.php" style="font-size: 0.8em; opacity: 0.7;">Admin Login</a></p> -->
    </form>
</div>

</body>
</html>
