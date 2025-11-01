<?php
// get_products.php
include('connect.php'); // ✅ connects to your database

header('Content-Type: application/json');

// Get category from the request (e.g., coffee or milk tea)
$category = isset($_GET['category']) ? $_GET['category'] : '';

if (empty($category)) {
    echo json_encode([]);
    exit;
}

// Prepare query — only get products by selected category
$stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE category = ?");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
$stmt->close();
$conn->close();
?>
