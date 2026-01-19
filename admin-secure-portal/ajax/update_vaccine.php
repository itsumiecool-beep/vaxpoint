<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn('admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$vaccine_id = intval($_POST['vaccine_id'] ?? 0);
$availability = $_POST['availability'] ?? '';

if ($vaccine_id <= 0 || !in_array($availability, ['Available', 'Unavailable'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("UPDATE vaccine SET availability = ? WHERE vaccine_id = ?");
    
    if ($stmt->execute([$availability, $vaccine_id])) {
        echo json_encode(['success' => true, 'message' => 'Vaccine availability updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update vaccine']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>