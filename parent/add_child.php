<?php
session_start();
include("../config/db.php");

$parent_id = $_SESSION['parent_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['child_name'];
    $dob = $_POST['date_of_birth'];
    $gender = $_POST['gender'];

    $sql = "INSERT INTO child (parent_id, child_name, gender, date_of_birth)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$parent_id, $name, $gender, $dob]);

    echo "Child added successfully";
}
?>
