<?php
session_start();
include('../php/connect.php');

// Only manager can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header('Location: login.php');
    exit;
}

// Fetch summary data
$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM product"))['total'];
$totalOrders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'] ?? 0;
$totalSales    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(price * sold) AS total FROM product"))['total'] ?? 0;
$lowStock      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM product WHERE stock <= 10"))['total'];

// Product data for charts
$productData = mysqli_query($conn, "SELECT name, sold, category FROM product ORDER BY sold DESC LIMIT 6");
$names = $sales = $categories = [];
while ($row = mysqli_fetch_assoc($productData)) {
    $names[] = $row['name'];
    $sales[] = $row['sold'];
    $categories[] = ucfirst($row['category']);
}

// Category distribution
$catQuery = mysqli_query($conn, "SELECT category, COUNT(*) AS count FROM product GROUP BY category");
$catLabels = $catValues = [];
while ($cat = mysqli_fetch_assoc($catQuery)) {
    $catLabels[] = ucfirst($cat['category']);
    $catValues[] = $cat['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manager Dashboard - Cup N' Play</title>
  <link rel="stylesheet" href="../css/manager.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="dashboard">
  <aside class="sidebar">
    <div class="logo-section">
  <img src="../images/cnplogo.png" alt="Cup N Play Logo" class="logo-img">
  <h2 class="logo-text">Cup 'N Play</h2>
</div>

    <nav>
      <a href="#" class="active">📊 Dashboard</a>
      <a href="store_management.php">🏪 Store Management</a>
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
      <h1>Manager Dashboard</h1>
    </header>

    <!-- Summary Cards -->
    <div class="summary-cards">
      <div class="card"><h3>Products</h3><p><?= $totalProducts ?></p></div>
      <div class="card"><h3>Orders</h3><p><?= $totalOrders ?></p></div>
      <div class="card"><h3>Sales</h3><p>₱<?= number_format($totalSales, 2) ?></p></div>
      <div class="card warning"><h3>Low Stock</h3><p><?= $lowStock ?></p></div>
    </div>

    <!-- Graph Section -->
    <section class="charts-grid">
      <div class="chart-card">
        <h2>Top Selling Products</h2>
        <canvas id="salesChart"></canvas>
      </div>

      <div class="chart-card">
        <h2>Category Distribution</h2>
        <canvas id="categoryChart"></canvas>
      </div>

      <div class="chart-card">
        <h2>Monthly Sales Trend</h2>
        <canvas id="trendChart"></canvas>
      </div>
    </section>
  </main>
</div>

<script>
const ctx1 = document.getElementById('salesChart').getContext('2d');
new Chart(ctx1, {
  type: 'bar',
  data: {
    labels: <?= json_encode($names) ?>,
    datasets: [{
      data: <?= json_encode($sales) ?>,
      backgroundColor: '#d8a25a',
      borderRadius: 6
    }]
  },
  options: { plugins: { legend: { display: false }}, scales: { y: { beginAtZero: true } } }
});

const ctx2 = document.getElementById('categoryChart').getContext('2d');
new Chart(ctx2, {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($catLabels) ?>,
    datasets: [{
      data: <?= json_encode($catValues) ?>,
      backgroundColor: ['#c88f4a','#a15c38','#e2b15a','#744c24'],
      borderWidth: 1
    }]
  },
  options: { plugins: { legend: { position: 'bottom' } } }
});

const ctx3 = document.getElementById('trendChart').getContext('2d');
new Chart(ctx3, {
  type: 'line',
  data: {
    labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct'],
    datasets: [{
      label: 'Sales (₱)',
      data: [5000, 8000, 6500, 7200, 8900, 12000, 15000, 14000, 17000, 20000],
      borderColor: '#c88f4a',
      backgroundColor: 'rgba(200,143,74,0.2)',
      fill: true,
      tension: 0.4
    }]
  },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
</body>
</html>
