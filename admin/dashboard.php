<?php


require __DIR__ . '/../db.php'; // safer include path

// Check if admin is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// ===== COUNT CARDS =====
$countTenants = $pdo->query("SELECT COUNT(*) FROM tenant")->fetchColumn();
$countProperties = $pdo->query("SELECT COUNT(*) FROM property")->fetchColumn();
$countPayments = $pdo->query("SELECT COUNT(*) FROM payment")->fetchColumn();
$countRequests = $pdo->query("SELECT COUNT(*) FROM maintenance_request")->fetchColumn();
$countRents = $pdo->query("SELECT COUNT(*) FROM rent")->fetchColumn();

// ===== RENT COLLECTION (monthly) =====
$rentData = $pdo->query("
    SELECT DATE_FORMAT(payment_date, '%Y-%m') AS month, SUM(amount) AS total 
    FROM payment 
    GROUP BY month 
    ORDER BY month ASC
")->fetchAll(PDO::FETCH_ASSOC);

// ===== RENT STATUS =====
$statusData = $pdo->query("
    SELECT status, COUNT(*) AS total 
    FROM rent 
    WHERE status IN ('Occupied', 'Vacant', 'Pending')
    GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);

// ===== MAINTENANCE STATUS =====
$maintenanceData = $pdo->query("
    SELECT status, COUNT(*) AS total 
    FROM maintenance_request 
    GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);

// ===== RECENT PAYMENTS =====
$recentPayments = $pdo->query("
    SELECT p.*, t.username, pr.address 
    FROM payment p 
    LEFT JOIN tenant t ON p.tenant_id = t.tenant_id 
    LEFT JOIN property pr ON p.property_id = pr.property_id 
    ORDER BY p.payment_date DESC 
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

// ===== RECENT MAINTENANCE REQUESTS =====
$requests = $pdo->query("
    SELECT r.*, t.username, pr.address 
    FROM maintenance_request r 
    LEFT JOIN tenant t ON r.tenant_id = t.tenant_id 
    LEFT JOIN property pr ON r.property_id = pr.property_id 
    ORDER BY r.request_date DESC 
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="dashboard-container">

  <!-- Sidebar -->
  <aside class="sidebar">
    <h2>🏠 RENTAL ADMIN</h2>
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="tenants.php">Tenants</a>
    <a href="properties.php">Properties</a>
    <a href="rent.php">Rent</a>
    <a href="payments.php">Payments</a>
    <a href="maintenance.php">Maintenance</a>
    <a href="../logout.php" class="logout">Logout</a>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="header">
      <h1>Admin Dashboard</h1>
    </header>

    <!-- Overview -->
    <section class="overview">
      <div class="card stat">Tenants<br><strong><?= $countTenants ?></strong></div>
      <div class="card stat">Properties<br><strong><?= $countProperties ?></strong></div>
      <div class="card stat">Rent<br><strong><?= $countRents ?></strong></div>
      <div class="card stat">Payments<br><strong><?= $countPayments ?></strong></div>
      <div class="card stat">Requests<br><strong><?= $countRequests ?></strong></div>
    </section>

    <!-- Charts -->
    <section class="charts">
      <div class="card chart-card">
        <h3>💰 Rent Collection (Monthly)</h3>
        <canvas id="rentChart"></canvas>
      </div>

      <div class="card chart-card">
        <h3>🏡 Rent Status</h3>
        <canvas id="statusChart"></canvas>
      </div>

      <div class="card chart-card">
        <h3>🧰 Maintenance Summary</h3>
        <canvas id="maintenanceChart"></canvas>
      </div>
    </section>

    <!-- Tables -->
    <section class="tables">
      <div class="card">
        <h3>Recent Payments</h3>
        <table>
          <thead><tr><th>Tenant</th><th>Property</th><th>Amount</th><th>Date</th></tr></thead>
          <tbody>
          <?php foreach($recentPayments as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['username']) ?></td>
            <td><?= htmlspecialchars($p['address']) ?></td>
            <td>₱<?= number_format($p['amount'], 2) ?></td>
            <td><?= htmlspecialchars($p['payment_date']) ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="card">
        <h3>Recent Maintenance Requests</h3>
        <table>
          <thead><tr><th>Tenant</th><th>Property</th><th>Date</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach($requests as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['username']) ?></td>
            <td><?= htmlspecialchars($r['address']) ?></td>
            <td><?= htmlspecialchars($r['request_date']) ?></td>
            <td><?= htmlspecialchars($r['status']) ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
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

<!-- JSON encode chart data -->
<script>
const rentData = <?= json_encode($rentData) ?>;
const statusData = <?= json_encode($statusData) ?>;
const maintenanceData = <?= json_encode($maintenanceData) ?>;
</script>

<script src="../assets/js/charts.js"></script>
</body>
</html>
