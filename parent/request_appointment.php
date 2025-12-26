<?php
session_start();
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_id = $_SESSION['parent_id'];
    $child_id = $_POST['child_id'];
    $vaccine_id = $_POST['vaccine_id'];
    $hospital_id = $_POST['hospital_id'];
    $date = $_POST['requested_date'];

    $sql = "INSERT INTO request 
            (parent_id, child_id, vaccine_id, hospital_id, requested_date)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $parent_id, $child_id, $vaccine_id, $hospital_id, $date
    ]);

    echo "Vaccination request submitted";
}
?>
