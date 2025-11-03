<?php
require '../../db.php';

// Only admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location:../../index.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property = trim($_POST['property']);
    $address = trim($_POST['address']);
    $rent = floatval($_POST['rent']);

    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imagePath = 'uploads/' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $imagePath);
    }

    // Insert into DB
    $stmt = $pdo->prepare("INSERT INTO property (property, address, rent_amount, image) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$property, $address, $rent, $imagePath])) {
        $message = "✅ Property added successfully!";
    } else {
        $message = "❌ Failed to add property.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Add Property</title>
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
    text-align: center;
    padding: 15px;
    font-size: 22px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.container {
    max-width: 550px;
    background: white;
    margin: 50px auto;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 20px;
}

label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

input[type="text"],
input[type="number"],
input[type="file"] {
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

.message {
    padding: 12px;
    border-radius: 6px;
    text-align: center;
    margin-bottom: 15px;
    font-weight: 600;
    color: white;
}

.success {
    background: #2ecc71;
}

.error {
    background: #e74c3c;
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

<div class="header">🏡 Add Property</div>

<div class="container">
    <h2>Property Information</h2>

    <?php if($message): ?>
    <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Property Name:</label>
        <input type="text" name="property" required>

        <label>Address:</label>
        <input type="text" name="address" required>

        <label>Rent Amount (₱):</label>
        <input type="number" step="0.01" name="rent" required>

        <label>Upload Image:</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Add Property</button>
        <a href="../../Admin/tenants.php" class="back">← Back to Tenants</a>
    </form>
</div>

</body>
</html>
