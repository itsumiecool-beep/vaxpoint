<?php
require_once '../config/config.php';

$page_title = 'Dashboard';

// Get statistics
$db = new Database();
$conn = $db->getConnection();

// Total children
$stmt = $conn->query("SELECT COUNT(*) as total FROM child");
$total_children = $stmt->fetch()['total'];

// Total parents
$stmt = $conn->query("SELECT COUNT(*) as total FROM parent");
$total_parents = $stmt->fetch()['total'];

// Total hospitals
$stmt = $conn->query("SELECT COUNT(*) as total FROM hospital WHERE status = 'Active'");
$total_hospitals = $stmt->fetch()['total'];

// Pending requests
$stmt = $conn->query("SELECT COUNT(*) as total FROM request WHERE request_status = 'Pending'");
$pending_requests = $stmt->fetch()['total'];

// Total bookings
$stmt = $conn->query("SELECT COUNT(*) as total FROM booking");
$total_bookings = $stmt->fetch()['total'];

// Upcoming vaccinations
$stmt = $conn->query("SELECT COUNT(*) as total FROM vaccination_schedule WHERE scheduled_date >= CURDATE()");
$upcoming_vaccinations = $stmt->fetch()['total'];

// Recent bookings
$stmt = $conn->query("
    SELECT b.*, p.name as parent_name, c.child_name, h.hospital_name, v.vaccine_name
    FROM booking b
    JOIN parent p ON b.parent_id = p.parent_id
    JOIN child c ON b.child_id = c.child_id
    JOIN hospital h ON b.hospital_id = h.hospital_id
    JOIN vaccine v ON b.vaccine_id = v.vaccine_id
    ORDER BY b.booking_date DESC
    LIMIT 5
");
$recent_bookings = $stmt->fetchAll();

// Recent requests
$stmt = $conn->query("
    SELECT r.*, p.name as parent_name, c.child_name, h.hospital_name, v.vaccine_name
    FROM request r
    JOIN parent p ON r.parent_id = p.parent_id
    JOIN child c ON r.child_id = c.child_id
    JOIN hospital h ON r.hospital_id = h.hospital_id
    JOIN vaccine v ON r.vaccine_id = v.vaccine_id
    WHERE r.request_status = 'Pending'
    ORDER BY r.created_at DESC
    LIMIT 5
");
$recent_requests = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card success">
        <div class="stat-header">
            <div>
                <div class="stat-value"><?php echo $total_children; ?></div>
                <div class="stat-label">Total Children</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-baby"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-value"><?php echo $total_parents; ?></div>
                <div class="stat-label">Registered Parents</div>
            </div>
            <div class="stat-icon" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-header">
            <div>
                <div class="stat-value"><?php echo $pending_requests; ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card danger">
        <div class="stat-header">
            <div>
                <div class="stat-value"><?php echo $total_hospitals; ?></div>
                <div class="stat-label">Active Hospitals</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-hospital"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pending Requests</h3>
        <a href="requests.php" class="btn btn-primary btn-sm">View All</a>
    </div>
    
    <?php if (count($recent_requests) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Parent</th>
                        <th>Child</th>
                        <th>Vaccine</th>
                        <th>Hospital</th>
                        <th>Requested Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['parent_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['child_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['vaccine_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['hospital_name']); ?></td>
                            <td><?php echo formatDate($request['requested_date']); ?></td>
                            <td><?php echo getStatusBadge($request['request_status']); ?></td>
                            <td>
                                <button class="btn btn-success btn-sm" onclick="approveRequest(<?php echo $request['request_id']; ?>)">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="rejectRequest(<?php echo $request['request_id']; ?>)">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #64748b; padding: 20px;">No pending requests</p>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Recent Bookings</h3>
        <a href="bookings.php" class="btn btn-primary btn-sm">View All</a>
    </div>
    
    <?php if (count($recent_bookings) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Parent</th>
                        <th>Child</th>
                        <th>Vaccine</th>
                        <th>Hospital</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_bookings as $booking): ?>
                        <tr>
                            <td>#<?php echo $booking['booking_id']; ?></td>
                            <td><?php echo htmlspecialchars($booking['parent_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['child_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['vaccine_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['hospital_name']); ?></td>
                            <td><?php echo formatDate($booking['booking_date']); ?></td>
                            <td><?php echo date('h:i A', strtotime($booking['appointment_time'])); ?></td>
                            <td><?php echo getStatusBadge($booking['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #64748b; padding: 20px;">No recent bookings</p>
    <?php endif; ?>
</div>

<script>
function approveRequest(requestId) {
    if (confirm('Are you sure you want to approve this request?')) {
        fetch('ajax/approve_request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'request_id=' + requestId + '&action=approve'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Request approved successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function rejectRequest(requestId) {
    if (confirm('Are you sure you want to reject this request?')) {
        fetch('ajax/approve_request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'request_id=' + requestId + '&action=reject'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Request rejected successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>