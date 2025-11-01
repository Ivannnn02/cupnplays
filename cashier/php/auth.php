<?php
session_start();
include '../php/connect.php'; // Database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = trim($_POST['employee_id']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($employee_id) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit;
    }

    // Query database
    $stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // ✅ Compare plain passwords
        if ($password === $row['password']) {
            $_SESSION['employee_id'] = $row['employee_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            if ($row['role'] === 'manager') {
                header("Location: ../pages/dashboard_manager.php");
            } elseif ($row['role'] === 'cashier') {
                header("Location: ../pages/dashboard_cashier.php");
            } else {
                header("Location: ../pages/dashboard.php");
            }
            exit;
        } else {
            echo "<script>alert('Incorrect password.'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Employee not found.'); window.history.back();</script>";
        exit;
    }
}
?>
