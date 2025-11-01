<?php
// pages/delete_product.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header('Location: login.php');
    exit;
}
include('../php/connect.php');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: dashboard_manager.php');
    exit;
}

// fetch image name to delete file
$stmt = $conn->prepare("SELECT image FROM product WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if ($row && !empty($row['image'])) {
    $imgPath = __DIR__ . '/../images/' . $row['image'];
    if (file_exists($imgPath)) {
        @unlink($imgPath);
    }
}

// delete product row
$stmtDel = $conn->prepare("DELETE FROM product WHERE id = ?");
stmtDel_check:
if (!$stmtDel) {
    // fallback to direct query (rare)
    mysqli_query($conn, "DELETE FROM product WHERE id = $id");
} else {
    $stmtDel->bind_param('i', $id);
    $stmtDel->execute();
    $stmtDel->close();
}

header('Location: dashboard_manager.php');
exit;
?>
