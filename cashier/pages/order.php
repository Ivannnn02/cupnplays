<?php
// pages/order.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cashier') {
    header('Location: login.php');
    exit;
}
include('../php/connect.php');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Order - Cup N' Play</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/order.css"> <!-- ✅ added new CSS file -->
  <script defer src="../js/script.js"></script>
</head>
<body>
<div class="app">
  <!-- Sidebar -->
   <div class="sidebar">
    <div class="logo">CnP</div>
    <button class="side-btn active" onclick="location.href='order.php'">☕ Order</button>
    <button class="side-btn" data-toggle="cart">🛒 Cart</button>
    <button class="side-btn" onclick="location.href='../php/logout.php'">⎋ Logout</button>
  </div>
  <!-- Main Section -->
  <div class="main">
    <div class="header">
      <h1 id="menu-title">☕ Coffee Menu</h1>
      <div class="user">Cashier: <?= htmlspecialchars($_SESSION['name']) ?></div>
    </div>

    <!-- Category Buttons -->
    <div class="category-bar">
      <button class="cat-btn active" data-category="coffee">☕ Coffee</button>
      <button class="cat-btn" data-category="milk tea">🧋 Milk Tea</button>
    </div>

    <!-- Menu Items -->
    <div class="card">
      <div id="menu-grid" class="menu-grid">
        <!-- Products will be loaded here -->
      </div>
    </div>
  </div>

  <!-- Cart Panel -->
  <div id="cart-panel" class="cart-panel card">
    <div class="cart-header">
      <strong>My Order</strong>
      <small id="cart-total">₱ 0.00</small>
    </div>
    <div id="cart-items" class="cart-items"></div>
    <div class="cart-footer">
      <div style="display:flex;gap:8px">
        <button id="checkout-btn" style="flex:1;padding:10px;border-radius:8px;border:none;background:var(--accent);color:white">Checkout</button>
        <button onclick="location.reload()" style="padding:10px;border-radius:8px;border:none;background:#ddd">Clear</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const menuGrid = document.getElementById("menu-grid");
  const buttons = document.querySelectorAll(".cat-btn");
  const menuTitle = document.getElementById("menu-title");

  // Load products by category
  function loadProducts(category) {
    fetch(`../php/get_products.php?category=${category}`)
      .then(res => res.json())
      .then(data => {
        menuGrid.innerHTML = "";
        if (data.length === 0) {
          menuGrid.innerHTML = "<p>No products found.</p>";
          return;
        }
        data.forEach(item => {
          menuGrid.innerHTML += `
            <div class="menu-item">
              <img src="../images/${item.image}" alt="${item.name}">
              <h3>${item.name}</h3>
              <p>₱${item.price}</p>
              <button data-id="${item.id}" data-name="${item.name}" data-price="${item.price}" class="add-btn">Add to Cart</button>
            </div>`;
        });
      });
  }

  // Default: Coffee category
  loadProducts("coffee");

  // Switch category
  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      buttons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const category = btn.getAttribute("data-category");
      menuTitle.textContent = category === "coffee" ? "☕ Coffee Menu" : "🧋 Milk Tea Menu";
      loadProducts(category);
    });
  });
});
</script>
</body>
</html>
