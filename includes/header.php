<!DOCTYPE html>
<html>
<head>
    <title>VaxPoint</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <!-- LOGO SECTION -->
    <div class="nav-left">
    <img src="images/vaxpointlogo.png" class="logo-img" alt="VaxPoint Logo">



        <span class="logo-text">VaxPoint</span>
    </div>

    <!-- MENU -->
    <div class="nav-links" id="navLinks">
        <a href="index.php">Home</a>
        <a href="auth/parent_login.php">Parent</a>
        <a href="auth/hospital_login.php">Hospital</a>
        <a href="auth/admin_login.php">Admin</a>
    </div>

    <!-- MOBILE ICON -->
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>

    <!-- FLOATING MOON -->
    <div class="moon"></div>
</nav>

<script>
function toggleMenu() {
    const nav = document.getElementById("navLinks");
    nav.style.display = nav.style.display === "flex" ? "none" : "flex";
}
</script>
