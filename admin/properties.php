<?php
require '../db.php';

// Ensure only admin can access
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location:index.php');
    exit;
}

// Fetch all properties
$props = $pdo->query("SELECT * FROM property ORDER BY property_id DESC")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Properties</title>
<link rel="stylesheet" href="../assets/css/style.css"> <!-- combined CSS -->
<style>
/* Page-specific styles */
.btn { padding:8px 14px; border:none; border-radius:5px; text-decoration:none; color:#fff; font-weight:bold; cursor:pointer; transition:0.3s; }
.btn-add { background:#3498db; }
.btn-view { background:#2ecc71; }
.btn-edit { background:#f1c40f; color:#000; }
.btn-delete { background:#e74c3c; }
.btn-back { background:#7f8c8d; }
.btn:hover { opacity:0.8; }
.actions { display:flex; gap:5px; }
img.property-img { width:80px; height:60px; object-fit:cover; border-radius:5px; border:1px solid #ccc; }

/* Notification styles */
.alert {
    padding: 12px 20px;
    margin: 15px 0;
    border-radius: 5px;
    color: #fff;
    font-weight: bold;
    opacity: 0.95;
    transition: opacity 0.5s ease-out;
}
.alert-success { background: #2ecc71; }
.alert-error { background: #e74c3c; }
</style>
</head>
<body>
<div class="dashboard-container">

  <!-- Sidebar -->
  <aside class="sidebar">
    <h2>🏠 RENTAL ADMIN</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="tenants.php">Tenants</a>
    <a href="properties.php" class="active">Properties</a>
    <a href="rent.php">Rent</a>
    <a href="payments.php">Payments</a>
    <a href="maintenance.php">Maintenance</a>
    <a href="../logout.php" class="logout">Logout</a>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="header">
      <h1>Manage Properties</h1>
    </header>

    <!-- Notification message -->
    <?php if(isset($_SESSION['message'])): ?>
      <div class="alert <?= strpos($_SESSION['message'],'❌') !== false ? 'alert-error' : 'alert-success' ?>">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
      </div>
    <?php endif; ?>
<br>
<br>
    <a class="btn btn-add" href="../Api/properties/addd.php">Add Property</a>
    <br>
<br>
    <table>
      <thead>
        <tr>
          <th>Picture</th>
          <th>ID</th>
          <th>Property</th>
          <th>Address</th>
          <th>Rent</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if($props): ?>
          <?php foreach($props as $p): ?>
          <tr>
            <td>
              <?php $img = !empty($p['image']) && file_exists("../".$p['image']) ? "../".htmlspecialchars($p['image']) : 'uploads/no-image.png'; ?>
              <img src="<?= $img ?>" class="property-img" alt="Property">
            </td>
            <td><?= $p['property_id'] ?></td>
            <td><?= htmlspecialchars($p['property']) ?></td>
            <td><?= htmlspecialchars($p['address']) ?></td>
            <td>₱<?= number_format($p['rent_amount'],2) ?></td>
            <td class="actions">
              <a class="btn btn-view" href="../Api/properties/view.php?id=<?= $p['property_id'] ?>">View</a>
              <a class="btn btn-edit" href="../Api/properties/edit.php?id=<?= $p['property_id'] ?>">Edit</a>
              <a class="btn btn-delete" 
   href="../Api/properties/delete.php?id=<?= $p['property_id'] ?>" 
   onclick="return confirm('Are you sure you want to delete this property?')">
   Delete
</a>


            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" style="text-align:center;color:#777;">No properties found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
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
// Auto-hide alerts after 3 seconds
setTimeout(() => {
    const alert = document.querySelector('.alert');
    if(alert) alert.style.opacity = '0';
}, 3000);
</script>
</body>
</html>
