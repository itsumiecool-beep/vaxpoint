<?php
require_once '../config/config.php';

$page_title = 'Parent Requests';

$db = new Database();
$conn = $db->getConnection();

// Get all requests with details
$stmt = $conn->query("
    SELECT r.*, 
           p.name as parent_name, p.email as parent_email, p.phone as parent_phone,
           c.child_name, c.gender, c.date_of_birth,
           h.hospital_name, h.location,
           v.vaccine_name
    FROM request r
    JOIN parent p ON r.parent_id = p.parent_id
    JOIN child c ON r.child_id = c.child_id
    JOIN hospital h ON r.hospital_id = h.hospital_id
    JOIN vaccine v ON r.vaccine_id = v.vaccine_id
    ORDER BY 
        CASE 
            WHEN r.request_status = 'Pending' THEN 1
            WHEN r.request_status = 'Approved' THEN 2
            WHEN r.request_status = 'Rejected' THEN 3
        END,
        r.created_at DESC
");
$requests = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Vaccination Requests</h3>
        <div>
            <span class="badge badge-warning">Pending: <?php echo count(array_filter($requests, fn($r) => $r['request_status'] === 'Pending')); ?></span>
            <span class="badge badge-success">Approved: <?php echo count(array_filter($requests, fn($r) => $r['request_status'] === 'Approved')); ?></span>
            <span class="badge badge-danger">Rejected: <?php echo count(array_filter($requests, fn($r) => $r['request_status'] === 'Rejected')); ?></span>
        </div>
    </div>
    
    <?php if (count($requests) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Parent Details</th>
                        <th>Child Details</th>
                        <th>Vaccine</th>
                        <th>Hospital</th>
                        <th>Requested Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><strong>#<?php echo $request['request_id']; ?></strong></td>
                            <td>
                                <strong><?php echo htmlspecialchars($request['parent_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($request['parent_email']); ?></small><br>
                                <small><?php echo htmlspecialchars($request['parent_phone']); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($request['child_name']); ?></strong><br>
                                <small><?php echo $request['gender']; ?> | <?php echo calculateAge($request['date_of_birth']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($request['vaccine_name']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($request['hospital_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($request['location']); ?></small>
                            </td>
                            <td><?php echo formatDate($request['requested_date']); ?></td>
                            <td><?php echo $request['requested_time'] ? date('h:i A', strtotime($request['requested_time'])) : 'Not specified'; ?></td>
                            <td><?php echo getStatusBadge($request['request_status']); ?></td>
                            <td>
                                <?php if ($request['request_status'] === 'Pending'): ?>
                                    <button class="btn btn-success btn-sm" onclick="handleRequest(<?php echo $request['request_id']; ?>, 'approve')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="handleRequest(<?php echo $request['request_id']; ?>, 'reject')">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                <?php else: ?>
                                    <span class="badge badge-info">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #64748b; padding: 40px;">No requests found</p>
    <?php endif; ?>
</div>

<script>
function handleRequest(requestId, action) {
    const actionText = action === 'approve' ? 'approve' : 'reject';
    
    if (confirm(`Are you sure you want to ${actionText} this request?`)) {
        fetch('ajax/approve_request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `request_id=${requestId}&action=${action}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
            console.error('Error:', error);
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>