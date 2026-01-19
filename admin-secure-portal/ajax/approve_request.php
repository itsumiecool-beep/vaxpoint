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

$request_id = intval($_POST['request_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($request_id <= 0 || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get request details
    $stmt = $conn->prepare("SELECT * FROM request WHERE request_id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();
    
    if (!$request) {
        echo json_encode(['success' => false, 'message' => 'Request not found']);
        exit();
    }
    
    if ($action === 'approve') {
        // Update request status
        $stmt = $conn->prepare("UPDATE request SET request_status = 'Approved' WHERE request_id = ?");
        $stmt->execute([$request_id]);
        
        // Create booking
        $stmt = $conn->prepare("
            INSERT INTO booking (parent_id, child_id, hospital_id, vaccine_id, booking_date, appointment_time, status)
            VALUES (?, ?, ?, ?, ?, ?, 'Approved')
        ");
        $stmt->execute([
            $request['parent_id'],
            $request['child_id'],
            $request['hospital_id'],
            $request['vaccine_id'],
            $request['requested_date'],
            $request['requested_time'] ?? '09:00:00'
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Request approved and booking created']);
    } else {
        // Reject request
        $stmt = $conn->prepare("UPDATE request SET request_status = 'Rejected' WHERE request_id = ?");
        $stmt->execute([$request_id]);
        
        echo json_encode(['success' => true, 'message' => 'Request rejected']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>