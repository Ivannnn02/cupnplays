<?php
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] === 'cashier') {
    header('Location: dashboard_cashier.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cup N' Play - Cashier Login</title>
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>
  <div class="login-wrapper">

    <!-- Left image section -->
    <div class="image-side">
    </div>

    <!-- Right login form -->
    <div class="form-side">
      <div class="form-box">
        <a href="home.php" class="back-btn">←</a>

        <div class="logo">
          <img src="../images/cnplogo.png" alt="Cup N' Play Logo">
          <h2>Cup N' Play</h2>
        </div>

        <h3>Welcome back!</h3>
        <p class="subtitle">Please log in to continue</p>

        <form action="../php/auth.php" method="POST">
          <div class="input-group">
            <label for="employee_id">Employee ID</label>
            <input type="text" id="employee_id" name="employee_id" placeholder="Enter your Employee ID" required>
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
          </div>

          <div class="remember">
            <label><input type="checkbox"> Remember me</label>
            
          </div>

          <button type="submit" class="btn-login">Log In</button>
        </form>

        <footer>
          <p>© 2025 Cup N' Play. All rights reserved.</p>
        </footer>
      </div>
    </div>

  </div>
</body>
</html>
