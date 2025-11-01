<?php
// php/save_order.php
include 'config.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','msg'=>'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$items = $data['items'] ?? [];
$total = floatval($data['total'] ?? 0);
$cashier = $_SESSION['name'] ?? 'Unknown';

if (empty($items)) {
    echo json_encode(['status'=>'error','msg'=>'Cart empty']);
    exit;
}

$conn->begin_transaction();

try {
    // create order
    $order_no = 'ORD'.time();
    $stmt = $conn->prepare("INSERT INTO orders (order_no, total, cashier_name) VALUES (?, ?, ?)");
    $stmt->bind_param('sds', $order_no, $total, $cashier);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // insert items and decrease stock
    $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
    $stmtStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
    foreach ($items as $it) {
        $pid = intval($it['id']);
        $qty = intval($it['quantity']);
        $subtotal = floatval($it['subtotal']);

        // insert order item
        $stmtItem->bind_param('iiid', $order_id, $pid, $qty, $subtotal);
        $stmtItem->execute();

        // update stock
        $stmtStock->bind_param('iii', $qty, $pid, $qty);
        $stmtStock->execute();
        if ($stmtStock->affected_rows === 0) {
            throw new Exception("Insufficient stock for product ID $pid");
        }
    }

    $conn->commit();
    echo json_encode(['status'=>'ok','order_no'=>$order_no]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);
}
?>
