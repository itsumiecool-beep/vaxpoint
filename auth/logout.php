<?php
session_start();

// Determine redirect location based on role
$redirect_url = '../index.php';

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            $redirect_url = '../admin-secure-portal/login.php';
            break;
        case 'parent':
            $redirect_url = '../parent/login.php';
            break;
        case 'hospital':
            $redirect_url = '../hospital/login.php';
            break;
    }
}

// Clear all session data
session_unset();
session_destroy();

// Redirect to appropriate login page
header("Location: " . $redirect_url);
exit();
?>