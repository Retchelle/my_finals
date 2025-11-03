<?php
require '../db.php';
session_start();

// ✅ Only admin can delete
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// ✅ Check if ID is provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM rent WHERE rent_id = ?");
        $stmt->execute([$id]);
        // ✅ Redirect back with a deleted message
        header("Location: ../admin/rent.php?deleted=1");
        exit;
    }
}

// If no valid ID, just redirect back
header("Location: ../admin/rent.php");
exit;
