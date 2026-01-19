<?php
/**
 * Global Configuration Settings
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', 'http://localhost/vaxpoint');

// Site settings
define('SITE_NAME', 'VaxPoint');
define('SITE_TAGLINE', 'Smart E-Vaccination Management System');

// Upload settings
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']);
define('MAX_FILE_SIZE', 5242880); // 5MB

// Email settings (configure with your SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-password');
define('FROM_EMAIL', 'noreply@vaxpoint.com');
define('FROM_NAME', 'VaxPoint System');

// Security settings
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_TIMEOUT', 3600); // 1 hour

// Pagination
define('RECORDS_PER_PAGE', 10);

// Date format
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd M, Y');

// Timezone
date_default_timezone_set('Asia/Karachi');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once BASE_PATH . '/utils/functions.php';
require_once BASE_PATH . '/utils/validation.php';
require_once BASE_PATH . '/config/database.php';
?>