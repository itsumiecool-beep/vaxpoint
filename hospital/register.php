<?php
include("../config/db.php");

if ($_POST) {
    $conn->prepare(
        "INSERT INTO hospital (hospital_name,address,location,email,password)
         VALUES (?,?,?,?,?)"
    )->execute([
        $_POST['hospital_name'],
        $_POST['address'],
        $_POST['location'],
        $_POST['email'],
        password_hash($_POST['password'], PASSWORD_DEFAULT)
    ]);
    echo "Hospital Registered";
}
?>
