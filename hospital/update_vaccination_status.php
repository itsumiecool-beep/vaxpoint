<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    $sql = "INSERT INTO vaccination_record
            (appointment_id, vaccination_status, remarks)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$appointment_id, $status, $remarks]);

    echo "Vaccination record updated";
}
?>
