<?php
// pages/edit_product.php
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

// fetch current product
$stmt = $conn->prepare("SELECT id, name, category, price, stock, sold, image FROM product WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: dashboard_manager.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price    = floatval($_POST['price'] ?? 0);
    $stock    = intval($_POST['stock'] ?? 0);
    $sold     = intval($_POST['sold'] ?? 0);

    if ($name === '') $errors[] = "Product name is required.";
    if ($category === '') $errors[] = "Category is required.";
    if ($price <= 0) $errors[] = "Price must be greater than 0.";

    // handle image if replaced
    $imageName = $product['image']; // keep current by default
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','webp'];
        $tmp = $_FILES['image']['tmp_name'];
        $original = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = "Image must be JPG/PNG/WEBP.";
        } else {
            $imageName = time() . '_' . preg_replace('/[^a-z0-9_\-\.]/i','', $original);
            $target = __DIR__ . '/../images/' . $imageName;
            if (!move_uploaded_file($tmp, $target)) {
                $errors[] = "Failed to upload image.";
            } else {
                // delete old image if exists
                if (!empty($product['image']) && file_exists(__DIR__ . '/../images/' . $product['image'])) {
                    @unlink(__DIR__ . '/../images/' . $product['image']);
                }
            }
        }
    }

    if (empty($errors)) {
        $stmtUpd = $conn->prepare("UPDATE product SET name=?, category=?, price=?, stock=?, sold=?, image=? WHERE id=?");
        $stmtUpd->bind_param('ssdissi', $name, $category, $price, $stock, $sold, $imageName, $id);
        if ($stmtUpd->execute()) {
            $stmtUpd->close();
            header('Location: dashboard_manager.php');
            exit;
        } else {
            $errors[] = "Database error: " . $stmtUpd->error;
            $stmtUpd->close();
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Product - Manager</title>
  <link rel="stylesheet" href="../css/edit_prod.css">
</head>
<body>
  <div class="content" style="padding:30px;">
    <h2>Edit Product</h2>

    <?php if (!empty($errors)): ?>
      <div style="background:#ffe6e6;padding:12px;border-radius:8px;margin-bottom:12px;">
        <ul>
          <?php foreach($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="edit_product.php?id=<?= $id ?>" method="post" enctype="multipart/form-data" style="display:grid;gap:12px;max-width:640px;">
      <label>
        Name
        <input type="text" name="name" required value="<?=htmlspecialchars($product['name'])?>">
      </label>

      <label>
        Category
        <input type="text" name="category" required value="<?=htmlspecialchars($product['category'])?>">
      </label>

      <label>
        Price
        <input type="number" step="0.01" name="price" required value="<?=htmlspecialchars($product['price'])?>">
      </label>

      <label>
        Stock (number)
        <input type="number" name="stock" value="<?=htmlspecialchars($product['stock'])?>" required>
      </label>

      <label>
        Sold
        <input type="number" name="sold" value="<?=htmlspecialchars($product['sold'])?>">
      </label>

      <label>
        Current Image
        <?php if (!empty($product['image']) && file_exists(__DIR__ . '/../images/' . $product['image'])): ?>
          <div style="margin:8px 0;"><img src="../images/<?=htmlspecialchars($product['image'])?>" style="max-width:160px;border-radius:8px;"></div>
        <?php else: ?>
          <div style="margin:8px 0;color:#666">No image</div>
        <?php endif; ?>
        Replace Image (optional)
        <input type="file" name="image" accept="image/*">
      </label>

      <div style="display:flex;gap:8px;">
        <button type="submit" style="background:#17a2b8;color:#fff;padding:10px;border:none;border-radius:6px;">Save Changes</button>
        <a href="dashboard_manager.php" style="padding:10px;border-radius:6px;background:#ddd;text-decoration:none;color:#333;">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
x