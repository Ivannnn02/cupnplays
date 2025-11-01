<?php
// pages/sales.php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sales - Cup N' Play</title>
  <link rel="stylesheet" href="../css/style.css">
  <script>
    async function fetchSales(){
      const res = await fetch('../php/get_sales.php');
      const data = await res.json();
      const tbody = document.getElementById('sales-body');
      tbody.innerHTML = '';
      data.forEach(s=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${s.order_no}</td><td>${s.items || ''}</td><td>₱ ${parseFloat(s.total).toFixed(2)}</td><td>${s.cashier_name}</td><td>${s.date}</td>`;
        tbody.appendChild(tr);
      });
    }
    document.addEventListener('DOMContentLoaded', fetchSales);
  </script>
</head>
<body>
<div class="app">
  <div class="sidebar">
    <div class="logo">CnP</div>
    <button class="side-btn" onclick="location.href='dashboard_manager.php'">🏠</button>
    <button class="side-btn" onclick="location.href='stock.php'">📦</button>
    <button class="side-btn" onclick="location.href='../php/logout.php'">⎋</button>
  </div>

  <div class="main">
    <div class="header">
      <h1>Sales</h1>
      <div class="user"><?=htmlspecialchars($_SESSION['name'])?></div>
    </div>

    <div class="card">
      <h3>Recent Orders</h3>
      <table class="table">
        <thead><tr><th>Order #</th><th>Items</th><th>Total</th><th>Cashier</th><th>Date</th></tr></thead>
        <tbody id="sales-body"></tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
