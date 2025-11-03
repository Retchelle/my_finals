<?php
require '../db.php';
session_start();

// Only admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Check POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rent_id     = $_POST['rent_id'] ?? null;
    $tenant_id   = $_POST['tenant_id'] ?? null;
    $property_id = $_POST['property_id'] ?? null;
    $start_date  = $_POST['start_date'] ?? null;
    $end_date    = $_POST['end_date'] ?? null;
    $rent_amount = $_POST['rent_amount'] ?? null;
    $status      = $_POST['status'] ?? null;

    if ($rent_id && $tenant_id && $property_id && $start_date && $rent_amount && $status) {
        $stmt = $pdo->prepare("UPDATE rent SET tenant_id=?, property_id=?, start_date=?, end_date=?, rent_amount=?, status=? WHERE rent_id=?");
        $stmt->execute([$tenant_id, $property_id, $start_date, $end_date, $rent_amount, $status, $rent_id]);
        echo json_encode(['success' => '✅ Rent record updated successfully!']);
    } else {
        echo json_encode(['error' => '⚠️ Please fill out all required fields.']);
    }
}
