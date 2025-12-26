<?php
include("../config/db.php");

/* Child-wise */
$childWise = $conn->query(
    "SELECT c.child_name, v.vaccine_name, vr.vaccination_status
     FROM vaccination_record vr
     JOIN child c ON vr.child_id=c.child_id
     JOIN vaccine v ON vr.vaccine_id=v.vaccine_id"
)->fetchAll();
?>
