<?php
session_start();
include("../config/db.php");

if ($_POST) {
    $stmt = $conn->prepare("SELECT * FROM hospital WHERE email=?");
    $stmt->execute([$_POST['email']]);
    $hospital = $stmt->fetch();

    if ($hospital && password_verify($_POST['password'], $hospital['password'])) {
        $_SESSION['hospital_id'] = $hospital['hospital_id'];
        header("Location: ../hospital/dashboard.php");
    } else {
        echo "Invalid Hospital Login";
    }
}
?>
