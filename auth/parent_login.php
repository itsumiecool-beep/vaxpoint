<?php
session_start();
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM parent WHERE email = ?");
    $stmt->execute([$email]);
    $parent = $stmt->fetch();

    if ($parent && password_verify($password, $parent['password'])) {
        $_SESSION['parent_id'] = $parent['parent_id'];
        header("Location: ../parent/dashboard.php");
    } else {
        echo "Invalid login details";
    }
}
?>
