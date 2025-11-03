<?php
// Include the database connection safely
require __DIR__ . '/db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname']);
    $lname = trim($_POST['lname']);
    $contact_number = trim($_POST['contact_number']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM tenant WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);

    if ($stmt->rowCount() > 0) {
        $message = "Username or Email already exists!";
    } else {
        // Insert new tenant
        $stmt = $pdo->prepare("INSERT INTO tenant (fname, mname, lname, contact_number, username, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$fname, $mname, $lname, $contact_number, $username, $email, $password])) {
            $message = "Registration successful! You can now log in.";
        } else {
            $message = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tenant Sign Up</title>
<style>
body { font-family: Arial; background: #f5f5f5; }
.container { width: 350px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 5px gray; }
input { width: 100%; padding: 8px; margin: 5px 0; }
button { width: 100%; padding: 8px; background: #4CAF50; color: white; border: none; cursor: pointer; }
button:hover { background: #45a049; }
a { text-decoration: none; color: #333; }
.message { color: green; margin: 10px 0; }
</style>
</head>
<body>
<div class="container">
  <h2>Tenant Sign Up</h2>
  <form method="POST">
    <input type="text" name="fname" placeholder="First Name" required>
    <input type="text" name="mname" placeholder="Middle Name">
    <input type="text" name="lname" placeholder="Last Name" required>
    <input type="text" name="contact_number" placeholder="Contact Number" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign Up</button>
  </form>
  <?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>
  <p>Already have an account? <a href="index.php">Login</a></p>
</div>
</body>
</html>
