<?php
// php/update_stock.php
include 'connect.php';
session_start();
if ($_SESSION['role'] !== 'manager') {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$id = intval($_POST['id']);
$stock = intval($_POST['stock']);
$price = floatval($_POST['price']);

$stmt = $conn->prepare("UPDATE products SET stock=?, price=? WHERE id=?");
$stmt->bind_param('idi', $stock, $price, $id);
$stmt->execute();

header('Location: ../pages/stock.php');
exit;
?>
