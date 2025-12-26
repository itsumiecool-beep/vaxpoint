<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    header("Location: ../auth/parent_login.php");
    exit();
}
?>
