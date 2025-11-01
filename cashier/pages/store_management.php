<?php
session_start();
include('../php/connect.php');

// Only manager can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header('Location: login.php');
    exit;
}

// Fetch all products
$result = mysqli_query($conn, "SELECT * FROM product ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Store Management - Cup N' Play</title>
  <link rel="stylesheet" href="../css/store.css">
</head>
<body>
<div class="dashboard">
  <aside class="sidebar">
    <div class="logo-section">
  <img src="../images/cnplogo.png" alt="Cup N Play Logo" class="logo-img">
  <h2 class="logo-text">Cup 'N Play</h2>
</div>

    <nav>
      <a href="dashboard_manager.php">📊 Dashboard</a>
      <a href="#" class="active">🏪 Store Management</a>
      <a href="order_management.php">🧾 Order Management</a>
      
    </nav>
    <div class="user-info">
      <img src="../images/manager.png" alt="Manager">
      <p><?=htmlspecialchars($_SESSION['name'])?></p>
      <a href="../php/logout.php" class="logout">Logout</a>
    </div>
  </aside>

  <main class="content">
    <header>
      <h1>Store Management</h1>
      <button class="add-btn" onclick="window.location='add_product.php'">+ Add Product</button>
    </header>

    <!-- Toolbar -->
    <div class="toolbar">
      <input type="text" id="search" placeholder="Search by name...">
      <select id="filter-category">
        <option value="">All Categories</option>
        <option value="coffee">Coffee</option>
        <option value="milktea">Milk Tea</option>
      </select>
      <select id="sort">
        <option value="name">Sort by Name</option>
        <option value="price">Sort by Price</option>
        <option value="stock">Sort by Stock</option>
      </select>
    </div>

    <!-- Product Table -->
    <div class="table-container">
      <table id="productTable">
        <thead>
          <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Sold</th>
            <th>Price</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><img src="../images/<?=htmlspecialchars($row['image'])?>" alt=""> <?=htmlspecialchars($row['name'])?></td>
            <td><?=htmlspecialchars($row['category'])?></td>
            <td><?=htmlspecialchars($row['stock'])?></td>
            <td><?=htmlspecialchars($row['sold'])?></td>
            <td>₱<?=number_format($row['price'], 2)?></td>
            <td>
              <a href="edit_product.php?id=<?=$row['id']?>" class="edit">Edit</a>
              <a href="delete_product.php?id=<?=$row['id']?>" class="delete" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
                
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
<script>
const search = document.getElementById('search');
const filter = document.getElementById('filter-category');
const sort = document.getElementById('sort');
const table = document.getElementById('productTable').querySelector('tbody');

function applyLowStockTag() {
  table.querySelectorAll('tr').forEach(row => {
    const stockCell = row.cells[2];
    const stockVal = parseInt(stockCell.innerText);
    if (stockVal <= 10) {
      stockCell.innerHTML = `<span class="low-stock">Low Stock (${stockVal})</span>`;
    }
  });
}

function filterTable() {
  const rows = Array.from(table.querySelectorAll('tr'));
  const term = search.value.toLowerCase();
  const category = filter.value;
  const sortBy = sort.value;

  rows.forEach(row => {
    const name = row.cells[0].innerText.toLowerCase();
    const cat = row.cells[1].innerText.toLowerCase();
    row.style.display = (name.includes(term) && (category === '' || cat === category)) ? '' : 'none';
  });

  const visibleRows = rows.filter(r => r.style.display !== 'none');

  visibleRows.sort((a, b) => {
    const getText = (cell) => cell.innerText.toLowerCase();
    if (sortBy === 'price')
      return parseFloat(a.cells[4].innerText.replace('₱','')) - parseFloat(b.cells[4].innerText.replace('₱',''));
    if (sortBy === 'stock')
      return parseInt(a.cells[2].innerText) - parseInt(b.cells[2].innerText);
    return getText(a.cells[0]).localeCompare(getText(b.cells[0]));
  });

  visibleRows.forEach(row => table.appendChild(row));
  applyLowStockTag();
}

search.addEventListener('input', filterTable);
filter.addEventListener('change', filterTable);
sort.addEventListener('change', filterTable);

window.onload = applyLowStockTag;
</script>
</body>
</html>
