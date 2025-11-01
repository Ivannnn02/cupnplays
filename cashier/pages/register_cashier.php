<?php
session_start();
include '../php/connect.php';

// Fetch latest employee ID
$result = $conn->query("SELECT employee_id FROM users ORDER BY id DESC LIMIT 1");
if ($result->num_rows > 0) {
    $lastId = $result->fetch_assoc()['employee_id'];
    $num = (int)substr($lastId, 3) + 1;
    $newId = 'EMP' . str_pad($num, 3, '0', STR_PAD_LEFT);
} else {
    $newId = 'EMP001';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $role = 'cashier';

    if (empty($name) || empty($password)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (employee_id, name, role, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $employee_id, $name, $role, $password);
        if ($stmt->execute()) {
            echo "<script>alert('Cashier Registered Successfully!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error registering employee.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register Cashier - Cup N' Play</title>
  <link rel="stylesheet" href="../css/register.css">
</head>
<body>
  <div class="register-wrap">
    <div class="register-card">
      <h2>Cashier Registration</h2>
      <form method="POST">
        <input type="text" name="employee_id" value="<?= $newId ?>" readonly>
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
        <a href="home.php" class="back">Back to Home</a>
      </form>
    </div>
  </div>
</body>
</html>
