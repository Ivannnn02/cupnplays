<?php
// pages/add_product.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header('Location: login.php');
    exit;
}
include('../php/connect.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price    = floatval($_POST['price'] ?? 0);
    $stock    = intval($_POST['stock'] ?? 0);
    $sold     = intval($_POST['sold'] ?? 0);

    // Validate
    if ($name === '') $errors[] = "Product name is required.";
    if ($category === '') $errors[] = "Category is required.";
    if ($price <= 0) $errors[] = "Price must be greater than 0.";

    // Handle image upload
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','webp'];
        $tmp = $_FILES['image']['tmp_name'];
        $original = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = "Image must be JPG/PNG/WEBP.";
        } else {
            // create a unique filename
            $imageName = time() . '_' . preg_replace('/[^a-z0-9_\-\.]/i','', $original);
            $target = __DIR__ . '/../images/' . $imageName;
            if (!move_uploaded_file($tmp, $target)) {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    if (empty($errors)) {
        // Insert into DB (columns: name, category, price, stock, sold, image)
        $stmt = $conn->prepare("INSERT INTO product (name, category, price, stock, sold, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssdiss', $name, $category, $price, $stock, $sold, $imageName);
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: dashboard_manager.php');
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
            $stmt->close();
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Product - Manager</title>
  <link rel="stylesheet" href="../css/add_prod.css">
</head>
<body>
  <div class="content" style="padding:30px;">
    <h2>Add New Product</h2>

    <?php if (!empty($errors)): ?>
      <div style="background:#ffe6e6;padding:12px;border-radius:8px;margin-bottom:12px;">
        <ul>
          <?php foreach($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="add_product.php" method="post" enctype="multipart/form-data" style="display:grid;gap:12px;max-width:640px;">
      <label>
        Name
        <input type="text" name="name" required>
      </label>

      <label>
        Category
        <input type="text" name="category" required placeholder="e.g. coffee or milk tea">
      </label>

      <label>
        Price
        <input type="number" step="0.01" name="price" required>
      </label>

      <label>
        Stock (number)
        <input type="number" name="stock" value="0" required>
      </label>

      <label>
        Sold (optional)
        <input type="number" name="sold" value="0">
      </label>

      <label>
        Image (JPG/PNG/WEBP)
        <input type="file" name="image" accept="image/*">
      </label>

      <div style="display:flex;gap:8px;">
        <button type="submit" style="background:#007bff;color:#fff;padding:10px;border:none;border-radius:6px;">Save Product</button>
        <a href="dashboard_manager.php" style="padding:10px;border-radius:6px;background:#ddd;text-decoration:none;color:#333;">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
