<?php
// php/get_sales.php
include 'connect.php';
header('Content-Type: application/json');

$q = "SELECT o.*, GROUP_CONCAT(CONCAT(oi.quantity,'x ',p.name) SEPARATOR ' | ') as items
      FROM orders o
      LEFT JOIN order_items oi ON oi.order_id = o.id
      LEFT JOIN products p ON p.id = oi.product_id
      GROUP BY o.id
      ORDER BY o.date DESC
      LIMIT 200";
$res = $conn->query($q);
$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;
echo json_encode($rows);
?>
