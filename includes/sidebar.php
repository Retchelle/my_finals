<aside class="sidebar">
  <h2>🏠 RENTAL ADMIN</h2>
  <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
  <a href="tenants.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tenants.php' ? 'active' : '' ?>">Tenants</a>
  <a href="properties.php" class="<?= basename($_SERVER['PHP_SELF']) == 'properties.php' ? 'active' : '' ?>">Properties</a>
  <a href="rent.php" class="<?= basename($_SERVER['PHP_SELF']) == 'rent.php' ? 'active' : '' ?>">Rent</a>
  <a href="payments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : '' ?>">Payments</a>
  <a href="maintenance.php" class="<?= basename($_SERVER['PHP_SELF']) == 'maintenance.php' ? 'active' : '' ?>">Maintenance</a>
  <a href="../logout.php" class="logout">Logout</a>
</aside>
