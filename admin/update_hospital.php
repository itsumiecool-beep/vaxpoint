<?php
include("../config/db.php");

if ($_POST) {
    $sql = "UPDATE hospital SET hospital_name=?,address=?,location=?
            WHERE hospital_id=?";
    $conn->prepare($sql)->execute([
        $_POST['hospital_name'],
        $_POST['address'],
        $_POST['location'],
        $_POST['hospital_id']
    ]);
    echo "Hospital Updated";
}
?>
