<?php
require '../db.php'; // db.php already starts session

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'tenant') {
    header('Location: login.php');
    exit;
}

$tenant_id = (int)$_SESSION['tenant_id'];

// Fetch properties assigned to this tenant
$stmt = $pdo->prepare("
    SELECT p.property_id, p.address
    FROM property p
    INNER JOIN rent r ON p.property_id = r.property_id
    WHERE r.tenant_id = ?
");
$stmt->execute([$tenant_id]);
$properties = $stmt->fetchAll();

// Fetch maintenance requests
$stmt = $pdo->prepare("
    SELECT mr.*, p.address 
    FROM maintenance_request mr 
    JOIN property p ON mr.property_id = p.property_id
    WHERE mr.tenant_id = ? 
    ORDER BY mr.request_date DESC
");
$stmt->execute([$tenant_id]);
$requests = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = (int)($_POST['property_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($property_id && $description) {
        $stmt = $pdo->prepare("
            INSERT INTO maintenance_request (tenant_id, property_id, description, status, request_date)
            VALUES (?, ?, ?, 'Pending', NOW())
        ");
        $stmt->execute([$tenant_id, $property_id, $description]);
        header("Location: maintenance_request.php?success=1");
        exit;
    } else {
        $error = "⚠️ Please select a property and enter a description.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tenant | Maintenance Requests</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
/* Extra design for footer + some enhancements */










</style>
</head>
<body>
<div class="dashboard-container">

  <!-- Sidebar -->
  <aside class="sidebar">
    <h2>🏠 Tenant Portal</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="payment_history.php">Payments</a>
    <a href="maintenance_request.php" class="active">Maintenance</a>
    <a href="../logout.php" class="logout">Logout</a>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <div class="header">
      Maintenance Requests
    </div>

    <div class="container">
      <?php if (!empty($error)): ?>
        <p class="message error"><?= htmlspecialchars($error) ?></p>
      <?php elseif (isset($_GET['success'])): ?>
        <p class="message success">✅ Maintenance request submitted successfully!</p>
      <?php endif; ?>

      <h2>Submit New Request</h2>
      <form method="POST">
        <label for="property">Select Property:</label>
        <select name="property_id" id="property" required>
          <option value="">-- Select Property --</option>
          <?php foreach ($properties as $prop): ?>
            <option value="<?= $prop['property_id'] ?>"><?= htmlspecialchars($prop['address']) ?></option>
          <?php endforeach; ?>
        </select>

        <label for="description">Describe your issue:</label>
        <textarea name="description" id="description" rows="4" placeholder="Describe your maintenance issue..." required></textarea>

        <button type="submit">Submit Request</button>
      </form>

      <h3>Previous Requests</h3>
      <table>
        <tr>
          <th>Date</th>
          <th>Property</th>
          <th>Description</th>
          <th>Status</th>
        </tr>
        <?php if ($requests): ?>
          <?php foreach ($requests as $req): ?>
          <tr>
            <td><?= htmlspecialchars($req['request_date']) ?></td>
            <td><?= htmlspecialchars($req['address']) ?></td>
            <td><?= htmlspecialchars($req['description']) ?></td>
            <td class="status-<?= strtolower(str_replace(' ', '-', $req['status'])) ?>">
              <?= htmlspecialchars($req['status']) ?>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="4" style="text-align:center;">No maintenance requests yet.</td></tr>
        <?php endif; ?>
      </table>
    </div>

   <!-- ✅ FOOTER -->
   <footer class="footer">
      <p>© <?= date('Y') ?> Rental Property Management System</p>
    </footer>
  </main>
</div>

</body>
</html>
