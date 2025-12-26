<?php
session_start();
if (!isset($_SESSION['parent_id'])) die("Login Required");
?>
<h2>Parent Dashboard</h2>
