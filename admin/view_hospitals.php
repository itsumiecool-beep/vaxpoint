<?php
include("../config/db.php");
$hospitals = $conn->query("SELECT * FROM hospital")->fetchAll();
?>

