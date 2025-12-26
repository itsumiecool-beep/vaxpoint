<?php
include("../config/db.php");
$conn->prepare("DELETE FROM hospital WHERE hospital_id=?")
     ->execute([$_GET['id']]);
echo "Hospital Deleted";
?>
