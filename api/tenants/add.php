<?php
require '../../db.php';

// Only admin can access
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

$errors = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['fname'] ?? '');
    $mname = trim($_POST['mname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $contact = trim($_POST['contact_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$fname || !$lname || !$email || !$username || !$password) {
        $errors = "Please fill in all required fields.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO tenant (fname, mname, lname, contact_number, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$fname, $mname, $lname, $contact, $email, $username, $hashed_password])) {
            $success = "✅ Tenant added successfully!";
            // Clear form fields after success
            $fname = $mname = $lname = $contact = $email = $username = $password = '';
        } else {
            $errors = "❌ Failed to add tenant. Make sure username or email is unique.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Tenant</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f6f7;
    margin: 0;
    padding: 0;
}

.header {
    background: #3498db;
    color: white;
    padding: 15px 25px;
    font-size: 22px;
    font-weight: 600;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.container {
    max-width: 550px;
    margin: 40px auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    padding: 30px 40px;
}

h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 20px;
}

label {
    font-weight: 600;
    color: #333;
    display: block;
    margin-top: 12px;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
    transition: 0.2s;
}

input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 3px rgba(52,152,219,0.4);
}

button {
    width: 100%;
    margin-top: 20px;
    padding: 12px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

button:hover {
    background: linear-gradient(135deg, #2980b9, #1c5980);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.error, .success {
    text-align: center;
    padding: 12px;
    border-radius: 6px;
    font-weight: bold;
    margin: 20px auto;
    max-width: 500px;
}

.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
a.back {
    display: block;
    text-align: center;
    margin-top: 20px;
    background: #7f8c8d;
    color: white;
    text-decoration: none;
    padding: 10px;
    border-radius: 6px;
    font-weight: 600;
    transition: 0.3s;
}
a.back:hover {
    background: #95a5a6;
}
</style>
</head>

<body>

<div class="header"> Add Tenant</div>

<?php if ($errors): ?>
<div class="error"><?= htmlspecialchars($errors) ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="container">
    <h2>Tenant Information</h2>
    <form method="post" action="">
        <label>First Name</label>
        <input type="text" name="fname" required value="<?= htmlspecialchars($fname ?? '') ?>">

        <label>Middle Name</label>
        <input type="text" name="mname" value="<?= htmlspecialchars($mname ?? '') ?>">

        <label>Last Name</label>
        <input type="text" name="lname" required value="<?= htmlspecialchars($lname ?? '') ?>">

        <label>Contact Number</label>
        <input type="text" name="contact_number" value="<?= htmlspecialchars($contact ?? '') ?>">

        <label>Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">

        <label>Username</label>
        <input type="text" name="username" required value="<?= htmlspecialchars($username ?? '') ?>">

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Add Tenant</button>
        <a href="../../Admin/tenants.php" class="back">← Back to Tenants</a>
    </form>
</div>

</body>
</html>
