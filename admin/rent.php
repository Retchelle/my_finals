<?php
require '../db.php';


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Fetch tenants & properties
$tenants = $pdo->query("SELECT tenant_id, fname, mname, lname FROM tenant ORDER BY lname ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch properties including property name and rent_amount
$properties = $pdo->query("SELECT property_id, property, rent_amount FROM property ORDER BY property ASC")->fetchAll(PDO::FETCH_ASSOC);

$message = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_rent'])) {
    $tenant_id = $_POST['tenant_id'] ?? null;
    $property_id = $_POST['property_id'] ?? null;
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $rent_amount = $_POST['rent_amount'] ?? null;
    $status = $_POST['status'] ?? 'Vacant';

    if ($tenant_id && $property_id && $start_date && $rent_amount) {
        $stmt = $pdo->prepare("
            INSERT INTO rent (tenant_id, property_id, start_date, end_date, rent_amount, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$tenant_id, $property_id, $start_date, $end_date, $rent_amount, $status]);
        $message = "✅ Rent record added successfully!";
    } else {
        $error = "⚠️ Please fill out all required fields.";
    }
}


if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM rent WHERE rent_id = ?");
    $stmt->execute([$id]);
    header("Location: rent.php?deleted=1");
    exit;
}

// Fetch Rent Records showing properties name 
$stmt = $pdo->query("
    SELECT r.*, t.fname, t.mname, t.lname, p.property
    FROM rent r
    JOIN tenant t ON r.tenant_id = t.tenant_id
    JOIN property p ON r.property_id = p.property_id
    ORDER BY r.rent_id DESC
");
$rents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Rent Management | Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dashboard-container">

  <aside class="sidebar">
    <h2>🏠 RENTAL ADMIN</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="tenants.php">Tenants</a>
    <a href="properties.php">Properties</a>
    <a href="rent.php" class="active">Rent</a>
    <a href="payments.php">Payments</a>
    <a href="maintenance.php">Maintenance</a>
    <a href="../logout.php" class="logout">Logout</a>
  </aside>


  <main class="main-content">
    <header class="header">
      <h1>Rent Management</h1>
    </header>
    <br>

    <!-- Alerts -->
    <?php if ($message): ?>
      <div class="alert success"><?= htmlspecialchars($message) ?></div>
    <?php elseif ($error): ?>
      <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php elseif (isset($_GET['deleted'])): ?>
      <div class="alert success">🗑️ Rent record deleted successfully!</div>
    <?php endif; ?>

    <!-- Rent Form of tenants -->
    <div class="card">
      <form method="POST" class="rent-form">
        <h3>Add Rent Record</h3>

        <label>Tenant:</label>
        <select name="tenant_id" required>
          <option value="">-- Select Tenant --</option>
          <?php foreach ($tenants as $t): ?>
            <option value="<?= $t['tenant_id'] ?>">
              <?= htmlspecialchars($t['fname'].' '.($t['mname'] ? $t['mname'].' ' : '').$t['lname']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Property:</label>
        <select name="property_id" id="propertySelect" required>
          <option value="">-- Select Property --</option>
          <?php foreach ($properties as $p): ?>
            <option value="<?= $p['property_id'] ?>" data-rent="<?= $p['rent_amount'] ?>">
              <?= htmlspecialchars($p['property']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Start Date:</label>
        <input type="date" name="start_date" required>

        <label>End Date:</label>
        <input type="date" name="end_date">

        <label>Rent Amount (₱):</label>
        <input type="number" step="0.01" name="rent_amount" required>

        <label>Status:</label>
        <select name="status" required>
          <option value="Occupied">Occupied</option>
          <option value="Vacant">Vacant</option>
          <option value="Pending">Pending</option>
        </select>

        <button type="submit" name="add_rent">Add Rent</button>
      </form>
    </div>
    <br>

    <!-- Rent Records Table -->
    <div class="card">
      <h3>Existing Rent Records</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Tenant</th>
            <th>Property</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Rent</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($rents)): ?>
            <?php foreach ($rents as $r): ?>
              <tr>
                <td data-label="ID"><?= htmlspecialchars($r['rent_id']) ?></td>
                <td data-label="Tenant"><?= htmlspecialchars($r['fname'].' '.($r['mname'] ? $r['mname'].' ' : '').$r['lname']) ?></td>
                <td data-label="Property"><?= htmlspecialchars($r['property']) ?></td>
                <td data-label="Start Date"><?= htmlspecialchars($r['start_date']) ?></td>
                <td data-label="End Date"><?= htmlspecialchars($r['end_date']) ?></td>
                <td data-label="Rent">₱<?= number_format($r['rent_amount'],2) ?></td>
                <td data-label="Status"><?= htmlspecialchars($r['status']) ?></td>
                <td data-label="Action">
                  <a href="?delete=<?= $r['rent_id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8">No rent records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Footer -->
    <footer class="footer">
      <p>© <?= date('Y') ?> Rental Property Management System</p>
    </footer>
  </main>
</div>

<script>
// Auto-fill rent amount when property is selected
const propertySelect = document.getElementById('propertySelect');
const rentInput = document.querySelector('input[name="rent_amount"]');

propertySelect.addEventListener('change', () => {
    const selectedOption = propertySelect.options[propertySelect.selectedIndex];
    rentInput.value = selectedOption.getAttribute('data-rent') || '';
});
</script>

</body>
</html>
