<?php
session_start();
include("../config/db.php");

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE email=?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        header("Location: ../admin/dashboard.php");
    } else {
        echo "Invalid Admin Login";
    }
}
?>
