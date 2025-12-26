<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO parent (name, email, password)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $email, $password]);

    echo "Parent registered successfully!";
}
?>
