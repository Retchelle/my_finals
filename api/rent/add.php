<?php
require '../db.php';


// Only admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Check POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenant_id   = $_POST['tenant_id'] ?? null;
    $property_id = $_POST['property_id'] ?? null;
    $start_date  = $_POST['start_date'] ?? null;
    $end_date    = $_POST['end_date'] ?? null;
    $rent_amount = $_POST['rent_amount'] ?? null;
    $status      = $_POST['status'] ?? 'Vacant';

    if ($tenant_id && $property_id && $start_date && $rent_amount) {
        $stmt = $pdo->prepare("INSERT INTO rent (tenant_id, property_id, start_date, end_date, rent_amount, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tenant_id, $property_id, $start_date, $end_date, $rent_amount, $status]);
        echo json_encode(['success' => '✅ Rent record added successfully!']);
    } else {
        echo json_encode(['error' => '⚠️ Please fill out all required fields.']);
    }
}
