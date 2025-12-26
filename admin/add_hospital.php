<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['hospital_name'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO hospital (hospital_name, address, email, password)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $address, $email, $password]);

    echo "Hospital added successfully";
}
?>
