<?php
/**
 * Form Validation Functions
 * Server-side validation for all forms
 */

/**
 * Validate email
 */
function validateEmail($email) {
    if (empty($email)) {
        return ['valid' => false, 'message' => 'Email is required'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Invalid email format'];
    }
    
    return ['valid' => true];
}

/**
 * Validate password
 */
function validatePassword($password) {
    if (empty($password)) {
        return ['valid' => false, 'message' => 'Password is required'];
    }
    
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return ['valid' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one uppercase letter'];
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one lowercase letter'];
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one number'];
    }
    
    return ['valid' => true];
}

/**
 * Validate phone number
 */
function validatePhone($phone) {
    if (empty($phone)) {
        return ['valid' => false, 'message' => 'Phone number is required'];
    }
    
    // Pakistani phone format: 03xxxxxxxxx or +923xxxxxxxxx
    if (!preg_match('/^(03[0-9]{9}|\+923[0-9]{9})$/', $phone)) {
        return ['valid' => false, 'message' => 'Invalid phone number format (e.g., 03001234567)'];
    }
    
    return ['valid' => true];
}

/**
 * Validate required field
 */
function validateRequired($value, $field_name) {
    if (empty(trim($value))) {
        return ['valid' => false, 'message' => $field_name . ' is required'];
    }
    
    return ['valid' => true];
}

/**
 * Validate date
 */
function validateDate($date, $field_name = 'Date') {
    if (empty($date)) {
        return ['valid' => false, 'message' => $field_name . ' is required'];
    }
    
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) {
        return ['valid' => false, 'message' => 'Invalid date format'];
    }
    
    return ['valid' => true];
}

/**
 * Validate date of birth (must be in past)
 */
function validateDOB($dob) {
    $result = validateDate($dob, 'Date of birth');
    if (!$result['valid']) {
        return $result;
    }
    
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    
    if ($birthDate >= $today) {
        return ['valid' => false, 'message' => 'Date of birth must be in the past'];
    }
    
    // Check if age is reasonable (not more than 18 years for child vaccination)
    $age = $birthDate->diff($today)->y;
    if ($age > 18) {
        return ['valid' => false, 'message' => 'Child must be under 18 years old'];
    }
    
    return ['valid' => true];
}

/**
 * Validate future date
 */
function validateFutureDate($date, $field_name = 'Date') {
    $result = validateDate($date, $field_name);
    if (!$result['valid']) {
        return $result;
    }
    
    $selectedDate = new DateTime($date);
    $today = new DateTime('today');
    
    if ($selectedDate < $today) {
        return ['valid' => false, 'message' => $field_name . ' must be in the future'];
    }
    
    return ['valid' => true];
}

/**
 * Validate blood group
 */
function validateBloodGroup($blood_group) {
    $valid_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    
    if (empty($blood_group)) {
        return ['valid' => false, 'message' => 'Blood group is required'];
    }
    
    if (!in_array(strtoupper($blood_group), $valid_groups)) {
        return ['valid' => false, 'message' => 'Invalid blood group'];
    }
    
    return ['valid' => true];
}

/**
 * Validate gender
 */
function validateGender($gender) {
    $valid_genders = ['Male', 'Female', 'Other'];
    
    if (empty($gender)) {
        return ['valid' => false, 'message' => 'Gender is required'];
    }
    
    if (!in_array($gender, $valid_genders)) {
        return ['valid' => false, 'message' => 'Invalid gender'];
    }
    
    return ['valid' => true];
}

/**
 * Validate time format
 */
function validateTime($time) {
    if (empty($time)) {
        return ['valid' => false, 'message' => 'Time is required'];
    }
    
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
        return ['valid' => false, 'message' => 'Invalid time format (HH:MM)'];
    }
    
    return ['valid' => true];
}

/**
 * Sanitize and validate registration data
 */
function validateRegistration($data, $type = 'parent') {
    $errors = [];
    
    // Name validation
    $name_result = validateRequired($data['name'] ?? '', 'Name');
    if (!$name_result['valid']) {
        $errors[] = $name_result['message'];
    }
    
    // Email validation
    $email_result = validateEmail($data['email'] ?? '');
    if (!$email_result['valid']) {
        $errors[] = $email_result['message'];
    }
    
    // Password validation
    $password_result = validatePassword($data['password'] ?? '');
    if (!$password_result['valid']) {
        $errors[] = $password_result['message'];
    }
    
    // Confirm password
    if (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) {
        $errors[] = 'Passwords do not match';
    }
    
    // Phone validation
    if ($type === 'parent' || $type === 'hospital') {
        $phone_result = validatePhone($data['phone'] ?? '');
        if (!$phone_result['valid']) {
            $errors[] = $phone_result['message'];
        }
    }
    
    // Address validation
    $address_result = validateRequired($data['address'] ?? '', 'Address');
    if (!$address_result['valid']) {
        $errors[] = $address_result['message'];
    }
    
    // Hospital specific
    if ($type === 'hospital') {
        $location_result = validateRequired($data['location'] ?? '', 'Location');
        if (!$location_result['valid']) {
            $errors[] = $location_result['message'];
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}
?>