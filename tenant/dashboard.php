<?php
require '../db.php';

// Ensure tenant is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'tenant') {
    header('Location: login.php');
    exit;
}

$tenant_id = (int)$_SESSION['tenant_id'];

// Fetch tenant profile
$stmt = $pdo->prepare("SELECT * FROM tenant WHERE tenant_id = ?");
$stmt->execute([$tenant_id]);
$profile = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tenant Dashboard</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="dashboard-container">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>🏡 Tenant</h2>
        <a href="profile.php" class="active">Profile</a>
        <a href="payment_history.php">Payments</a>
        <a href="maintenance_request.php">Maintenance</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            Welcome, <strong><?= htmlspecialchars($profile['fname'] . ' ' . $profile['lname']) ?></strong>!
        </div>
<br>
<br>
        <!-- Quick Info Card -->
        <div class="card">
            <h4>📋 Quick Info</h4>
            <p class="muted">
                Use this dashboard to manage your profile, view payments, and submit maintenance requests.
                <br><br>
                <strong>Status Legend:</strong><br>
                🕓 Pending — Waiting for admin review<br>
                🧱 In Progress — Being repaired<br>
                ✅ Completed — Issue resolved
            </p>
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
    </div>

</div>

</body>
</html>
