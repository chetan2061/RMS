<?php
require '../includes/session.php';
require '../includes/db.php';

$error = '';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $error = "Invalid request.";
        } else {
            $email = $_POST['email'];
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                $error = "Invalid email or password";
            } else {
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                // Check Password AND Role
                if ($user && password_verify($password, $user['password'])) {
                    if ($user['role'] === 'admin') {
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['role'] = 'admin';
                        header('Location: index.php'); // Redirect to new index
                        exit;
                    } else {
                        $error = "Access denied. Not an admin account.";
                    }
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
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/signup_css.css">

</head>
<body class="admin-auth">

<div class="auth-container">
    <form method="POST" class="auth-form">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <label>Email</label>
        <input type="email" name="email" required placeholder="admin@example.com">

        <label>Password</label>
        <input type="password" name="password" required placeholder="password">

        <button type="submit" name="login">Login as Admin</button>
        
        <p><a href="signup.php">Register New Admin</a></p>
        <p><a href="../login.php">Back to Customer Login</a></p>
    </form>
</div>

</body>
</html>
