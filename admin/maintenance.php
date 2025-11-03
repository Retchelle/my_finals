<?php
require '../db.php';


// ✅ Restrict access to admin only
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// ✅ Fetch all maintenance requests
$stmt = $pdo->query("
    SELECT 
        r.request_id,
        r.request_date,
        r.description,
        r.status,
        t.fname, t.mname, t.lname,
        p.address
    FROM maintenance_request r
    LEFT JOIN tenant t ON r.tenant_id = t.tenant_id
    LEFT JOIN property p ON r.property_id = p.property_id
    ORDER BY r.request_date DESC
");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Maintenance Requests | Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<style>
    /* Maintenance Page Styling */


button {
  background: #3498db;
  color: #fff;
  border: none;
  padding: 6px 12px;
  border-radius: 5px;
  cursor: pointer;
}

button:hover {
  background: #2c80b4;
}

.status-select {
  padding: 5px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

    </style>
<div class="dashboard-container">

  <!-- Sidebar -->
  <aside class="sidebar">
    <h2>🏠 RENTAL ADMIN</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="tenants.php">Tenants</a>
    <a href="properties.php">Properties</a>
    <a href="rent.php">Rent</a>
    <a href="payments.php">Payments</a>
    <a href="maintenance.php" class="active">Maintenance</a>
    <a href="../logout.php" class="logout">Logout</a>
  </aside>

  <!-- Main content -->
  <main class="main-content">
    <header class="header">
      <h1> Maintenance Request Management</h1>
    </header>
    <br>
     <br>
    <div id="message"></div>

    <div class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Tenant</th>
            <th>Property</th>
            <th>Description</th>
            <th>Request Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($requests)): ?>
            <?php foreach ($requests as $r): ?>
              <tr id="row-<?= $r['request_id'] ?>">
                <td><?= $r['request_id'] ?></td>
                <td><?= htmlspecialchars(trim($r['fname'].' '.($r['mname'] ? $r['mname'].' ' : '').$r['lname'])) ?></td>
                <td><?= htmlspecialchars($r['address'] ?? '—') ?></td>
                <td><?= htmlspecialchars($r['description']) ?></td>
                <td><?= htmlspecialchars($r['request_date']) ?></td>
                <td class="status"><?= htmlspecialchars($r['status']) ?></td>
                <td>
                  <select class="status-select" data-id="<?= $r['request_id'] ?>">
                    <option value="Pending" <?= $r['status']=='Pending'?'selected':'' ?>>Pending</option>
                    <option value="In Progress" <?= $r['status']=='In Progress'?'selected':'' ?>>In Progress</option>
                    <option value="Completed" <?= $r['status']=='Completed'?'selected':'' ?>>Completed</option>
                  </select>
                  <button class="update-btn" data-id="<?= $r['request_id'] ?>">Update</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7">No maintenance requests yet.</td></tr>
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
document.querySelectorAll('.update-btn').forEach(button => {
  button.addEventListener('click', async (e) => {
    const id = e.target.dataset.id;
    const statusSelect = document.querySelector(`.status-select[data-id="${id}"]`);
    const newStatus = statusSelect.value;
    const messageDiv = document.getElementById('message');

    messageDiv.innerHTML = '';

    const formData = new FormData();
    formData.append('request_id', id);
    formData.append('status', newStatus);

    try {
      const response = await fetch('../api/maintenance/update.php', {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        document.querySelector(`#row-${id} .status`).innerText = newStatus;
        messageDiv.innerHTML = `<div class="success">${data.message}</div>`;
        setTimeout(() => { messageDiv.innerHTML = ''; }, 3000);
      } else {
        messageDiv.innerHTML = `<div class="error">${data.message}</div>`;
      }
    } catch (err) {
      messageDiv.innerHTML = `<div class="error">Error: ${err.message}</div>`;
    }
  });
});
</script>
</body>
</html>
