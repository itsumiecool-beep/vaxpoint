<?php
session_start();

// Sabhi session variables clear karo
$_SESSION = [];

// Session destroy karo
session_destroy();

// Browser cache disable (security ke liye)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Login selection page par redirect
header("Location: ../index.php");
exit();
?>

