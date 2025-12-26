<?php
session_start();
if (!isset($_SESSION['hospital_id'])) die("Login Required");
?>
<h2>Hospital Dashboard</h2>
