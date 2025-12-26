<?php
session_start();
include("../config/db.php");

$sql = "SELECT c.child_name, v.vaccine_name, vr.vaccination_status
        FROM vaccination_record vr
        JOIN child c ON vr.child_id=c.child_id
        JOIN vaccine v ON vr.vaccine_id=v.vaccine_id
        WHERE c.parent_id=?";

$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION['parent_id']]);
$reports = $stmt->fetchAll();
?>
