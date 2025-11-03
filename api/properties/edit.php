<?php
require '../../db.php';


// Only admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location:../../index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$message = '';
$prop = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM property WHERE property_id=?");
    $stmt->execute([$id]);
    $prop = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prop) {
        $message = "❌ Property not found.";
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $property = trim($_POST['property']);
        $address = trim($_POST['address']);
        $rent = floatval($_POST['rent']);

        $imagePath = $prop['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imagePath = 'uploads/' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $imagePath);
            if (!empty($prop['image']) && file_exists('../../' . $prop['image'])) {
                unlink('../../' . $prop['image']);
            }
        }

        $stmt = $pdo->prepare("UPDATE property SET property=?, address=?, rent_amount=?, image=? WHERE property_id=?");
        if ($stmt->execute([$property, $address, $rent, $imagePath, $id])) {
            $message = "✅ Property updated successfully!";
            $stmt = $pdo->prepare("SELECT * FROM property WHERE property_id=?");
            $stmt->execute([$id]);
            $prop = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $message = "❌ Failed to update property.";
        }
    }
} else {
    $message = "❌ Invalid property ID.";
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Edit Property</title>
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
    padding: 15px;
    text-align: center;
    font-size: 22px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.container {
    max-width: 600px;
    background: #fff;
    margin: 40px auto;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 25px;
}

label {
    font-weight: 600;
    color: #333;
    display: block;
    margin-top: 12px;
}

input[type="text"],
input[type="number"],
input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
    transition: 0.2s;
}

input:focus {
    border-color: #3498db;
    box-shadow: 0 0 3px rgba(52,152,219,0.4);
    outline: none;
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

.alert {
    padding: 12px;
    border-radius: 6px;
    font-weight: 500;
    margin-bottom: 20px;
    text-align: center;
}

.alert.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

img.preview {
    display: block;
    width: 150px;
    height: 110px;
    object-fit: cover;
    margin-top: 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
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

<div class="header">🏠 Edit Property</div>

<div class="container">
    <h2>Update Property Details</h2>

    <?php if (!empty($message)): ?>
        <div class="alert <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($prop): ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Property Name:</label>
        <input type="text" name="property" value="<?= htmlspecialchars($prop['property']) ?>" required>

        <label>Address:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($prop['address']) ?>" required>

        <label>Rent Amount (₱):</label>
        <input type="number" step="0.01" name="rent" value="<?= htmlspecialchars($prop['rent_amount']) ?>" required>

        <label>Image (leave empty to keep existing):</label>
        <input type="file" name="image" accept="image/*">

        <?php if (!empty($prop['image']) && file_exists('../../' . $prop['image'])): ?>
            <img src="../../<?= htmlspecialchars($prop['image']) ?>" alt="Property Image" class="preview">
        <?php endif; ?>

        <button type="submit"> Update Property</button>
        <a href="../../Admin/tenants.php" class="back">← Back to Tenants</a>
    </form>
    <?php endif; ?>
</div>

</body>
</html>
