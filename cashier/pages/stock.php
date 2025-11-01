<?php
// pages/stock.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header('Location: login.php');
    exit;
}
include_once('../php/connect.php');
$products = $conn->query("SELECT * FROM products ORDER BY category, name");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Stock - Cup N' Play</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="app">
  <div class="sidebar">
    <div class="logo">CnP</div>
    <button class="side-btn" onclick="location.href='dashboard_manager.php'">🏠</button>
    <button class="side-btn" onclick="location.href='sales.php'">💰</button>
    <button class="side-btn" onclick="location.href='../php/logout.php'">⎋</button>
  </div>

  <div class="main">
    <div class="header">
      <h1>Stock Management</h1>
      <div class="user"><?=htmlspecialchars($_SESSION['name'])?></div>
    </div>

    <div class="card">
      <h3>Add Product</h3>
      <form action="../php/add_product.php" method="POST" enctype="multipart/form-data" style="display:flex;flex-wrap:wrap;gap:8px">
        <input name="name" placeholder="Name" required style="flex:2">
        <input name="category" placeholder="Category" required style="flex:1">
        <input name="price" placeholder="Price" type="number" step="0.01" required style="flex:1">
        <input name="stock" placeholder="Stock" type="number" required style="flex:1">
        <input name="image" type="file" accept="image/*" style="flex:1">
        <button type="submit" style="flex-basis:100%;padding:10px;border:none;border-radius:8px;background:var(--accent);color:white">Add Product</button>
      </form>
    </div>

    <div class="card" style="margin-top:12px">
      <h3>Existing Products</h3>
      <table class="table">
        <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Action</th></tr></thead>
        <tbody>
          <?php while($p = $products->fetch_assoc()): ?>
          <tr>
            <td><img src="../images/<?=$p['image']?>" style="width:60px;height:40px;object-fit:cover" onerror="this.style.opacity=.6"></td>
            <td><?=htmlspecialchars($p['name'])?></td>
            <td><?=htmlspecialchars($p['category'])?></td>
            <td>₱ <?=number_format($p['price'],2)?></td>
            <td><?=intval($p['stock'])?></td>
            <td>
              <form action="../php/update_stock.php" method="POST" style="display:inline-flex;gap:6px;align-items:center">
                <input type="hidden" name="id" value="<?=$p['id']?>">
                <input name="stock" type="number" value="<?=$p['stock']?>" style="width:80px">
                <input name="price" type="number" step="0.01" value="<?=$p['price']?>" style="width:100px">
                <button type="submit" style="padding:6px 8px;border-radius:6px;border:none;background:#cfa27f">Update</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>
</body>
</html>
