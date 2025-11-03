<?php
require '../db.php';


// ✅ Only admin can access
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// ✅ Fetch tenants and properties
$tenants = $pdo->query("SELECT tenant_id, CONCAT(fname, ' ', lname) AS name FROM tenant ORDER BY fname")->fetchAll(PDO::FETCH_ASSOC);
$properties = $pdo->query("SELECT property_id, property, address, rent_amount FROM property ORDER BY property")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch all payments
$payments = $pdo->query("
    SELECT 
        p.*, 
        t.fname, t.lname, 
        pr.property, pr.address 
    FROM payment p 
    LEFT JOIN tenant t ON p.tenant_id = t.tenant_id 
    LEFT JOIN property pr ON p.property_id = pr.property_id 
    ORDER BY p.payment_date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin | Payment Management</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
 
  
<div class="dashboard-container">

  <!-- Sidebar -->
  <aside class="sidebar">
    <h2>🏠 RENTAL ADMIN</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="tenants.php">Tenants</a>
    <a href="properties.php">Properties</a>
    <a href="rent.php">Rent</a>
    <a href="payments.php" class="active">Payments</a>
    <a href="maintenance.php">Maintenance</a>
    <a href="../logout.php" class="logout">Logout</a>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="header">
      <h1> Payment Management</h1>
    </header>
<br>
<br>
    <!-- Payment Form -->
    <div class="card">
      <h4>Record a Payment</h4>
      <div id="message"></div>
      <form id="paymentForm">
        <div class="form-row">
          <div class="field">
            <label>Tenant</label>
            <select name="tenant_id" required>
              <option value="">-- Select Tenant --</option>
              <?php foreach($tenants as $t): ?>
                <option value="<?= $t['tenant_id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label>Property</label>
            <select name="property_id" id="propertySelect" required>
              <option value="">-- Select Property --</option>
              <?php foreach($properties as $p): ?>
                <option value="<?= $p['property_id'] ?>" data-rent="<?= $p['rent_amount'] ?>">
                  <?= htmlspecialchars($p['property'] . ' - ' . $p['address']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row" style="margin-top:10px;">
          <div class="field">
            <label>Amount</label>
            <input name="amount" id="amountField" type="number" step="0.01" required>
          </div>
          <div class="field">
            <label>Payment Date</label>
            <input name="payment_date" type="date" required>
          </div>
          <div class="field">
            <label>Status</label>
            <select name="status">
              <option value="Paid">Paid</option>
              <option value="Unpaid">Unpaid</option>
              <option value="Late">Late</option>
            </select>
          </div>
        </div>

        <div style="margin-top:15px;">
          <button type="submit" class="btn btn-submit">Record Payment</button>
        </div>
      </form>
    </div>

    <!-- Payment List -->
    <div class="card">
      <h4>All Payments</h4>
      <table>
        <thead>
          <tr>
            <th>Tenant</th>
            <th>Property</th>
            <th>Address</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($payments)): ?>
            <tr><td colspan="6" style="text-align:center;">No payments recorded yet.</td></tr>
          <?php else: ?>
            <?php foreach($payments as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['fname'].' '.$p['lname']) ?></td>
              <td><?= htmlspecialchars($p['property']) ?></td>
              <td><?= htmlspecialchars($p['address']) ?></td>
              <td><?= number_format($p['amount'],2) ?></td>
              <td><?= htmlspecialchars($p['payment_date']) ?></td>
              <td><?= htmlspecialchars($p['status']) ?></td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <br>
<br>
<br>
<br>
<br>

    <footer class="footer">
      <p>© <?= date('Y') ?> Rental Property Management System</p>
    </footer>
  </main>
</div>

<script>
// Auto-fill amount when property is selected
document.getElementById('propertySelect').addEventListener('change', function() {
  const selected = this.options[this.selectedIndex];
  const rent = selected.getAttribute('data-rent');
  document.getElementById('amountField').value = rent ? rent : '';
});

// AJAX form submission
document.getElementById('paymentForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const formData = new FormData(e.target);
  const messageDiv = document.getElementById('message');
  messageDiv.innerHTML = '';

  try {
    const response = await fetch('../api/payments/add.php', {
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      messageDiv.innerHTML = `<div class="success">${data.message}</div>`;
      e.target.reset();
    } else {
      messageDiv.innerHTML = `<div class="error">${data.message}</div>`;
    }

  } catch (err) {
    messageDiv.innerHTML = `<div class="error">Error: ${err.message}</div>`;
  }
});
</script>
</body>
</html>
