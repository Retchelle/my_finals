<?php
require '../../db.php';
session_start();

// ✅ Allow only admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    $_SESSION['message'] = '❌ Unauthorized access.';
    header('Location: ../../Admin/properties.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $pdo->beginTransaction();

        // ✅ Delete related records (remove tenant_property since table doesn't exist)
        $pdo->prepare("DELETE FROM payment WHERE property_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM rent WHERE property_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM maintenance_request WHERE property_id = ?")->execute([$id]);

        // ✅ Get image path
        $stmt = $pdo->prepare("SELECT image FROM property WHERE property_id = ?");
        $stmt->execute([$id]);
        $image = $stmt->fetchColumn();

        // ✅ Delete property
        $stmt = $pdo->prepare("DELETE FROM property WHERE property_id = ?");
        if ($stmt->execute([$id])) {
            // ✅ Delete image file if exists
            if ($image && file_exists('../../' . $image)) {
                unlink('../../' . $image);
            }

            $pdo->commit();
            $_SESSION['message'] = '✅ Property deleted successfully!';
        } else {
            $pdo->rollBack();
            $_SESSION['message'] = '❌ Failed to delete property.';
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['message'] = '❌ Error: ' . htmlspecialchars($e->getMessage());
    }
} else {
    $_SESSION['message'] = '❌ Invalid property ID.';
}

// ✅ Redirect back to properties list
header('Location: ../../Admin/properties.php');
exit;
?>
