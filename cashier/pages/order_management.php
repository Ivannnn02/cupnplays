<?php
session_start();
include('../php/connect.php');

// Restrict access to managers only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
  header('Location: login.php');
  exit;
}

// Fetch orders from SQL
$query = "SELECT id, order_no, total, date, cashier_name FROM orders ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Management - Cup N' Play</title>
  <link rel="stylesheet" href="../css/order.css">
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
      <a href="store_management.php">🏪 Store Management</a>
      <a href="#" class="active">🧾 Order Management</a>
    </nav>
    <div class="user-info">
      <img src="../images/manager.png" alt="Manager">
      <p><?= htmlspecialchars($_SESSION['name']) ?></p>
      <a href="../php/logout.php" class="logout">Logout</a>
    </div>
  </aside>

  <main class="content">
    <header>
      <h1>Order Management</h1>
      <input type="text" id="searchOrder" placeholder="Search orders...">
    </header>

    <div class="table-container">
      <table id="orderTable">
        <thead>
          <tr>
            <th>Order No.</th>
            <th>Total (₱)</th>
            <th>Date/Time</th>
            
            <th>Cashier</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= number_format($row['total'], 2) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                
                <td><?= htmlspecialchars($row['cashier_name']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No orders found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<script>
// Simple search filter
document.getElementById("searchOrder").addEventListener("input", e => {
  const term = e.target.value.toLowerCase();
  document.querySelectorAll("#orderTable tbody tr").forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(term) ? "" : "none";
  });
});
</script>
</body>
</html>
