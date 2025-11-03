<?php
require '../db.php';  // Make sure config.php includes session_start() and $pdo

// Ensure tenant is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'tenant') {
    header('Location: login.php');
    exit;
}

$tenant_id = (int)$_SESSION['tenant_id'];
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = (int)($_POST['property_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($property_id > 0 && $description !== '') {
        $stmt = $pdo->prepare("INSERT INTO maintenance_request (tenant_id, property_id, request_date, description, status) VALUES (?, ?, CURDATE(), ?, 'Pending')");
        if ($stmt->execute([$tenant_id, $property_id, $description])) {
            $message = "✅ Maintenance request submitted successfully!";
        } else {
            $message = "⚠️ Failed to submit request. Please try again.";
        }
    } else {
        $message = "⚠️ Please select a property and enter a description.";
    }
}

// Fetch properties assigned to this tenant
$stmt = $pdo->prepare("
    SELECT p.property_id, p.address 
    FROM property p
    INNER JOIN rent r ON p.property_id = r.property_id
    WHERE r.tenant_id = ?
");
$stmt->execute([$tenant_id]);
$propertyList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Submit Maintenance Request</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
.container { max-width: 500px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 5px #aaa; }
input, select, textarea, button { width: 100%; padding: 10px; margin: 5px 0; border-radius: 5px; border: 1px solid #ccc; }
button { background: #4CAF50; color: #fff; border: none; cursor: pointer; }
button:hover { background: #45a049; }
.message { margin-bottom: 10px; padding: 10px; border-radius: 5px; }
.success { background: #d4edda; color: #155724; }
.error { background: #f8d7da; color: #721c24; }
</style>
</head>
<body>
<div class="container">
    <h2>Submit Maintenance Request</h2>

    <?php if ($message): ?>
        <p class="message <?= strpos($message,'✅') === 0 ? 'success' : 'error' ?>"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="submit.php">
        <label for="property">Select Property:</label>
        <select name="property_id" id="property" required>
            <option value="">-- Select Property --</option>
            <?php foreach($propertyList as $prop): ?>
                <option value="<?= $prop['property_id'] ?>"><?= htmlspecialchars($prop['address']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="description">Request Description:</label>
        <textarea name="description" id="description" rows="4" placeholder="Enter request details" required></textarea>

        <button type="submit">Submit Request</button>
    </form>
</div>
</body>
</html>
