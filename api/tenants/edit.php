<?php
require '../../db.php';

// ✅ Allow only admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die("Invalid tenant ID.");
}

// ✅ Fetch tenant
$stmt = $pdo->prepare("SELECT * FROM tenant WHERE tenant_id = ?");
$stmt->execute([$id]);
$tenant = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tenant) die("Tenant not found.");

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['fname'] ?? '');
    $mname = trim($_POST['mname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $contact = trim($_POST['contact_number'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($fname && $lname && $email) {
        $stmt = $pdo->prepare("UPDATE tenant SET fname=?, mname=?, lname=?, contact_number=?, email=? WHERE tenant_id=?");
        if ($stmt->execute([$fname, $mname, $lname, $contact, $email, $id])) {
            $message = "✅ Tenant updated successfully!";
            // Refresh tenant data
            $stmt = $pdo->prepare("SELECT * FROM tenant WHERE tenant_id = ?");
            $stmt->execute([$id]);
            $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $message = "❌ Failed to update tenant.";
        }
    } else {
        $message = "⚠️ First name, last name, and email are required.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Edit Tenant</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f6f7;
    margin: 0;
    padding: 0;
}

/* ✅ Header */
.header {
    background: #3498db;
    color: white;
    text-align: center;
    padding: 15px;
    font-size: 22px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* ✅ Card Container */
.container {
    max-width: 550px;
    background: white;
    margin: 50px auto;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* ✅ Titles */
h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 20px;
}

/* ✅ Labels & Inputs */
label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

input[type="text"],
input[type="email"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 15px;
    transition: 0.3s;
}

input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 3px rgba(52,152,219,0.4);
}

/* ✅ Button */
button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: linear-gradient(135deg, #2980b9, #1f6390);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

/* ✅ Message Styles */
.message {
    padding: 12px;
    border-radius: 6px;
    text-align: center;
    margin-bottom: 15px;
    font-weight: 600;
    color: white;
}

.success { background: #2ecc71; }
.error { background: #e74c3c; }
.warning { background: #f1c40f; color: #000; }

/* ✅ Back link */
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

<div class="header">Edit Tenant</div>

<div class="container">
    <h2>Tenant Information</h2>

    <?php if ($message): ?>
        <div class="message 
            <?= strpos($message, '✅') !== false ? 'success' : 
               (strpos($message, '❌') !== false ? 'error' : 'warning') ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>First Name:</label>
        <input type="text" name="fname" value="<?= htmlspecialchars($tenant['fname']) ?>" required>

        <label>Middle Name:</label>
        <input type="text" name="mname" value="<?= htmlspecialchars($tenant['mname']) ?>">

        <label>Last Name:</label>
        <input type="text" name="lname" value="<?= htmlspecialchars($tenant['lname']) ?>" required>

        <label>Contact Number:</label>
        <input type="text" name="contact_number" value="<?= htmlspecialchars($tenant['contact_number']) ?>">

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($tenant['email']) ?>" required>

        <button type="submit"> Update Tenant</button>
    </form>

    <a href="../../Admin/tenants.php" class="back">← Back to Tenants</a>
</div>

</body>
</html>
