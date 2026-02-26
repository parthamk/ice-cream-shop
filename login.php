<?php
session_start();
require 'db.php';

// Auto-create a default admin if the table is completely empty
$stmtCheck = $pdo->query("SELECT COUNT(*) FROM admins");
if ($stmtCheck->fetchColumn() == 0) {
    $defaultHash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->query("INSERT INTO admins (username, password_hash) VALUES ('admin', '$defaultHash')");
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    // Verify the hashed password
    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Create session variables
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Frosty Bites</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container { max-width: 400px; margin: 10vh auto; background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: center; }
        .login-container h2 { color: var(--primary-color); margin-bottom: 1.5rem; }
        .login-container i.bi-shield-lock { font-size: 3rem; color: var(--primary-color); }
        .form-group { margin-bottom: 1.5rem; text-align: left; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: var(--text-dark); }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none; }
        .form-group input:focus { border-color: var(--primary-color); }
        .btn-login { width: 100%; padding: 10px; font-size: 1.1rem; }
        .error-message { color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="login-container">
        <i class="bi bi-shield-lock"></i>
        <h2>Admin Login</h2>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
        </form>
        <p style="margin-top: 15px; font-size: 0.8rem; color: #888;">Default login: admin / admin123</p>
    </div>
</body>
</html>
