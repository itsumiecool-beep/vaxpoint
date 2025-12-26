<?php
session_start();
if (!isset($_SESSION['admin_id'])) die("Access Denied");
?>
<h2>Admin Dashboard</h2>
