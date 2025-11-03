<?php
require '../../db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Only admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$tenant_id = (int)($_GET['id'] ?? 0);

if ($tenant_id > 0) {
    $stmt = $pdo->prepare("DELETE FROM tenant WHERE tenant_id = ?");
    if ($stmt->execute([$tenant_id])) {
        echo json_encode(['status' => 'success', 'message' => 'Tenant deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete tenant']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid tenant ID']);
}

exit;
