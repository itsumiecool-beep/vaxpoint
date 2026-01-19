<?php
/**
 * Helper Functions
 * Common utility functions used across the application
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Check if user is logged in
 */
function isLoggedIn($role = null) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        return false;
    }
    
    if ($role && $_SESSION['role'] !== $role) {
        return false;
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Redirect to a specific page
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Format date for display
 */
function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    if (empty($date) || $date === '0000-00-00') {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

/**
 * Calculate age from date of birth
 */
function calculateAge($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    $age = $birthDate->diff($today);
    
    if ($age->y > 0) {
        return $age->y . ' year' . ($age->y > 1 ? 's' : '');
    } elseif ($age->m > 0) {
        return $age->m . ' month' . ($age->m > 1 ? 's' : '');
    } else {
        return $age->d . ' day' . ($age->d > 1 ? 's' : '');
    }
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Upload file
 */
function uploadFile($file, $destination, $allowed_types = ALLOWED_IMAGE_TYPES) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds limit'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $destination . $filename;
    
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

/**
 * Get vaccination status badge
 */
function getStatusBadge($status) {
    $badges = [
        'Pending' => '<span class="badge badge-warning">Pending</span>',
        'Approved' => '<span class="badge badge-success">Approved</span>',
        'Rejected' => '<span class="badge badge-danger">Rejected</span>',
        'Completed' => '<span class="badge badge-info">Completed</span>',
        'Vaccinated' => '<span class="badge badge-success">Vaccinated</span>',
        'Not Vaccinated' => '<span class="badge badge-danger">Not Vaccinated</span>',
        'Available' => '<span class="badge badge-success">Available</span>',
        'Unavailable' => '<span class="badge badge-danger">Unavailable</span>',
        'Active' => '<span class="badge badge-success">Active</span>',
        'Inactive' => '<span class="badge badge-secondary">Inactive</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge badge-secondary">' . $status . '</span>';
}

/**
 * Paginate results
 */
function paginate($total_records, $current_page, $records_per_page = RECORDS_PER_PAGE) {
    $total_pages = ceil($total_records / $records_per_page);
    $offset = ($current_page - 1) * $records_per_page;
    
    return [
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'offset' => $offset,
        'limit' => $records_per_page
    ];
}

/**
 * Generate pagination HTML
 */
function renderPagination($total_pages, $current_page, $base_url) {
    if ($total_pages <= 1) return '';
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous button
    $disabled = $current_page <= 1 ? 'disabled' : '';
    $prev_page = max(1, $current_page - 1);
    $html .= '<li class="page-item ' . $disabled . '"><a class="page-link" href="' . $base_url . '&page=' . $prev_page . '">Previous</a></li>';
    
    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = $i == $current_page ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $base_url . '&page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Next button
    $disabled = $current_page >= $total_pages ? 'disabled' : '';
    $next_page = min($total_pages, $current_page + 1);
    $html .= '<li class="page-item ' . $disabled . '"><a class="page-link" href="' . $base_url . '&page=' . $next_page . '">Next</a></li>';
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Send JSON response
 */
function jsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}
?>