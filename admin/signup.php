<?php
require '../includes/db.php';

$message = '';
$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Simple validation
        if (empty($email) || empty($password)) {
            $error = "All fields are required.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters.";
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email already registered.";
            } else {
                // Create Admin
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = 'admin';

                $sql = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$email, $hashed_password, $role])) {
                    $message = "Admin account created successfully! Redirecting to login...";
                    header("refresh:2;url=login.php");
                } else {
                    $error = "Registration failed.";
                }
            }
        }
    }
} catch (Exception $e) {
    $error = "Something went wrong: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../css/signup_css.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="admin-auth">

<div class="auth-container">
    <form method="POST" class="auth-form">
        <h2>Register New Admin</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($message): ?>
            <p style="color: green; text-align: center;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        
        <label>Email</label>
        <input type="email" name="email" required placeholder="email">

        <label>Password</label>
        <input type="password" name="password" required placeholder="password">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required placeholder="confirm password">

        <button type="submit" name="signup">Register Admin</button>
        
        <p><a href="login.php">Back to Admin Login</a></p>
    </form>
</div>

</body>
</html>
