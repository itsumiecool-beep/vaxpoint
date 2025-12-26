<?php
include("../config/db.php");

$sql = "SELECT r.*, p.name AS parent_name, c.child_name, v.vaccine_name
        FROM request r
        JOIN parent p ON r.parent_id = p.parent_id
        JOIN child c ON r.child_id = c.child_id
        JOIN vaccine v ON r.vaccine_id = v.vaccine_id";

$stmt = $conn->query($sql);
$requests = $stmt->fetchAll();
?>
