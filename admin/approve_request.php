<?php
include("../config/db.php");

$request_id = $_GET['id'];

$sql = "UPDATE request SET request_status = 'Approved'
        WHERE request_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$request_id]);

echo "Request approved";
?>
