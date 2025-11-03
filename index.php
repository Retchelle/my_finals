<?php
require 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // --- ADMIN LOGIN ---
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && hash('sha256', $password) === $admin['password']) {
        $_SESSION['user_type'] = 'admin';
        $_SESSION['username'] = $admin['username'];
        $_SESSION['admin_id'] = $admin['admin_id'];
        header("Location: admin/dashboard.php");
        exit;
    }

    // --- TENANT LOGIN ---
    $stmt = $pdo->prepare("SELECT * FROM tenant WHERE username = ?");
    $stmt->execute([$username]);
    $tenant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tenant && password_verify($password, $tenant['password'])) {
        $_SESSION['user_type'] = 'tenant';
        $_SESSION['username'] = $tenant['username'];
        $_SESSION['tenant_id'] = $tenant['tenant_id'];
        header("Location: tenant/dashboard.php");
        exit;
    }

    // --- INVALID LOGIN ---
    $message = "Invalid username or password!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
body { font-family: Arial, sans-serif; background: #f5f5f5; }
.container { width: 320px; margin: 100px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
h2 { text-align: center; color: #333; }
input[type=text], input[type=password] { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
button { width: 100%; padding: 10px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
button:hover { background: #45a049; }
p { text-align: center; }
a { color: #333; text-decoration: none; }
</style>
</head>
<body>
<div class="container">
  <h2>Login</h2>
  <form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
  <p style="color:red;"><?= htmlspecialchars($message) ?></p>
  <p>Don't have an account? <a href="signup.php">Sign up</a></p>
</div>
</body>
</html>
