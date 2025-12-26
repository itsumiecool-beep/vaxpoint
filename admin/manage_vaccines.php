<?php
include("../config/db.php");

if ($_POST) {
    $conn->prepare("INSERT INTO vaccine (vaccine_name,description)
                   VALUES (?,?)")
         ->execute([$_POST['name'], $_POST['description']]);
}

$vaccines = $conn->query("SELECT * FROM vaccine")->fetchAll();
?>
