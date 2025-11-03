<?php
require '../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['request_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id && $status) {
        $stmt = $pdo->prepare("UPDATE maintenance_request SET status = ? WHERE request_id = ?");
        if ($stmt->execute([$status, $id])) {
            echo json_encode(['success' => true, 'message' => '✅ Status updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Failed to update status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
}
?>
