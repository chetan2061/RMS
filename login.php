<?php
// User login logic
require 'includes/session.php';
require 'includes/db.php';

$error = '';

// Generate security token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    // Process login request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Validate security token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $error = "Invalid request.";
        }
        else {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Check for empty fields
            if (empty($email) || empty($password)) {
                $error = "Invalid email or password";
            }
            else {
                // Look up user by email
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                // Validate password hash
                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    
                    // Store user data in session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];

                    // Direct to appropriate dashboard
                    if ($user['role'] === 'admin') {
                        header('Location: admin/index.php');
                    } else {
                        header('Location: index.php');
                    }
                    exit;
                } else {
                    $error = "Invalid email or password";
                }
            }
        }
    }

} catch (Exception $e) {
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
    <!-- Login form -->
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
    </form>
</div>

</body>
</html>
