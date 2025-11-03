<?php
require '../db.php';



if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Fetch all tenants
$tenants = $pdo->query("SELECT tenant_id, fname, mname, lname, contact_number, email FROM tenant ORDER BY tenant_id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Tenants | Admin Dashboard</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>

.btn {
    display: inline-block;
    padding: 8px 14px;
    border-radius: 5px;
    text-decoration: none;
    color: #fff;
    font-weight: bold;
    transition: background 0.3s;
}


.btn-add { background-color: #28a745; } 
.btn-add:hover { background-color: #218838; }

.btn-edit { background-color: #007bff; } 
.btn-edit:hover { background-color: #0069d9; }

.btn-delete { background-color: #dc3545; } 
.btn-delete:hover { background-color: #c82333; }
</style>
</head>
<body>

<div class="dashboard-container">

  <aside class="sidebar">
    <h2>🏠 RENTAL ADMIN</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="tenants.php" class="active">Tenants</a>
    <a href="properties.php">Properties</a>
    <a href="rent.php">Rent</a>
    <a href="payments.php">Payments</a>
    <a href="maintenance.php">Maintenance</a>
    <a href="../logout.php" class="logout">Logout</a>
  </aside>

  
  <main class="main-content">
    <div class="header">
      <h1>Manage Tenants</h1>
    </div>
  <br>
  <br>
    <div class="card">
      <a class="btn btn-add" href="../api/tenants/add.php"> Add Tenant</a>
     <br>
     <br>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($tenants)): ?>
            <?php foreach($tenants as $t): ?>
            <tr>
              <td><?= $t['tenant_id'] ?></td>
              <td><?= htmlspecialchars($t['fname'] . ' ' . ($t['mname'] ? $t['mname'] . ' ' : '') . $t['lname']) ?></td>
              <td><?= htmlspecialchars($t['contact_number'] ?? '') ?></td>
              <td><?= htmlspecialchars($t['email'] ?? '') ?></td>
              <td class="action-links">
                <a class="btn btn-edit" href="../api/tenants/edit.php?id=<?= $t['tenant_id'] ?>">Edit</a>
                <a class="btn btn-delete" href="../api/tenants/delete.php?id=<?= $t['tenant_id'] ?>" onclick="return confirm('Delete this tenant?')">Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="muted">No tenants found.</td></tr>
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

</body>
</html>
