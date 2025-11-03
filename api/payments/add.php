<?php
require '../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenant_id = $_POST['tenant_id'] ?? null;
    $property_id = $_POST['property_id'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $payment_date = $_POST['payment_date'] ?? ''; // can be chosen by user
    $status = $_POST['status'] ?? 'Paid';

    if (!$tenant_id || !$property_id || !$amount) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // ✅ Use today's date if user didn't choose
    if (empty($payment_date)) {
        $payment_date = date('Y-m-d');
    }

    $stmt = $pdo->prepare("
        INSERT INTO payment (tenant_id, property_id, amount, payment_date, status)
        VALUES (?, ?, ?, ?, ?)
    ");

    if ($stmt->execute([$tenant_id, $property_id, $amount, $payment_date, $status])) {
        echo json_encode(['success' => true, 'message' => '✅ Payment recorded successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => '❌ Failed to record payment.']);
    }
}
?>
