<?php
require '../db.php';

// Ensure tenant is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'tenant') {
    header('Location: login.php');
    exit;
}

$tenant_id = (int)$_SESSION['tenant_id'];
$stmt = $pdo->prepare("SELECT * FROM payment WHERE tenant_id = ? ORDER BY payment_date DESC");
$stmt->execute([$tenant_id]);
$payments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment History | Tenant Dashboard</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
/* ✅ Table Styling */






</style>
</head>
<body>

<div class="dashboard-container">

  <!-- ✅ SIDEBAR -->
  <aside class="sidebar">
    <h2>🏠 Tenant Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="payment_history.php" class="active">Payments</a>
    <a href="maintenance_request.php">Maintenance</a>
    <a href="logout.php" class="logout">Logout</a>
  </aside>

  <!-- ✅ MAIN CONTENT -->
  <main class="main-content">
    <!-- HEADER -->
    <div class="header">
      💰 Payment History
    </div>

    <!-- CONTENT -->
    <div class="content">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($payments): ?>
          <?php foreach ($payments as $payment): ?>
          <tr>
            <td data-label="Date"><?= htmlspecialchars($payment['payment_date']) ?></td>
            <td data-label="Amount">₱<?= number_format($payment['amount'], 2) ?></td>
            <td data-label="Status">
              <?php
                $status = strtolower($payment['status']);
                $badgeClass = $status === 'paid' ? 'paid' : ($status === 'pending' ? 'pending' : 'failed');
              ?>
              <span class="status <?= $badgeClass ?>">
                <?= ucfirst($status) ?>
              </span>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3" style="text-align:center; color:#7f8c8d;">No payment records found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <br>
<br>
<br>
<br>
<br>
<br>
<br><br>
<br>
<br>
<br>
<br>
    <!-- ✅ FOOTER -->
    <footer class="footer">
      <p>© <?= date('Y') ?> Rental Property Management System</p>
    </footer>
  </main>
</div>

</body>
</html>
