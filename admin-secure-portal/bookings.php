<?php
require_once '../config/config.php';

$page_title = 'Booking Management';

$db = new Database();
$conn = $db->getConnection();

// Get all bookings
$stmt = $conn->query("
    SELECT b.*, 
           p.name as parent_name, p.email as parent_email,
           c.child_name, c.gender, c.date_of_birth,
           h.hospital_name, h.location,
           v.vaccine_name
    FROM booking b
    JOIN parent p ON b.parent_id = p.parent_id
    JOIN child c ON b.child_id = c.child_id
    JOIN hospital h ON b.hospital_id = h.hospital_id
    JOIN vaccine v ON b.vaccine_id = v.vaccine_id
    ORDER BY b.booking_date DESC, b.appointment_time DESC
");
$bookings = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Vaccination Bookings</h3>
        <div>
            <span class="badge badge-warning">Pending: <?php echo count(array_filter($bookings, fn($b) => $b['status'] === 'Pending')); ?></span>
            <span class="badge badge-success">Approved: <?php echo count(array_filter($bookings, fn($b) => $b['status'] === 'Approved')); ?></span>
            <span class="badge badge-info">Completed: <?php echo count(array_filter($bookings, fn($b) => $b['status'] === 'Completed')); ?></span>
        </div>
    </div>
    
    <?php if (count($bookings) > 0): ?>
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
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><strong>#<?php echo $booking['booking_id']; ?></strong></td>
                            <td>
                                <strong><?php echo htmlspecialchars($booking['parent_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($booking['parent_email']); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($booking['child_name']); ?></strong><br>
                                <small><?php echo $booking['gender']; ?> | <?php echo calculateAge($booking['date_of_birth']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($booking['vaccine_name']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($booking['hospital_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($booking['location']); ?></small>
                            </td>
                            <td><?php echo formatDate($booking['booking_date']); ?></td>
                            <td><?php echo date('h:i A', strtotime($booking['appointment_time'])); ?></td>
                            <td><?php echo getStatusBadge($booking['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #64748b; padding: 40px;">No bookings found</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>