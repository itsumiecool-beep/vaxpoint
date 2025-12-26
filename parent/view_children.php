<?php
session_start();
include("../config/db.php");

$stmt = $conn->prepare("SELECT * FROM child WHERE parent_id=?");
$stmt->execute([$_SESSION['parent_id']]);
$children = $stmt->fetchAll();
?>
