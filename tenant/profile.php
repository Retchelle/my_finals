<?php
require '../db.php';


// Ensure tenant is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'tenant') {
    header('Location: login.php');
    exit;
}

$tenant_id = (int)$_SESSION['tenant_id'];
$stmt = $pdo->prepare("SELECT * FROM tenant WHERE tenant_id = ?");
$stmt->execute([$tenant_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tenant Profile</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="dashboard-container">
  
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>🏡 Tenant Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php" class="active">Profile</a>
    <a href="payment_history.php">Payments</a>
    <a href="maintenance_request.php">Maintenance</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Header -->
    <div class="header">
      Tenant Profile
    </div>

    <!-- Profile Info -->
    <div class="card">
      <h2>👤 Profile Information</h2>
      <p><strong>Name:</strong> <?= htmlspecialchars($profile['fname'] . ' ' . $profile['lname']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($profile['email']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($profile['contact_number'] ?? '') ?></p>
    </div>
    <br>
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
  </div>

</div>

</body>
</html>
